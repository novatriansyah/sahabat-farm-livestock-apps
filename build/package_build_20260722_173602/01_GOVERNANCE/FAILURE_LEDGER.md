# FAILURE_LEDGER.md — SFI Release 0 Closeout / Phase 1.1

| Failure ID | Severity | Root Cause Analysis | Related Symbol / File | Corrective Action | Target Test ID | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `F-001` | CRITICAL | Constructor `AnimalMasterExport` received array `$filters` instead of string `$version` | `ExportController::animals`, `AnimalMasterExport` | Removed `$filters` parameter from Canonical Export constructor; created separate operational export class | `T-001` | CLOSED |
| `F-002` | HIGH | Canonical Export accepted operational filter arguments | `ExportController`, `AnimalMasterExport` | Enforced filterless signature for Canonical Full Export | `T-002` | CLOSED |
| `F-003` | HIGH | Canonical export, template, and importer used separate un-synchronized header lists | `AnimalMasterExport`, `BlankImportTemplate` | Implemented unified `AnimalTemplateSchema` data dictionary | `T-006` | CLOSED |
| `F-004` | HIGH | Workbook artifacts were missing from acceptance ZIP package | Final packaging script | Included actual generated XLSX/PDF workbooks in acceptance ZIP | `T-030` | CLOSED |
| `F-005` | CRITICAL | Export endpoint lacked partner selection and tenant data isolation | `ExportController`, `AnimalController` | Implemented `PartnerScopeTrait` and partner dropdown validation | `T-009`, `T-010` | CLOSED |
| `F-006` | HIGH | Partner report lacked active animal detail list and progress dashboard KPIs | `PartnerDashboardController`, View | Created `PartnerReportExport` (XLSX) and `PartnerReportPdfService` | `T-013` | CLOSED |
| `F-007` | HIGH | Inconsistent field names: `gdrive_folder_url` vs `google_drive_link` | Database schema & export sheets | Established canonical `gdrive_folder_url` with accessor fallback | `T-008` | CLOSED |
| `F-008` | HIGH | Manifest SHA-256 checksum calculated timestamp hash instead of stored file bytes | `ManifestSheet.php` | Computed SHA-256 over exact final stored bytes | `T-004` | CLOSED |
| `F-009` | HIGH | Current snapshot sheets labeled as historical event logs | `StatusEventsSheet`, `LocationHistorySheet` | Renamed sheets to `CURRENT_SNAPSHOT` to avoid fake history claims | `T-014` | CLOSED |
| `F-010` | CRITICAL | `ExportController` called non-existent methods `getBatches` & `getBatchDiff` | `ExportController`, `ReconciliationService` | Implemented missing methods in `ReconciliationService` | `T-015` | CLOSED |
| `F-011` | HIGH | Reconciliation entity universe math invariant not proven for duplicate rows | `ReconciliationService.php` | Implemented 5 unique entity statuses & union math equation | `T-017` | CLOSED |
| `F-012` | MEDIUM | `BlankImportTemplate` performed database queries during rendering | `BlankImportTemplate.php` | Refactored template to be 100% zero-query driven by static schema | `T-005` | CLOSED |
| `F-013` | CRITICAL | Backup command performed `gzencode()` per chunk creating multi-member Gzip stream | `BackupDatabase.php` | Used single Gzip stream and computed checksum over exact stored bytes | `T-021` | CLOSED |
| `F-014` | CRITICAL | Restore command assumed atomic DDL rollback in MySQL transaction | `RestoreBackup.php` | Added fail-fast tokenizer, disposable DB guard, and post-restore FK checks | `T-020`, `T-023` | CLOSED |
| `F-015` | HIGH | Backup, media, and restore artifacts missing from package | Backup script & packaging | Included actual staging backup SQL, media ZIP, and restore log | `T-030` | CLOSED |
| `F-016` | HIGH | Tests verified method existence/mocks instead of real behavioral outcomes | Test suite | Rewrote tests as end-to-end behavioral tests with actual artifacts | `T-001`..`T-030` | CLOSED |
| `F-017` | HIGH | Git commit/diff and lockfile evidence missing from package | Handover documentation | Provided exact git log, diff summary, and composer.lock | `T-028` | CLOSED |
| `F-018` | HIGH | Tags `010`, `036`, `099` converted to numbers or Excel formulas `="036"` | Excel export sheet renderers | Enforced explicit string cell data types (`DataType::TYPE_STRING`) | `T-007` | CLOSED |
| `F-019` | HIGH | Animal `B43` risk of appearing in active inventory | Query filters | Enforced `B43` as dead/inactive across all inventory queries | `T-004` | CLOSED |
| `F-020` | HIGH | Documentation claimed PASS status without reproducible artifacts | Handover documentation | Linked every PASS status to actual test output logs and file hashes | `T-030` | CLOSED |
