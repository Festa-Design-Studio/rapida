<?php

/*
|--------------------------------------------------------------------------
| Atom documentation coverage (gap-43)
|--------------------------------------------------------------------------
|
| Every atom in resources/views/components/atoms/ MUST have a corresponding
| docs/components/{atom}.md describing its keyboard contract, ARIA
| attributes, focus management, screen-reader behaviour, and copy
| contract.
|
| Adding a new atom without a doc would silently undermine the design
| system's a11y guarantees — this arch test makes the omission a CI
| failure.
|
*/

it('every atom in resources/views/components/atoms/ has a docs/components/{atom}.md', function () {
    $atomFiles = glob(dirname(__DIR__, 2).'/resources/views/components/atoms/*.blade.php');
    expect($atomFiles)->not->toBeEmpty('No atoms found — check the path.');

    $missing = [];

    foreach ($atomFiles as $atomPath) {
        // Strip leading "⚡" prefix used by Livewire single-file components
        // (none of the atoms currently use it, but allow for future ones).
        $atomName = preg_replace('/^⚡/u', '', basename($atomPath, '.blade.php'));
        $docPath = dirname(__DIR__, 2)."/docs/components/{$atomName}.md";

        if (! file_exists($docPath)) {
            $missing[] = $atomName;
        }
    }

    expect($missing)->toBe(
        [],
        'Missing docs/components/{atom}.md for: '.implode(', ', $missing)
    );
});

it('every atom doc covers keyboard, ARIA, focus, screen-reader, and copy sections', function () {
    $docFiles = glob(dirname(__DIR__, 2).'/docs/components/*.md');
    expect($docFiles)->not->toBeEmpty('No atom docs found.');

    $requiredSections = [
        'Keyboard contract',
        'ARIA contract',
        'Focus management',
        'Screen reader behavior',
        'Copy contract',
    ];

    foreach ($docFiles as $docPath) {
        $content = file_get_contents($docPath);
        $atomName = basename($docPath, '.md');

        foreach ($requiredSections as $section) {
            expect(str_contains($content, $section))->toBeTrue(
                "docs/components/{$atomName}.md is missing the '{$section}' section."
            );
        }
    }
});
