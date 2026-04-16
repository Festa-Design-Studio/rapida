import maplibregl from 'maplibre-gl';
import { Protocol } from 'pmtiles';
import { initBuildingLayer, destroyBuildingLayer } from './map-buildings';
import { initPinLayer, destroyPinLayer, startPinPolling, refetchPins } from './map-pins';
import { initHeatmapLayer, destroyHeatmapLayer } from './map-heatmap';

/* ============================================================
   RAPIDA Map — Alpine.js MapLibre GL Controller (Composer)
   Imports building, pin, and heatmap layers from focused modules.
   ============================================================ */

// Register PMTiles protocol once globally
const protocol = new Protocol();
maplibregl.addProtocol('pmtiles', protocol.tile);

// Tokens should be passed from server config via Alpine data attribute.
// These defaults match config/rapida-tokens.php as a safety net.
const DEFAULT_TOKENS = {
    damage_minimal: '#22c55e',
    damage_partial: '#f59e0b',
    damage_complete: '#c46b5a',
    footprint_fill: '#2e6689',
    footprint_stroke: '#1a3a4a',
    user_dot: '#2e6689',
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
 * @param {string} config.heatmapUrl — API endpoint for H3 heatmap
 */
function rapidaMap(config = {}) {
    return {
        map: null,
        userMarker: null,
        watchId: null,

        config: {
            crisisSlug: config.crisisSlug || '',
            mode: config.mode || 'reporter',
            center: config.center || [-0.20, 5.56],
            zoom: config.zoom || 14,
            tokens: { ...DEFAULT_TOKENS, ...(config.tokens || {}) },
            buildingsUrl: config.buildingsUrl || '',
            pinsUrl: config.pinsUrl || '',
            heatmapUrl: config.heatmapUrl || '',
        },

        init() {
            const component = this;
            this.$nextTick(() => {
                this._createMap();
            });

            // Expose a stable refetch API on the DOM element for external callers
            // (dashboard filters). Uses plain object to avoid Livewire circular JSON.
            if (this.$el) {
                this.$el.__rapidaMapApi = {
                    refetchPins(url) {
                        component.config.pinsUrl = url;
                        refetchPins(component.map, url);
                    },
                };
            }
        },

        destroy() {
            if (this.watchId !== null) {
                navigator.geolocation.clearWatch(this.watchId);
            }
            destroyPinLayer(this.map);
            destroyBuildingLayer(this.map);
            destroyHeatmapLayer(this.map);
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
                const tokens = this.config.tokens;
                const dispatch = this.$dispatch.bind(this);

                initBuildingLayer(this.map, this.config.buildingsUrl, tokens, dispatch);
                this._setupTapAnywhereHandler();

                if (this.config.mode === 'reporter' && this.config.heatmapUrl) {
                    // Privacy-conscious: show aggregated H3 hexagons, not individual pins
                    initHeatmapLayer(this.map, this.config.heatmapUrl).then((ok) => {
                        if (!ok) {
                            // Fallback to pins if heatmap fails
                            initPinLayer(this.map, this.config.pinsUrl, tokens);
                        }
                    });
                    this._startGpsTracking();
                } else {
                    // Dashboard/analyst: show individual pins
                    initPinLayer(this.map, this.config.pinsUrl, tokens);
                    if (this.config.mode === 'dashboard') {
                        startPinPolling(this.map, this.config.pinsUrl, tokens);
                    }
                }
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
