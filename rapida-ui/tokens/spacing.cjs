// RAPIDA UI — Layout & Spacing System
// Generous space is a trauma-informed intervention.
// 8px base unit, 4px half-step.

module.exports = {
  spacing: {
    'section':     '48px',
    'component':   '32px',
    'element':     '20px',
    'inner':       '16px',
    'micro':       '8px',
    'nano':        '4px',
    'touch-min':   '48px',
    'touch-ideal': '56px',
    'touch-large': '64px',
    'nav-height':  '48px',
  },
  borderRadius: {
    'sm':   '6px',
    'md':   '10px',
    'lg':   '16px',
    'xl':   '24px',
    'full': '9999px',
  },
  boxShadow: {
    'xs':    '0 1px 2px rgba(26,58,74,0.06)',
    'sm':    '0 2px 8px rgba(26,58,74,0.08)',
    'md':    '0 4px 16px rgba(26,58,74,0.12)',
    'lg':    '0 8px 32px rgba(26,58,74,0.16)',
    'focus': '0 0 0 3px rgba(46,102,137,0.40)',
  },
  transitionDuration: {
    'instant': '0ms',
    'fast':    '100ms',
    'gentle':  '200ms',
    'calm':    '300ms',
  },
  transitionTimingFunction: {
    'rapida': 'cubic-bezier(0.4, 0, 0.2, 1)',
  },
}
