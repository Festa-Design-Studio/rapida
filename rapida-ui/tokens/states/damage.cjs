// RAPIDA UI — Damage Level State Tokens
// Two token sets per level:
//   -ui-*  : Deep, muted, WCAG-compliant on cream backgrounds (buttons, cards, badges)
//   -map-* : Bright, high-visibility on gray map basemaps (pins, dots)

module.exports = {
  // ── Minimal ─────────────────────────────────
  'damage-minimal-ui':             '#2a5540',  // ground-green-800 (button/card text+bg)
  'damage-minimal-ui-surface':     '#f0f9f4',  // ground-green-50
  'damage-minimal-ui-text':        '#1e3d2f',  // ground-green-900
  'damage-minimal-ui-border':      '#b8d9c8',  // ground-green-200
  'damage-minimal-map':            '#22c55e',  // bright green (map pin/dot visibility)

  // ── Partial ─────────────────────────────────
  'damage-partial-ui':             '#c47d2a',  // alert-amber-500 (button/card text+bg)
  'damage-partial-ui-surface':     '#fdf6ec',  // alert-amber-50
  'damage-partial-ui-text':        '#7a4510',  // alert-amber-900
  'damage-partial-ui-border':      '#f5e4c8',  // alert-amber-100
  'damage-partial-map':            '#f59e0b',  // bright amber (map pin/dot visibility)

  // ── Complete ────────────────────────────────
  'damage-complete-ui':            '#c46b5a',  // crisis-rose-400 (button/card text+bg)
  'damage-complete-ui-surface':    '#fdf3f1',  // crisis-rose-50
  'damage-complete-ui-text':       '#5c2420',  // crisis-rose-900
  'damage-complete-ui-border':     '#f2dbd6',  // crisis-rose-100
  'damage-complete-map':           '#c46b5a',  // crisis-rose-400 (deep enough for map)

  // ── Unclassified ────────────────────────────
  'damage-none-ui':                '#475569',  // slate-600
  'damage-none-ui-surface':        '#f1f5f9',  // slate-100
  'damage-none-ui-border':         '#cbd5e1',  // slate-300
  'damage-none-map':               '#64748b',  // slate-500
}
