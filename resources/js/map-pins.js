/* ============================================================
   map-pins.js — Damage pin layer with clustering
   Extracted from rapida-map.js for single-responsibility.
   ============================================================ */

let pollTimerId = null;
let lastPinTimestamp = null;

/**
 * Fetch pin data (supports incremental polling via `since` param).
 */
async function fetchPins(map, pinsUrl) {
    if (!pinsUrl) {
        return;
    }

    try {
        let url = pinsUrl;

        if (lastPinTimestamp) {
            url += `?since=${encodeURIComponent(lastPinTimestamp)}`;
        }

        const response = await fetch(url);

        if (!response.ok) {
            return;
        }

        const geojson = await response.json();
        const source = map.getSource('pins');

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
                lastPinTimestamp = timestamps[timestamps.length - 1];
            }
        }
    } catch (e) {
        console.warn('[rapida-map] Failed to fetch pins:', e.message);
    }
}

/**
 * Initialise clustered pin layers on the map.
 *
 * @param {maplibregl.Map} map
 * @param {string} pinsUrl - API endpoint for pin GeoJSON
 * @param {Object} tokens - color tokens
 */
export function initPinLayer(map, pinsUrl, tokens) {
    // Guard: don't re-add if source already exists
    if (map.getSource('pins')) {
        return;
    }

    map.addSource('pins', {
        type: 'geojson',
        data: { type: 'FeatureCollection', features: [] },
        cluster: true,
        clusterMaxZoom: 14,
        clusterRadius: 50,
    });

    // Cluster circles
    map.addLayer({
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
    map.addLayer({
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
    map.addLayer({
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
    fetchPins(map, pinsUrl);
}

/**
 * Start polling for new pins at a fixed interval.
 *
 * @param {maplibregl.Map} map
 * @param {string} pinsUrl - API endpoint for pin GeoJSON
 * @param {Object} tokens - color tokens (unused during poll, but kept for API consistency)
 * @param {number} intervalMs - polling interval in milliseconds (default 15000)
 */
export function startPinPolling(map, pinsUrl, tokens, intervalMs = 15000) {
    // Clear any existing poll timer
    if (pollTimerId) {
        clearInterval(pollTimerId);
    }

    pollTimerId = setInterval(() => {
        fetchPins(map, pinsUrl);
    }, intervalMs);
}

/**
 * Remove pin layers, source, and stop polling.
 */
export function destroyPinLayer(map) {
    if (pollTimerId) {
        clearInterval(pollTimerId);
        pollTimerId = null;
    }

    lastPinTimestamp = null;

    if (!map) {
        return;
    }

    ['pin-unclustered', 'pin-cluster-count', 'pin-clusters'].forEach((id) => {
        if (map.getLayer(id)) {
            map.removeLayer(id);
        }
    });

    if (map.getSource('pins')) {
        map.removeSource('pins');
    }
}

/**
 * Reset the pin timestamp and re-fetch. Used by the external refetch API.
 *
 * @param {maplibregl.Map} map
 * @param {string} pinsUrl - API endpoint for pin GeoJSON
 */
export function refetchPins(map, pinsUrl) {
    lastPinTimestamp = null;
    fetchPins(map, pinsUrl);
}
