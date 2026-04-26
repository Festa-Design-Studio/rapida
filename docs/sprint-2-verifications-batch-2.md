# Sprint 2 Verifications — Batch 2 (gaps #26, #27)

## Gap #26 — multi_photo_enabled actually allows multi-photo submission

**Claim (PRD V2 §6 + Crisis model):** Operators can flag a crisis with `multi_photo_enabled = true` and reporters can then submit multiple photos per location.

**Code reality:** Partial. The `crises.multi_photo_enabled` column exists with proper fillable + boolean cast on `app/Models/Crisis.php:28,41`. But grep across `resources/views/components/wizard/` returns **zero** references to it. The wizard's photo step (`step-photo.blade.php`) accepts a single photo regardless of crisis configuration. `SubmitReportData` DTO carries one `photoUrl` field, not an array.

**Verdict:** Column is dead code. The flag is operator-settable in the model but not consumed anywhere in the wizard, the DTO, the API, or the WhatsApp bot.

**Follow-up gap (file as #51, M complexity):** Wire `multi_photo_enabled` through to behavior:
1. Wizard's step-photo accepts up to N photos (default 5) when `crisis.multi_photo_enabled` is true; renders a thumbnail strip + add-more button.
2. `SubmitReportData` DTO grows a `photoUrls: array<string>` alongside the legacy single `photoUrl`.
3. `damage_reports` table needs either a denormalised array column or a new `damage_report_photos` join table. Schema decision.
4. Server stores each photo through the existing `PhotoStorageService` + `PhotoExifService` (gap-01 EXIF pipeline applies per-photo).
5. WhatsApp flow: in `multi_photo_enabled` crises, accept "next" command between photos; cap at the same N.

## Gap #27 — Spatial indexes present and queries use them

**Claim:** PostGIS spatial indexes exist on `buildings.footprint_geom` and `damage_reports` lat/lng, and the `BuildingFootprintService::snapToNearest` query uses them.

**Code reality:** ✅ Honest at code level.
- `database/migrations/2026_03_27_210355_create_buildings_table.php:26` creates `buildings_footprint_geom_gist` GIST index.
- `database/migrations/2026_03_27_210354_create_crises_table.php:29` creates `crises_region_bbox_gist`.
- `database/migrations/2026_03_27_210405_add_postgis_spatial_indexes.php:15` creates an additional GIST on `ST_MakePoint(longitude, latitude)` for damage_reports.
- `app/Services/BuildingFootprintService.php:17,21` queries use `ST_DWithin(footprint_geom::geography, ST_MakePoint(?, ?)::geography, ?)` and `ST_Distance(...)` against the same column.

The query pattern is the canonical PostGIS form for nearest-neighbour lookups using the GIST index — PostgreSQL's planner will use the index for the `ST_DWithin` filter and a sequential scan only for the final `ORDER BY ST_Distance`.

**Verdict:** Verified at code level. Runtime `EXPLAIN ANALYZE` against the live PostGIS deploy is the only remaining check, and that's an operations task, not a code change.

**Action:** No new gap. Add `EXPLAIN ANALYZE` step to the deploy verification checklist (`docs/submission/deploy-verification-checklist.md`):

```
- [ ] EXPLAIN ANALYZE SELECT * FROM buildings WHERE ST_DWithin(footprint_geom::geography, ST_MakePoint(-0.20, 5.56)::geography, 100) — assert "Index Scan using buildings_footprint_geom_gist" appears in the plan
```

## Summary

| # | Claim | Status | Follow-up |
|---|-------|--------|-----------|
| 26 | multi_photo flag wired through to wizard | **False (column dead code)** | New gap #51 — wire end-to-end |
| 27 | Spatial indexes used by snap query | ✅ Honest | Add EXPLAIN check to deploy checklist |
