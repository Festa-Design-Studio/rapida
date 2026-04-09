// RAPIDA UI — Semantic Color Mapping
// All code must reference semantic tokens — never raw hex values directly.
// One change to a semantic mapping updates every surface that uses it.

module.exports = {
  // Surfaces
  'surface-page':           '#f0f7fa',  // rapida-blue-50
  'surface-form':           '#f7f7f5',  // neutral-50
  'surface-card':           '#ffffff',
  'surface-modal':          '#ffffff',
  'surface-nav':            '#1a3a4a',  // rapida-blue-900

  // Text
  'text-primary':           '#333333',  // grey-900
  'text-secondary':         '#555555',  // grey-700
  'text-placeholder':       '#888888',  // grey-500
  'text-on-dark':           '#f7f7f5',  // neutral-50
  'text-on-brand':          '#f0f7fa',  // rapida-blue-50

  // Brand / interactive
  'color-primary':          '#1a3a4a',  // rapida-blue-900
  'color-primary-hover':    '#24506a',  // rapida-blue-800
  'color-focus-ring':       '#2e6689',  // rapida-blue-700
  'color-link':             '#2e6689',  // rapida-blue-700

  // Status
  'color-success':          '#1e3d2f',  // ground-green-900
  'color-success-surface':  '#f0f9f4',  // ground-green-50
  'color-warning':          '#c47d2a',  // alert-amber-500
  'color-warning-surface':  '#fdf6ec',  // alert-amber-50
  'color-critical':         '#c46b5a',  // crisis-rose-400
  'color-critical-surface': '#fdf3f1',  // crisis-rose-50

  // Damage classification — canonical tokens in states/damage.cjs
  // Removed from here to prevent triple-definition (Gap C2).
  // Use damage-*-ui-* for cards/buttons, damage-*-map for pins.

  // Confirmation loop
  'confirmation-surface':   '#f0f9f4',  // ground-green-50
  'confirmation-text':      '#1e3d2f',  // ground-green-900
  'rapida-loop-surface':    '#fdf3f1',  // crisis-rose-50
  'rapida-loop-text':       '#5c2420',  // crisis-rose-900
}
