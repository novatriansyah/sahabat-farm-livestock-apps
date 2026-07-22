# MANIFEST SOURCE CODE TAHAP 1 (CORRECTED)

**Commit Base:** `f8a8c7cc96429eb7a74e20350b05a14975444612`  
**Latest Corrective Commits:** Phase 1 Audit Remediation  

---

## Modul & File Utama

### 1. Backup & Restore Engine
- `app/Console/Commands/BackupDatabase.php` — Direct stream backup, string numeric tag preservation, SHA-256 manifest.
- `app/Console/Commands/RestoreBackup.php` — Production safety guard (`APP_ENV=production`), PDO statement parser, post-restore integrity check.
- `routes/web.php` — Renamed route `/admin/export/data-snapshot-json`.
- `app/Http/Controllers/ExportController.php` — Renamed action `dataSnapshotJson()`.

### 2. Canonical Export
- `app/Exports/AnimalMasterExport.php` — Unfiltered master export with 13 canonical sheets.
- `app/Exports/Sheets/AnimalsCurrentSheet.php` — Source-of-truth master entity sheet with UUID.
- `app/Exports/Sheets/ParentageBirthEventsSheet.php`
- `app/Exports/Sheets/WeightEventsSheet.php`
- `app/Exports/Sheets/TagHistorySheet.php`
- `app/Exports/Sheets/StatusEventsSheet.php`
- `app/Exports/Sheets/LocationHistorySheet.php`
- `app/Exports/Sheets/OwnershipHistorySheet.php`
- `app/Exports/Sheets/ExitDeathEventsSheet.php`
- `app/Exports/Sheets/HealthTreatmentEventsSheet.php`
- `app/Exports/Sheets/MediaLinksSheet.php`
- `app/Exports/Sheets/DataQualityIssuesSheet.php`
- `app/Exports/Sheets/ReferenceMappingSheet.php`

### 3. Reconciliation Engine & Blank Template
- `app/Services/ReconciliationService.php` — Header & sheet-based parsing, 4-tier match ladder, zero DB side-effect comparison.
- `app/Exports/BlankImportTemplate.php` — Zero DB query template with injected/static reference mapping and `[CONTOH]` rows.

### 4. Test Suites
- `tests/Feature/CanonicalExportTest.php`
- `tests/Unit/BlankTemplateTest.php`
- `tests/Feature/ReconciliationEngineTest.php`
- `tests/Feature/BackupRestoreCommandTest.php`
