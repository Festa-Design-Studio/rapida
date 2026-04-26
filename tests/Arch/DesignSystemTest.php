<?php

use Symfony\Component\Finder\Finder;

/**
 * Trauma-informed UX rule from the RAPIDA design system: do not use pure
 * red anywhere a person in crisis will see it. Pure red triggers stress;
 * use the crisis-rose palette (muted terracotta) for danger states.
 *
 * The token doc at resources/views/rapida-ui/tokens/colors.blade.php is
 * explicit: "Never use red." This test enforces it across all Blade
 * components so a future PR cannot reintroduce the violation by accident.
 */
it('forbids pure-red Tailwind classes in Blade components', function () {
    $componentsRoot = __DIR__.'/../../resources/views/components';

    $finder = (new Finder)
        ->files()
        ->in($componentsRoot)
        ->name('*.blade.php');

    $pattern = '/\b(text-red|bg-red|border-red|ring-red|from-red|to-red|via-red|fill-red|stroke-red|divide-red|placeholder-red|caret-red|accent-red|outline-red|shadow-red|decoration-red)-\d+\b/';

    $offenders = [];
    foreach ($finder as $file) {
        $content = file_get_contents($file->getRealPath());
        if (preg_match_all($pattern, $content, $matches)) {
            $relative = str_replace(realpath($componentsRoot).'/', '', $file->getRealPath());
            $offenders[$relative] = array_values(array_unique($matches[0]));
        }
    }

    expect($offenders)->toBeEmpty(
        'Pure-red Tailwind classes are forbidden in components — use crisis-rose-* tokens instead. Offenders: '
        .json_encode($offenders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
});
