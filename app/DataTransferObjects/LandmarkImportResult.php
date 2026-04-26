<?php

namespace App\DataTransferObjects;

/**
 * Outcome of a CSV bulk import. The Livewire component renders this
 * directly in the post-import result table — `$imported` and
 * `$skipped` feed counters, `$errors` feeds a row-by-row
 * "row N: <reason>" listing.
 *
 * Errors are arrays of `['row' => int, 'reason' => string]` so the
 * UI can highlight specific lines in the operator's CSV.
 *
 * @phpstan-type ImportError array{row: int, reason: string}
 */
class LandmarkImportResult
{
    /**
     * @param  array<int, ImportError>  $errors
     */
    public function __construct(
        public readonly int $imported = 0,
        public readonly int $skipped = 0,
        public readonly array $errors = [],
    ) {}

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function totalRows(): int
    {
        return $this->imported + $this->skipped;
    }
}
