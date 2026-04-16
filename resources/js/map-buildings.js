/* ============================================================
   map-buildings.js — Building footprint layer
   Extracted from rapida-map.js for single-responsibility.
   ============================================================ */

let moveendHandler = null;

/**
 * Fetch building footprints within current viewport.
 */
async function fetchBuildings(map, buildingsUrl) {
    if (!buildingsUrl) {
        return;
    }

    try {
        const bounds = map.getBounds();
        const bbox = [
            bounds.getWest(),
            bounds.getSouth(),
            bounds.getEast(),
            bounds.getNorth(),
        ].join(',');

        const url = `${buildingsUrl}?bbox=${bbox}`;
        const response = await fetch(url);

        if (!response.ok) {
            return;
        }

        const geojson = await response.json();
        const source = map.getSource('buildings');

        if (source) {
            source.setData(geojson);
        }
    } catch (e) {
        console.warn('[rapida-map] Failed to fetch buildings:', e.message);
    }
}

/**
 * Initialise building footprint layers (fill + stroke) and tap handler.
 *
 * @param {maplibregl.Map} map
 * @param {string} buildingsUrl - API endpoint for building GeoJSON
 * @param {Object} tokens - color tokens
 * @param {Function} dispatch - bound Alpine $dispatch for emitting events
 */
export function initBuildingLayer(map, buildingsUrl, tokens, dispatch) {
    // Guard: don't re-add if source already exists
    if (map.getSource('buildings')) {
        return;
    }

    map.addSource('buildings', {
        type: 'geojson',
        data: { type: 'FeatureCollection', features: [] },
    });

    // Fill layer — colored by damage level
    map.addLayer({
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
    map.addLayer({
        id: 'buildings-stroke',
        type: 'line',
        source: 'buildings',
        paint: {
            'line-color': tokens.footprint_stroke,
            'line-width': 1.5,
        },
    });

    // Fetch buildings
    fetchBuildings(map, buildingsUrl);

    // Reload buildings when map moves
    moveendHandler = () => {
        fetchBuildings(map, buildingsUrl);
    };
    map.on('moveend', moveendHandler);

    // --- Tap handler ---

    // Pointer cursor on hover
    map.on('mouseenter', 'buildings-fill', () => {
        map.getCanvas().style.cursor = 'pointer';
    });
    map.on('mouseleave', 'buildings-fill', () => {
        map.getCanvas().style.cursor = '';
    });

    // Click / tap handler
    map.on('click', 'buildings-fill', (e) => {
        if (!e.features || e.features.length === 0) {
            return;
        }

        const feature = e.features[0];
        const props = feature.properties;

        // Get centroid from click location
        const lngLat = e.lngLat;

        // Dispatch Alpine event for Livewire to consume
        dispatch('building-selected', {
            id: String(props.id),
            latitude: lngLat.lat,
            longitude: lngLat.lng,
            damage_level: props.canonical_damage_level || null,
        });

        // Visual feedback — brief highlight
        map.setPaintProperty('buildings-fill', 'fill-opacity', [
            'case',
            ['==', ['id'], feature.id],
            0.8,
            0.45,
        ]);

        // Reset after 2 seconds
        setTimeout(() => {
            map.setPaintProperty('buildings-fill', 'fill-opacity', 0.45);
        }, 2000);
    });
}

/**
 * Remove building layers and source from the map.
 */
export function destroyBuildingLayer(map) {
    if (!map) {
        return;
    }

    if (moveendHandler) {
        map.off('moveend', moveendHandler);
        moveendHandler = null;
    }

    ['buildings-stroke', 'buildings-fill'].forEach((id) => {
        if (map.getLayer(id)) {
            map.removeLayer(id);
        }
    });

    if (map.getSource('buildings')) {
        map.removeSource('buildings');
    }
}
