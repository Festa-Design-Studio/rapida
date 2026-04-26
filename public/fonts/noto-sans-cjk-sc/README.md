# Noto Sans CJK SC (Simplified Chinese)

Gap-45 deferred binary placement: WOFF2 files are not committed to the repo (~7MB each); the deploy step or a developer must download them once.

## Required files

- `NotoSansSC-Regular.woff2` (weight 400)
- `NotoSansSC-Bold.woff2` (weight 700)

## How to fetch

Download from Google Fonts:

1. Visit https://fonts.google.com/noto/specimen/Noto+Sans+SC
2. Select weights 400 + 700, language: Chinese (Simplified)
3. Download → extract → drop the WOFF2 files here

Or via google-webfonts-helper for the subsetted variant:
https://gwfh.mranftl.com/fonts/noto-sans-sc?subsets=chinese-simplified,latin

## Fallback

If these files are missing at runtime the cascade in `resources/css/app.css` falls back to system-stack fonts: `system-ui`, `PingFang SC` (macOS/iOS), `Microsoft YaHei` (Windows). Chinese text still renders but with vendor-dependent inconsistency — the explicit Noto Sans SC is the guarantee.

## Why subsetted (unicode-range)

The `@font-face` rule in `resources/views/layouts/rapida.blade.php` uses `unicode-range: U+4E00-9FFF, U+3000-303F, U+FF00-FFEF` so browsers download the file only when CJK characters are actually present in the rendered text. Latin/Arabic/Cyrillic users pay zero bandwidth cost.
