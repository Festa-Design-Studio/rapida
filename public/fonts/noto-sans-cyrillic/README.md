# Noto Sans Cyrillic Subset

Gap-45 deferred binary placement: the WOFF2 files are not committed to the repo (binary blobs); the deploy step or a developer must download them once.

## Required files

- `NotoSans-Cyrillic-Regular.woff2` (weight 400)
- `NotoSans-Cyrillic-Bold.woff2` (weight 700)

## How to fetch

Download from Google Fonts (helper subset):

1. Visit https://fonts.google.com/noto/specimen/Noto+Sans
2. Select weights 400 + 700, language: Cyrillic
3. Download → extract → drop the WOFF2 files here

Or via google-webfonts-helper:
https://gwfh.mranftl.com/fonts/noto-sans?subsets=cyrillic

## Fallback

If these files are missing at runtime the cascade in `resources/css/app.css` falls back to `'Noto Sans'` (which bundles partial Cyrillic) then `system-ui`. Russian text still renders, just inconsistently across devices — the explicit subset is the guarantee.
