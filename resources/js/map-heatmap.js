/* ============================================================
   map-heatmap.js — H3 heatmap layer
   Extracted from rapida-map.js for single-responsibility.
   ============================================================ */

import maplibregl from 'maplibre-gl';
import { cellToBoundary } from 'h3-js';

/**
 * Initialise H3 hexagon heatmap layer.
 *
 * @param {maplibregl.Map} map
 * @param {string} heatmapUrl - API endpoint for heatmap cell data
 * @returns {Promise<boolean>} true if layer was added, false on failure
 */
export async function initHeatmapLayer(map, heatmapUrl) {
    if (!heatmapUrl) {
        return false;
    }

    // Remove existing heatmap layers/source if re-rendering
    if (map.getSource('heatmap-cells')) {
        ['heatmap-labels', 'heatmap-outline', 'heatmap-fill'].forEach((id) => {
            if (map.getLayer(id)) {
                map.removeLayer(id);
            }
        });
        map.removeSource('heatmap-cells');
    }

    try {
        const res = await fetch(heatmapUrl);
        if (!res.ok) {
            return false;
        }
        const data = await res.json();

        const features = (data.cells || []).map((cell) => {
            // Convert H3 cell ID to polygon boundary using h3-js
            const boundary = cellToBoundary(cell.cell_id, true); // true = GeoJSON [lng, lat] order
            return {
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [boundary],
                },
                properties: {
                    cell_id: cell.cell_id,
                    report_count: cell.report_count,
                    minimal: cell.minimal,
                    partial: cell.partial,
                    complete: cell.complete,
                    dominant: cell.dominant,
                },
            };
        });

        map.addSource('heatmap-cells', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features },
        });

        // Fill layer — opacity by report count, color by dominant damage
        map.addLayer({
            id: 'heatmap-fill',
            type: 'fill',
            source: 'heatmap-cells',
            paint: {
                'fill-color': [
                    'match', ['get', 'dominant'],
                    'minimal', '#2e6689',  // rapida-blue-700
                    'partial', '#c47d2a',  // alert-amber-500
                    'complete', '#c46b5a', // crisis-rose-400
                    '#4a8db5',             // rapida-blue-500 default
                ],
                'fill-opacity': [
                    'interpolate', ['linear'], ['get', 'report_count'],
                    1, 0.2,
                    5, 0.35,
                    10, 0.5,
                    25, 0.65,
                ],
            },
        });

        // Outline layer
        map.addLayer({
            id: 'heatmap-outline',
            type: 'line',
            source: 'heatmap-cells',
            paint: {
                'line-color': '#1a3a4a', // rapida-blue-900
                'line-width': 1,
                'line-opacity': 0.3,
            },
        });

        // Count label
        map.addLayer({
            id: 'heatmap-labels',
            type: 'symbol',
            source: 'heatmap-cells',
            layout: {
                'text-field': ['to-string', ['get', 'report_count']],
                'text-size': 12,
                'text-font': ['Open Sans Bold', 'Arial Unicode MS Bold'],
            },
            paint: {
                'text-color': '#1a3a4a',
                'text-halo-color': '#ffffff',
                'text-halo-width': 1.5,
            },
        });

        // Popup on click
        map.on('click', 'heatmap-fill', (e) => {
            const props = e.features[0].properties;
            const html = `
                <div style="font-family:system-ui;font-size:13px;line-height:1.5;min-width:140px">
                    <strong>${props.report_count} reports in this zone</strong>
                    <div style="margin-top:6px;display:flex;gap:4px;align-items:center">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#22c55e"></span>
                        ${props.minimal} minimal
                    </div>
                    <div style="display:flex;gap:4px;align-items:center">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b"></span>
                        ${props.partial} partial
                    </div>
                    <div style="display:flex;gap:4px;align-items:center">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#c46b5a"></span>
                        ${props.complete} complete
                    </div>
                </div>
            `;
            new maplibregl.Popup({ offset: 10 })
                .setLngLat(e.lngLat)
                .setHTML(html)
                .addTo(map);
        });

        map.on('mouseenter', 'heatmap-fill', () => {
            map.getCanvas().style.cursor = 'pointer';
        });
        map.on('mouseleave', 'heatmap-fill', () => {
            map.getCanvas().style.cursor = '';
        });

        return true;
    } catch (err) {
        console.warn('Heatmap load failed:', err);
        return false;
    }
}

/**
 * Remove heatmap layers and source from the map.
 */
export function destroyHeatmapLayer(map) {
    if (!map) {
        return;
    }

    ['heatmap-labels', 'heatmap-outline', 'heatmap-fill'].forEach((id) => {
        if (map.getLayer(id)) {
            map.removeLayer(id);
        }
    });

    if (map.getSource('heatmap-cells')) {
        map.removeSource('heatmap-cells');
    }
}
