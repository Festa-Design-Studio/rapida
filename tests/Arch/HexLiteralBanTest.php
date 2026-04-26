<?php

use Symfony\Component\Finder\Finder;

/**
 * Trauma-informed token enforcement (gap-41): raw hex color literals in Blade
 * components are drift away from the canonical token system in
 * config/rapida-tokens.php and rapida-ui/tokens/. Use Tailwind classes
 * (text-rapida-blue-900) or {{ Js::from(config('rapida-tokens.X')) }} instead.
 *
 * Allowlist:
 *   - HTML numeric entities (&#10003;, &#039;)
 *   - Lines using Js::from(config( for JS x-data attributes
 *   - Lines containing config('rapida-tokens
 *   - Lines marked with // allowed: justification
 *   - Comment-only lines (developer notes referencing hex values for context)
 *   - SVG fill/stroke/stop-color attributes (genuine SVG-attribute use cases)
 */
it('forbids hex color literals in Blade components (gap-41)', function () {
    $componentsRoot = __DIR__.'/../../resources/views/components';

    $finder = (new Finder)
        ->files()
        ->in($componentsRoot)
        ->name('*.blade.php');

    // 6- or 8-digit hex color literals: #aabbcc, #aabbccff. Excludes 3-digit
    // because the regex would catch numeric HTML entities like &#039 too easily.
    $hexPattern = '/#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?\b/';

    $offenders = [];
    foreach ($finder as $file) {
        $relative = str_replace(realpath($componentsRoot).'/', '', $file->getRealPath());
        $content = file_get_contents($file->getRealPath());

        foreach (explode("\n", $content) as $lineNumber => $line) {
            // Allowlist
            if (str_contains($line, '&#')) {
                continue;
            }
            if (str_contains($line, 'Js::from(config(') || str_contains($line, "config('rapida-tokens")) {
                continue;
            }
            if (str_contains($line, '// allowed:') || str_contains($line, '{{-- allowed --}}')) {
                continue;
            }
            // Pure comment lines that just document a token's hex value for context
            if (preg_match('/^\s*\/\/\s*[a-z-]+:\s*#[0-9a-fA-F]{6}/i', $line)) {
                continue;
            }

            // SVG attribute literals — fill="#abc", stroke="#abc", stop-color="#abc".
            // SVG attributes don't accept Tailwind classes, so the hex stays inline.
            $linePostSvgStrip = preg_replace(
                '/(fill|stroke|stop-color)\s*=\s*"#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?"/',
                '',
                $line,
            );
            if (! preg_match($hexPattern, $linePostSvgStrip ?? '')) {
                continue;
            }

            if (preg_match_all($hexPattern, $line, $matches)) {
                $offenders[$relative][] = [
                    'line' => $lineNumber + 1,
                    'hex' => array_values(array_unique($matches[0])),
                ];
            }
        }
    }

    expect($offenders)->toBeEmpty(
        'Hex color literals are forbidden in components. Use Tailwind classes or {{ Js::from(config("rapida-tokens.X")) }}. Offenders: '
        .json_encode($offenders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
});
