import maplibregl from 'maplibre-gl';
import { Protocol } from 'pmtiles';

/* ============================================================
   RAPIDA Map — Alpine.js MapLibre GL Controller
   Tech Spec 06 — Building Footprints + Pin Clusters
   ============================================================ */

// Register PMTiles protocol once globally
const protocol = new Protocol();
maplibregl.addProtocol('pmtiles', protocol.tile);

/**
 * Color tokens for damage levels and map features.
 */
const DEFAULT_TOKENS = {
    damage_minimal: '#22C55E',
    damage_partial: '#F59E0B',
    damage_complete: '#EF4444',
    footprint_fill: '#2563EB',
    footprint_stroke: '#1A2E4A',
    user_dot: '#2563EB',
};

/**
 * Returns the color for a given damage level string.
 */
function damageColor(level, tokens) {
    const map = {
        minimal: tokens.damage_minimal,
        partial: tokens.damage_partial,
        complete: tokens.damage_complete,
    };
    return map[level] || tokens.footprint_fill;
}

/**
 * rapidaMap — Alpine.js data component for MapLibre GL.
 *
 * @param {Object} config
 * @param {string} config.crisisSlug
 * @param {'reporter'|'dashboard'} config.mode
 * @param {[number, number]} config.center — [lng, lat]
 * @param {number} config.zoom
 * @param {Object} config.tokens — color tokens
 * @param {string} config.buildingsUrl — API endpoint for building footprints
 * @param {string} config.pinsUrl — API endpoint for map pins
 */
function rapidaMap(config = {}) {
    return {
        map: null,
        userMarker: null,
        watchId: null,
        pollTimer: null,
        lastPinTimestamp: null,

        config: {
            crisisSlug: config.crisisSlug || '',
            mode: config.mode || 'reporter',
            center: config.center || [-0.20, 5.56],
            zoom: config.zoom || 14,
            tokens: { ...DEFAULT_TOKENS, ...(config.tokens || {}) },
            buildingsUrl: config.buildingsUrl || '',
            pinsUrl: config.pinsUrl || '',
        },

        init() {
            this.$nextTick(() => {
                this._createMap();
            });
        },

        destroy() {
            if (this.watchId !== null) {
                navigator.geolocation.clearWatch(this.watchId);
            }
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
            }
            if (this.map) {
                this.map.remove();
            }
        },

        /* ----------------------------------------------------------
           Map creation
           ---------------------------------------------------------- */
        _createMap() {
            const el = this.$el || document.getElementById('rapida-map');
            if (!el) {
                return;
            }

            this.map = new maplibregl.Map({
                container: el,
                style: {
                    version: 8,
                    sources: {
                        'osm-raster': {
                            type: 'raster',
                            tiles: [
                                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                            ],
                            tileSize: 256,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        },
                    },
                    layers: [
                        {
                            id: 'osm-raster-layer',
                            type: 'raster',
                            source: 'osm-raster',
                            minzoom: 0,
                            maxzoom: 19,
                        },
                    ],
                },
                center: this.config.center,
                zoom: this.config.zoom,
                attributionControl: true,
            });

            this.map.addControl(new maplibregl.NavigationControl(), 'top-right');

            this.map.on('load', () => {
                this._addBuildingsLayer();
                this._addPinsLayer();
                this._setupBuildingTapHandler();
                this._setupTapAnywhereHandler();

                if (this.config.mode === 'reporter') {
                    this._startGpsTracking();
                }

                if (this.config.mode === 'dashboard') {
                    this._startPinPolling();
                }
            });
        },

        /* ----------------------------------------------------------
           Buildings GeoJSON layer (fill + stroke)
           ---------------------------------------------------------- */
        _addBuildingsLayer() {
            const tokens = this.config.tokens;

            // Guard: don't re-add if source already exists
            if (this.map.getSource('buildings')) {
                return;
            }

            this.map.addSource('buildings', {
                type: 'geojson',
                data: { type: 'FeatureCollection', features: [] },
            });

            // Fill layer — colored by damage level
            this.map.addLayer({
                id: 'buildings-fill',
                type: 'fill',
                source: 'buildings',
                paint: {
                    'fill-color': [
                        'match',
                        ['get', 'canonical_damage_level'],
                        'minimal', tokens.damage_minimal,
                        'partial', tokens.damage_partial,
                        'complete', tokens.damage_complete,
                        tokens.footprint_fill, // default
                    ],
                    'fill-opacity': 0.45,
                },
            });

            // Stroke layer
            this.map.addLayer({
                id: 'buildings-stroke',
                type: 'line',
                source: 'buildings',
                paint: {
                    'line-color': tokens.footprint_stroke,
                    'line-width': 1.5,
                },
            });

            // Fetch buildings
            this._fetchBuildings();

            // Reload buildings when map moves
            this.map.on('moveend', () => {
                this._fetchBuildings();
            });
        },

        async _fetchBuildings() {
            if (!this.config.buildingsUrl) {
                return;
            }

            try {
                const bounds = this.map.getBounds();
                const bbox = [
                    bounds.getWest(),
                    bounds.getSouth(),
                    bounds.getEast(),
                    bounds.getNorth(),
                ].join(',');

                const url = `${this.config.buildingsUrl}?bbox=${bbox}`;
                const response = await fetch(url);

                if (!response.ok) {
                    return;
                }

                const geojson = await response.json();
                const source = this.map.getSource('buildings');

                if (source) {
                    source.setData(geojson);
                }
            } catch (e) {
                console.warn('[rapida-map] Failed to fetch buildings:', e.message);
            }
        },

        /* ----------------------------------------------------------
           Pins GeoJSON layer (clustered circles)
           ---------------------------------------------------------- */
        _addPinsLayer() {
            const tokens = this.config.tokens;

            // Guard: don't re-add if source already exists
            if (this.map.getSource('pins')) {
                return;
            }

            this.map.addSource('pins', {
                type: 'geojson',
                data: { type: 'FeatureCollection', features: [] },
                cluster: true,
                clusterMaxZoom: 14,
                clusterRadius: 50,
            });

            // Cluster circles
            this.map.addLayer({
                id: 'pin-clusters',
                type: 'circle',
                source: 'pins',
                filter: ['has', 'point_count'],
                paint: {
                    'circle-color': [
                        'step',
                        ['get', 'point_count'],
                        tokens.damage_minimal,
                        10, tokens.damage_partial,
                        50, tokens.damage_complete,
                    ],
                    'circle-radius': [
                        'step',
                        ['get', 'point_count'],
                        18,
                        10, 24,
                        50, 32,
                    ],
                    'circle-stroke-width': 2,
                    'circle-stroke-color': '#ffffff',
                },
            });

            // Cluster count labels
            this.map.addLayer({
                id: 'pin-cluster-count',
                type: 'symbol',
                source: 'pins',
                filter: ['has', 'point_count'],
                layout: {
                    'text-field': '{point_count_abbreviated}',
                    'text-size': 13,
                    'text-font': ['Open Sans Bold', 'Arial Unicode MS Bold'],
                },
                paint: {
                    'text-color': '#ffffff',
                },
            });

            // Individual pin circles — colored by damage level
            this.map.addLayer({
                id: 'pin-unclustered',
                type: 'circle',
                source: 'pins',
                filter: ['!', ['has', 'point_count']],
                paint: {
                    'circle-color': [
                        'match',
                        ['get', 'damage_level'],
                        'minimal', tokens.damage_minimal,
                        'partial', tokens.damage_partial,
                        'complete', tokens.damage_complete,
                        tokens.footprint_fill,
                    ],
                    'circle-radius': 8,
                    'circle-stroke-width': 2,
                    'circle-stroke-color': '#ffffff',
                },
            });

            // Fetch initial pins
            this._fetchPins();
        },

        async _fetchPins() {
            if (!this.config.pinsUrl) {
                return;
            }

            try {
                let url = this.config.pinsUrl;

                if (this.lastPinTimestamp) {
                    url += `?since=${encodeURIComponent(this.lastPinTimestamp)}`;
                }

                const response = await fetch(url);

                if (!response.ok) {
                    return;
                }

                const geojson = await response.json();
                const source = this.map.getSource('pins');

                if (source) {
                    source.setData(geojson);
                }

                // Track latest timestamp for incremental polling
                if (geojson.features && geojson.features.length > 0) {
                    const timestamps = geojson.features
                        .map((f) => f.properties.submitted_at)
                        .filter(Boolean)
                        .sort();
                    if (timestamps.length > 0) {
                        this.lastPinTimestamp = timestamps[timestamps.length - 1];
                    }
                }
            } catch (e) {
                console.warn('[rapida-map] Failed to fetch pins:', e.message);
            }
        },

        /* ----------------------------------------------------------
           Building tap handler — dispatches Alpine event
           ---------------------------------------------------------- */
        _setupBuildingTapHandler() {
            // Pointer cursor on hover
            this.map.on('mouseenter', 'buildings-fill', () => {
                this.map.getCanvas().style.cursor = 'pointer';
            });
            this.map.on('mouseleave', 'buildings-fill', () => {
                this.map.getCanvas().style.cursor = '';
            });

            // Click / tap handler
            this.map.on('click', 'buildings-fill', (e) => {
                if (!e.features || e.features.length === 0) {
                    return;
                }

                const feature = e.features[0];
                const props = feature.properties;

                // Get centroid from click location
                const lngLat = e.lngLat;

                // Dispatch Alpine event for Livewire to consume
                this.$dispatch('building-selected', {
                    id: String(props.id),
                    latitude: lngLat.lat,
                    longitude: lngLat.lng,
                    damage_level: props.canonical_damage_level || null,
                });

                // Visual feedback — brief highlight
                this.map.setPaintProperty('buildings-fill', 'fill-opacity', [
                    'case',
                    ['==', ['id'], feature.id],
                    0.8,
                    0.45,
                ]);

                // Reset after 2 seconds
                setTimeout(() => {
                    this.map.setPaintProperty('buildings-fill', 'fill-opacity', 0.45);
                }, 2000);
            });
        },

        /* ----------------------------------------------------------
           Tap-anywhere handler — fallback for map areas without buildings
           ---------------------------------------------------------- */
        _setupTapAnywhereHandler() {
            let selectedMarker = null;

            this.map.on('click', (e) => {
                // If the click hit a building, the building handler already fired
                const buildingFeatures = this.map.queryRenderedFeatures(e.point, {
                    layers: ['buildings-fill'],
                });
                if (buildingFeatures.length > 0) {
                    return; // Building tap handler takes priority
                }

                const lngLat = e.lngLat;

                // Remove previous selected marker
                if (selectedMarker) {
                    selectedMarker.remove();
                }

                // Drop a pin marker at the tapped location
                const pinEl = document.createElement('div');
                pinEl.style.cssText = `
                    width: 24px;
                    height: 24px;
                    background: ${this.config.tokens.damage_partial};
                    border: 3px solid #ffffff;
                    border-radius: 50%;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    cursor: pointer;
                `;
                selectedMarker = new maplibregl.Marker({ element: pinEl })
                    .setLngLat([lngLat.lng, lngLat.lat])
                    .addTo(this.map);

                // Show a toast-style popup
                new maplibregl.Popup({
                    closeButton: false,
                    closeOnClick: true,
                    offset: 20,
                    className: 'rapida-map-popup',
                })
                    .setLngLat([lngLat.lng, lngLat.lat])
                    .setHTML(`
                        <div style="font-family: 'Noto Sans', sans-serif; padding: 4px 8px; font-size: 14px; color: #333;">
                            <strong>Location selected</strong><br>
                            <span style="font-size: 12px; color: #666;">
                                ${lngLat.lat.toFixed(5)}, ${lngLat.lng.toFixed(5)}
                            </span>
                        </div>
                    `)
                    .addTo(this.map);

                // Dispatch to Livewire via Alpine
                this.$dispatch('building-selected', {
                    id: `point-${Date.now()}`,
                    latitude: lngLat.lat,
                    longitude: lngLat.lng,
                    damage_level: null,
                });
            });
        },

        /* ----------------------------------------------------------
           User GPS dot (reporter mode only)
           ---------------------------------------------------------- */
        _startGpsTracking() {
            if (!navigator.geolocation || window.location.protocol !== 'https:') {
                return; // GPS requires HTTPS (except localhost)
            }

            this.watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    const lngLat = [longitude, latitude];

                    if (!this.userMarker) {
                        // Create a pulsing user dot
                        const dot = document.createElement('div');
                        dot.className = 'rapida-user-dot';
                        dot.style.cssText = `
                            width: 16px;
                            height: 16px;
                            background: ${this.config.tokens.user_dot};
                            border: 3px solid #ffffff;
                            border-radius: 50%;
                            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.3);
                        `;

                        this.userMarker = new maplibregl.Marker({ element: dot })
                            .setLngLat(lngLat)
                            .addTo(this.map);
                    } else {
                        this.userMarker.setLngLat(lngLat);
                    }
                },
                (err) => {
                    console.warn('[rapida-map] GPS error:', err.message);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 10000,
                    timeout: 15000,
                },
            );
        },

        /* ----------------------------------------------------------
           Pin polling (dashboard mode only) — every 15s
           ---------------------------------------------------------- */
        _startPinPolling() {
            this.pollTimer = setInterval(() => {
                this._fetchPins();
            }, 15000);
        },
    };
}

/**
 * rapidaReportMap — Lightweight single-report map for the report detail view.
 *
 * Shows ONE pin at the report's coordinates, zoomed to street level.
 * No buildings layer, no pins API, no tap handlers, no GPS, no polling.
 *
 * @param {Object} config
 * @param {[number, number]} config.center — [lng, lat] of the report
 * @param {number} config.zoom — default 17 (street level, ~200m radius)
 * @param {string} config.damageLevel — 'minimal' | 'partial' | 'complete'
 * @param {string} config.label — text for the popup (e.g. location name)
 */
function rapidaReportMap(config = {}) {
    return {
        map: null,
        marker: null,

        config: {
            center: config.center || [-0.20, 5.56],
            zoom: config.zoom || 17,
            damageLevel: config.damageLevel || 'partial',
            label: config.label || '',
            tokens: { ...DEFAULT_TOKENS, ...(config.tokens || {}) },
        },

        init() {
            this.$nextTick(() => {
                this._createMap();
            });
        },

        destroy() {
            if (this.marker) {
                this.marker.remove();
            }
            if (this.map) {
                this.map.remove();
            }
        },

        _createMap() {
            const el = this.$el;
            if (!el) {
                return;
            }

            this.map = new maplibregl.Map({
                container: el,
                style: {
                    version: 8,
                    sources: {
                        'osm-raster': {
                            type: 'raster',
                            tiles: [
                                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                            ],
                            tileSize: 256,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        },
                    },
                    layers: [
                        {
                            id: 'osm-raster-layer',
                            type: 'raster',
                            source: 'osm-raster',
                            minzoom: 0,
                            maxzoom: 19,
                        },
                    ],
                },
                center: this.config.center,
                zoom: this.config.zoom,
                attributionControl: false,
                interactive: true,
            });

            this.map.on('load', () => {
                this._addReportMarker();
            });
        },

        _addReportMarker() {
            const color = damageColor(this.config.damageLevel, this.config.tokens);

            // Create a styled marker element
            const markerEl = document.createElement('div');
            markerEl.style.cssText = `
                width: 20px;
                height: 20px;
                background: ${color};
                border: 3px solid #ffffff;
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3), 0 0 0 4px ${color}33;
                cursor: default;
            `;

            this.marker = new maplibregl.Marker({ element: markerEl })
                .setLngLat(this.config.center)
                .addTo(this.map);

            // Add a popup with the location label if provided
            if (this.config.label) {
                const popup = new maplibregl.Popup({
                    closeButton: false,
                    closeOnClick: false,
                    offset: 16,
                    className: 'rapida-report-popup',
                })
                    .setHTML(`
                        <div style="font-family: 'Noto Sans', sans-serif; padding: 2px 6px; font-size: 13px; color: #333;">
                            ${this.config.label}
                        </div>
                    `);

                this.marker.setPopup(popup);
                popup.addTo(this.map);
            }
        },
    };
}

// Expose to Alpine/global scope
window.rapidaMap = rapidaMap;
window.rapidaReportMap = rapidaReportMap;
