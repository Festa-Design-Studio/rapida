<?php

/*
|--------------------------------------------------------------------------
| RTL logical-direction enforcement (gap-42)
|--------------------------------------------------------------------------
|
| Tailwind 3.3+ ships logical-direction utilities (ps-/pe-/ms-/me-/
| start-/end-/text-start/text-end) that flip automatically when the
| <html dir="rtl"> attribute is set (Arabic locale).
|
| Physical-direction utilities (pl-/pr-/ml-/mr-/text-left/text-right)
| do NOT flip — they break Arabic layouts silently. This arch test
| bans them in resources/views/components/ so a future PR cannot
| reintroduce the regression.
|
*/

if (! function_exists('rtl_glob_recursive')) {
    function rtl_glob_recursive(string $dir, string $pattern): array
    {
        $files = glob($dir.'/'.$pattern) ?: [];
        foreach (glob($dir.'/*', GLOB_ONLYDIR | GLOB_NOSORT) ?: [] as $sub) {
            $files = array_merge($files, rtl_glob_recursive($sub, $pattern));
        }

        return $files;
    }
}

it('no Blade component file in resources/views/components/ uses physical-direction Tailwind utilities', function () {
    $files = rtl_glob_recursive(dirname(__DIR__, 2).'/resources/views/components', '*.blade.php');
    expect($files)->not->toBeEmpty('No component files found.');

    // Match physical-direction class tokens with word boundaries — must
    // not match inside SVG attribute values or arbitrary class names
    // like "scroll-left-button".
    $physicalPattern = '/(?<![\w-])(?:pl-\d|pr-\d|ml-\d|mr-\d|sm:pl-\d|sm:pr-\d|md:pl-\d|md:pr-\d|lg:pl-\d|lg:pr-\d|text-left|text-right|-mr-\d|-ml-\d|pl-(?:inner|component|micro|touch-min)|pr-(?:inner|component|micro|touch-min))(?![\w-])/';

    $offenders = [];

    foreach ($files as $path) {
        $content = file_get_contents($path);
        if (preg_match_all($physicalPattern, $content, $matches)) {
            $relative = str_replace(dirname(__DIR__, 2).'/', '', $path);
            $offenders[$relative] = array_values(array_unique($matches[0]));
        }
    }

    $offenderReport = collect($offenders)
        ->map(fn ($matches, $file) => "  {$file}: ".implode(', ', $matches))
        ->implode("\n");

    expect($offenders)->toBe(
        [],
        "Physical-direction Tailwind utilities found in components — convert to logical (ps-/pe-/ms-/me-/text-start/text-end):\n".$offenderReport
    );
});
