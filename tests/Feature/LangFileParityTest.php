<?php

/**
 * Lang-file completeness audit (gap-25). For every lang file in lang/en/*.php,
 * assert each of the other 5 UN locales has the exact same set of keys.
 * Any divergence means the UI in that locale will fall back to the raw key
 * (e.g., "rapida.transparency_conflict_1") visible to a real reporter.
 *
 * This test is the regression guard for every microcopy gap that adds a key
 * to one locale but forgets the other five. Failure output names the missing
 * keys so the fix is one append-to-array per offender.
 */
it('every UN locale has the same key set as English in every lang file', function (string $file) {
    $base = lang_path("en/{$file}");
    expect(file_exists($base))->toBeTrue("lang/en/{$file} must exist as the canonical key set");

    $expected = collectKeys(require $base);

    $missingByLocale = [];
    foreach (['fr', 'ar', 'es', 'zh', 'ru'] as $locale) {
        $localePath = lang_path("{$locale}/{$file}");
        if (! file_exists($localePath)) {
            $missingByLocale[$locale] = ['<entire file missing>'];

            continue;
        }
        $actual = collectKeys(require $localePath);
        $missing = array_values(array_diff($expected, $actual));
        if ($missing !== []) {
            $missingByLocale[$locale] = $missing;
        }
    }

    expect($missingByLocale)->toBeEmpty(
        "Lang parity gap in lang/*/{$file}. Append the listed keys to the locale arrays before merging:\n"
        .json_encode($missingByLocale, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
})->with(['rapida.php', 'wizard.php', 'whatsapp.php', 'onboarding.php', 'account.php']);

/**
 * Recursively flatten a Laravel lang array into dotted key paths.
 *
 * @param  array<string, mixed>  $array
 * @return array<int, string>
 */
function collectKeys(array $array, string $prefix = ''): array
{
    $keys = [];
    foreach ($array as $key => $value) {
        $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
        if (is_array($value)) {
            $keys = array_merge($keys, collectKeys($value, $path));
        } else {
            $keys[] = $path;
        }
    }

    return $keys;
}
