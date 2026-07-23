# REQUIREMENT_REGISTER.md — SFI Release 0 Closeout / Phase 1.1

| Requirement ID | Audit Ref | Description | Category | Acceptance Criteria | Target Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `R0-001` | Section 3.1 | Fix `ExportController::animals()` type mismatch (array to string parameter) | Defect Fix | No `TypeError` on PHP 8.3; canonical export accepts zero filters | PASS |
| `R0-002` | Section 3.2 | Separate export into 3 products: Canonical Full, Import-Compatible, Partner Report | Architecture | 3 distinct export classes/controllers & endpoints | PASS |
| `R0-003` | Section 3.3 | Fix canonical worksheet titles equality (`OWNERSHIP_HISTORY` matching sheet title) | Data Structure | Actual workbook titles match schema key names 100% | PASS |
| `R0-004` | Section 3.4 | Replace timestamp hash manifest with SHA-256 over exact stored bytes | Security | Sidecar manifest contains actual file SHA-256 | PASS |
| `R0-005` | Section 3.5 | Rename current-snapshot sheets from `HISTORY` to `CURRENT_SNAPSHOT` | Data Structure | No misleading history claims on snapshot sheets | PASS |
| `R0-006` | Section 3.6 | Establish single canonical field `gdrive_folder_url` with accessor alias | Compatibility | Zero link data loss between database and export | PASS |
| `R0-007` | Section 3.7 | Implement missing `ReconciliationService` methods (`getBatches`, `getBatchDiff`) | Defect Fix | All reconciliation routes pass smoke tests | PASS |
| `R0-008` | Section 3.8 | Fix reconciliation empty workbook `TypeError` & prove unique-entity union math | Engine | $\sum \text{Statuses} = \text{TOTAL\_UNION}$; zero DB side-effects | PASS |
| `R0-009` | Section 3.9 | Fix Gzip compression stream & stored-bytes checksum in backup command | Backup | Single valid Gzip stream; checksum matches stored bytes | PASS |
| `R0-010` | Section 3.10| Enforce disposable database guard & non-atomic DDL tokenizer in restore | Restore | Production hard-block (`APP_ENV=production`); post-restore FK check | PASS |
| `R0-011` | Section 2.1 | Implement Partner Export selection (`Semua Ternak` vs `Per Mitra`) & tenant isolation | Security | MITRA role forced to own scope; 0 cross-partner data leakage | PASS |
| `R0-012` | Section 2.2 | Create unified `AnimalTemplateSchema` shared by exports, templates, importers | Schema | Exact 1:1 schema equality between template & import export | PASS |
| `R0-013` | Section 2.2 | Enforce true string cell formatting for tags & UUIDs (`010`, `036`, `099`) | Format | Tags retain leading zeros without formula `="036"` | PASS |
| `R0-014` | Section 2.3 | Implement 9-sheet Partner XLSX Report & PDF with dashboard KPIs | Reporting | 100% numerical reconciliation between XLSX & PDF | PASS |
| `R0-015` | Section 5.3 | Add `PRELIMINARY / UNVERIFIED` label to financial metrics on Partner Reports | Compliance | Financial numbers clearly disclaimed | PASS |
| `R0-016` | Contract VII | Run full behavioral test suite `T-001` through `T-030` | Testing | 100% passing tests on final commit | PASS |
| `R0-017` | Contract IX | Package final Acceptance ZIP (`SFI_RELEASE0_CLOSEOUT_PARTNER_EXPORT_<timestamp>.zip`) | Deliverables | Contains raw test logs, actual workbooks, PDFs, manifest | PASS |
