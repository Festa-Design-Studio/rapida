// RAPIDA UI — Typography System
// Legibility under stress above all else.
// Inter for headings/UI, Noto Sans for body/forms.

module.exports = {
  fontFamily: {
    heading: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
    sans:    ['Noto Sans', 'Noto Sans Arabic', 'system-ui', 'sans-serif'],
    arabic:  ['Noto Sans Arabic', 'Noto Sans', 'sans-serif'],
    mono:    ['JetBrains Mono', 'monospace'],
  },
  fontSize: {
    'display': ['2rem',     { lineHeight: '1.25', letterSpacing: '-0.01em' }],
    'h1':      ['1.75rem',  { lineHeight: '1.3',  letterSpacing: '-0.01em' }],
    'h2':      ['1.375rem', { lineHeight: '1.35' }],
    'h3':      ['1.125rem', { lineHeight: '1.4'  }],
    'h4':      ['1rem',     { lineHeight: '1.5'  }],
    'body-lg': ['1.125rem', { lineHeight: '1.6'  }],
    'body':    ['1rem',     { lineHeight: '1.6'  }],
    'body-sm': ['0.875rem', { lineHeight: '1.6'  }],
    'label':   ['0.875rem', { lineHeight: '1.4', fontWeight: '500' }],
    'caption': ['0.75rem',  { lineHeight: '1.5'  }],
    'btn':     ['1rem',     { lineHeight: '1.25', fontWeight: '600' }],
    'btn-sm':  ['0.875rem', { lineHeight: '1.25', fontWeight: '600' }],
  },
}
