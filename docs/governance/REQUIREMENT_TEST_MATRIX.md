# REQUIREMENT_TEST_MATRIX.md — SFI Release 0 Closeout / Phase 1.1

| Requirement ID | Failure ID | Test ID | Test Level | Target Area / Fixture | Expected Result | Command | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `R0-001` | `F-001` | `T-001` | Feature | `GET /admin/export/animals` | 200 OK, XLSX attachment download, no `TypeError` | `php artisan test --filter=CanonicalExportTest` | PASS |
| `R0-002` | `F-002` | `T-002` | Feature | Canonical Export Filter | Accepts zero filter parameters; exports all database animals | `php artisan test --filter=CanonicalExportTest` | PASS |
| `R0-003` | `F-002` | `T-003` | Feature | Animal Count Equality | Exported UUID count equals `animals` table count | `php artisan test --filter=CanonicalExportTest` | PASS |
| `R0-004` | `F-019` | `T-004` | Unit | B43 Status Preservation | Animal `B43` is dead/inactive and excluded from active inventory | `php artisan test --filter=CanonicalExportTest` | PASS |
| `R0-012` | `F-012` | `T-005` | Unit | `BlankImportTemplate` | Generates template with 0 database queries executed | `php artisan test --filter=BlankTemplateTest` | PASS |
| `R0-012` | `F-003` | `T-006` | Unit | Schema 1:1 Equality | Template vs Import Export headers, order, and counts match 100% | `php artisan test --filter=ImportCompatibleExportTest` | PASS |
| `R0-013` | `F-018` | `T-007` | Unit | Text Cell Formatting | Tags `"010"`, `"036"`, `"099"` retain true string type without truncation | `php artisan test --filter=ImportCompatibleExportTest` | PASS |
| `R0-006` | `F-007` | `T-008` | Unit | GDrive URL Mapping | `gdrive_folder_url` preserved without loss of non-null link values | `php artisan test --filter=ImportCompatibleExportTest` | PASS |
| `R0-011` | `F-005` | `T-009` | Feature | Partner Tenant Isolation | Partner A export contains zero rows for Partner B or Internal SFI | `php artisan test --filter=TenantIsolationTest` | PASS |
| `R0-011` | `F-005` | `T-010` | Feature | MITRA Role Scope Guard | `MITRA` role cannot request or download another partner's data | `php artisan test --filter=TenantIsolationTest` | PASS |
| `R0-011` | `F-005` | `T-011` | Feature | Dropdown Partner Validation| System validates `partner_id` parameter against `master_partners,id` | `php artisan test --filter=TenantIsolationTest` | PASS |
| `R0-011` | `F-005` | `T-012` | Feature | Empty Partner Portfolio | Export handles empty partner portfolio and partner with dead animals | `php artisan test --filter=TenantIsolationTest` | PASS |
| `R0-014` | `F-006` | `T-013` | Feature | Partner Report | Partner Report XLSX & PDF match KPI summary counts 100% | `php artisan test --filter=PartnerReportTest` | PASS |
| `R0-003` | `F-009` | `T-014` | Unit | Worksheet Titles | Actual sheet titles match schema definitions exactly | `php artisan test --filter=CanonicalExportTest` | PASS |
| `R0-007` | `F-010` | `T-015` | Feature | Reconciliation Routes | `GET /reconciliation` and `GET /reconciliation/{batch}` return 200 OK | `php artisan test --filter=ReconciliationTest` | PASS |
| `R0-008` | `F-011` | `T-016` | Unit | Matching Ladder Tiers | Multi-tier matching (UUID $\rightarrow$ Active Tag $\rightarrow$ Tag History $\rightarrow$ Composite) | `php artisan test --filter=ReconciliationEngineTest` | PASS |
| `R0-008` | `F-011` | `T-017` | Unit | 5 Statuses Math Invariant | $\text{SAME} + \text{WEB\_ONLY} + \text{EXCEL\_ONLY} + \text{CONFLICT} + \text{UNCERTAIN} = \text{TOTAL\_UNION}$ | `php artisan test --filter=ReconciliationEngineTest` | PASS |
| `R0-008` | `F-011` | `T-018` | Unit | Zero-Write DB Canary | Comparison generates 0 database mutations (before/after count check) | `php artisan test --filter=ReconciliationEngineTest` | PASS |
| `R0-008` | `F-010` | `T-019` | Unit | Empty Workbook / Duplicates| Handles empty workbook, duplicate Excel rows, boolean conversion | `php artisan test --filter=ReconciliationEngineTest` | PASS |
| `R0-009` | `F-013` | `T-020` | Console | Uncompressed DB Backup | `db:backup` produces valid uncompressed SQL snapshot & SHA-256 | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-009` | `F-013` | `T-021` | Console | Compressed Gzip Backup | `db:backup --compress` produces single valid Gzip stream | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-009` | `F-013` | `T-022` | Console | Checksum Corruption | `db:restore` fails fast on checksum mismatch or corrupted file | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-010` | `F-014` | `T-023` | Console | Production Guard | `db:restore` throws Exception when `APP_ENV=production` | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-010` | `F-014` | `T-024` | Console | Post-Restore FK Validation | Post-restore record counts, FK constraints, and indexes valid | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-010` | `F-025` | `T-025` | Console | Special Character Safety | Restores nulls, Unicode, quotes, newlines, semicolons, and tags `"010"` | `php artisan test --filter=BackupRestoreCommandTest` | PASS |
| `R0-011` | `F-026` | `T-026` | Feature | RBAC Authorization Matrix| Direct URL access tested across `PEMILIK`, `PETERNAK`, `STAF`, `MITRA` | `php artisan test --filter=AuthorizationMatrixTest` | PASS |
| `R0-016` | `F-027` | `T-027` | Browser | UI Smoke Verification | Export & reconciliation UI layouts load without JS/CSS errors | `php artisan test` | PASS |
| `R0-016` | `F-028` | `T-028` | CleanRoom| Clean Room Build | `composer install` & `npm run build` pass clean from zero state | `php artisan test` | PASS |
| `R0-016` | `F-029` | `T-029` | Regression | Full Test Suite | All existing test suites pass with 0 failures | `php artisan test` | PASS |
| `R0-017` | `F-030` | `T-030` | Package | Acceptance ZIP Validation | ZIP contains manifest, SHA-256 hashes, workbooks, PDFs, test logs | `php artisan test` | PASS |
