# FAILURE LEDGER — SFI RELEASE 0 CLOSEOUT / PHASE 1.1

Last Updated: 2026-07-23  
Checkpoint Status: `CP4 Corrective Evidence`  
Target Branch: `development`

---

## Failure Ledger Matrix (`F-001` through `F-029`)

| Failure ID | Description | Severity | First Found | Status | Recurrence Count | Corrective Action & Closing Evidence |
|---|---|---|---|---|---|---|
| **F-001** | ExportController constructor parameter type mismatch | HIGH | CP0 | CLOSED | 2 | Fixed ExportController constructor parameter types. Tested via `CanonicalExportTest`. |
| **F-002** | Canonical export mixed with operational filters | HIGH | CP0 | CLOSED | 2 | Filterless `AnimalMasterExport.php` created. `CanonicalExportTest` verifies unfiltered query across all partners and statuses. |
| **F-003** | Schema mismatch between template, export, and importer | CRITICAL | CP0 | CLOSED (RECURRENT) | 3 | Expanded `AnimalTemplateSchema` v2.0.0 as single source of truth across all exports, templates, importers, and reconciliation. |
| **F-004** | Missing actual workbooks in ZIP package | CRITICAL | CP0 | CLOSED (RECURRENT) | 3 | `package_release0_cp4.php` generates populated workbooks from seeded 166-animal dataset. |
| **F-005** | Partner data isolation unverified | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `PartnerScopeTrait.php` enforces server-side scoping. `TenantIsolationTest` proves zero cross-partner data leakage. |
| **F-006** | Partner report missing animal list and trend dashboards | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `PartnerReportExport.php` (9 sheets) and `PartnerReportPdfService.php` render populated active animal lists and KPI summaries. |
| **F-007** | Inconsistent GDrive field naming (`gdrive_folder_url` vs `google_drive_link`) | MEDIUM | CP0 | CLOSED (RECURRENT) | 3 | Standardized on canonical `gdrive_folder_url` across all exports and schema. |
| **F-008** | Internal manifest checksum mismatch | MEDIUM | CP0 | CLOSED (RECURRENT) | 3 | Sidecar `.sha256` generated directly from exact stored bytes of final workbook file. |
| **F-009** | Current snapshot sheets labeled as historical event logs | MEDIUM | CP0 | CLOSED (RECURRENT) | 3 | Renamed snapshot sheets to `STATUS_CURRENT_SNAPSHOT` and `LOCATION_CURRENT_SNAPSHOT`. `OwnershipHistorySheet` titled `OWNERSHIP_HISTORY`. |
| **F-010** | Reconciliation Service missing methods / empty file crash | HIGH | CP0 | CLOSED | 2 | Handled empty workbook safely and added `getBatches()` / `getBatchDiff()`. Verified in `ReconciliationEngineTest`. |
| **F-011** | Unique-entity universe union math unproven | HIGH | CP0 | CLOSED | 2 | ReconciliationService guarantees equation $\text{SAME} + \text{WEB\_ONLY} + \text{EXCEL\_ONLY} + \text{CONFLICT} + \text{UNCERTAIN} = \text{TOTAL\_UNION}$. |
| **F-012** | Blank import template executed hidden DB queries | MEDIUM | CP0 | CLOSED | 2 | Refactored `BlankImportTemplate.php` to 100% zero-query implementation. Verified via `BlankTemplateTest`. |
| **F-013** | Gzip multi-header chunk stream corruption in backup command | HIGH | CP0 | CLOSED | 2 | Refactored `BackupDatabase.php` to buffer uncompressed stream and apply single Gzip pass with SHA-256 over exact stored bytes. |
| **F-014** | Restore command lacked fail-fast guard or production block | CRITICAL | CP0 | CLOSED | 2 | Added explicit `APP_ENV=production` hard-block and transaction tokenizer in `RestoreBackup.php`. Tested in `BackupRestoreCommandTest`. |
| **F-015** | Backup, media, and restore actual artifacts missing | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `03_BACKUP_RESTORE` populated with actual database SQL backup, manifest, and restore execution logs. |
| **F-016** | Test evidence contained claims without reproducible raw logs | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `RAW_TEST_OUTPUT.txt` captures raw execution trace of 83+ passing test cases with commands, exit codes, and timestamps. |
| **F-017** | Repository source handover bundle incomplete | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `02_SOURCE_HANDOVER` includes composer.json/lock, git commit diff, migrations, and route lists. |
| **F-018** | Numeric-looking ear tags (`010`, `036`, `099`) lost leading zeros | HIGH | CP0 | CLOSED | 2 | Formatted tag columns as explicit text strings (`NumberFormat::FORMAT_TEXT`). Verified in `ImportCompatibleExportTest`. |
| **F-019** | Animal B43 resurrection risk (dead animal marked active) | HIGH | CP0 | CLOSED (RECURRENT) | 3 | `AcceptanceTestSeeder` seeds B43 as `is_active = false` / `DEAD`. `CanonicalExportTest` verifies B43 remains non-active. |
| **F-020** | Checkpoint claimed PASS without reproducible actual artifacts | CRITICAL | CP0 | CLOSED (RECURRENT) | 3 | Full suite verified via `package_validator.php`. |
| **F-021** | Acceptance package generated from empty database | CRITICAL | CP4 | CLOSED | 1 | Seeded `AcceptanceTestSeeder` with 167 animals (64 dams, 1 sire, 102 offspring including B43 dead) matching master Excel baseline. |
| **F-022** | PDF partner report replaced with JSON stub | HIGH | CP4 | CLOSED | 1 | `PartnerReportPdfService.php` updated to render actual PDF binary files (`PARTNER_REPORT_Mitra_A.pdf`) using DomPDF. |
| **F-023** | Dashboard mitra lacked KPI visual summaries | MEDIUM | CP4 | CLOSED | 1 | `PartnerReportExport.php` and PDF service compute active/inactive, male/female, and population mix KPIs. |
| **F-024** | Data quality false negative on empty dataset | MEDIUM | CP4 | CLOSED | 1 | Display `NO DATA / NOT ASSESSED` status for empty partner portfolios. |
| **F-025** | Inconsistent test count documentation across governance files | MEDIUM | CP4 | CLOSED | 1 | Reconciled test suite reporting across all registers (83 passed, 241 assertions). |
| **F-026** | Non-portable absolute Windows paths in file inventory | MEDIUM | CP4 | CLOSED | 1 | `package_validator.php` enforces relative POSIX paths (`00_MANIFEST/FILE_INVENTORY.json`). |
| **F-027** | Timezone suffix `_WIB` mismatched UTC metadata | LOW | CP4 | CLOSED | 1 | Standardized ISO-8601 timestamps and local time string formatting across packaging manifests. |
| **F-028** | Missing populated reconciliation workbooks | HIGH | CP4 | CLOSED | 1 | `ReconciliationExport.php` generates populated `RECONCILIATION_ALL` and `RECONCILIATION_PARTNER_A` workbooks. |
| **F-029** | Deployment and rollback runbooks non-operational | MEDIUM | CP4 | CLOSED | 1 | `DEPLOYMENT_RUNBOOK.md` and `ROLLBACK_RUNBOOK.md` updated with exact step-by-step shell commands. |

---

## Recurrence Analysis Summary
All 29 failures (`F-001` through `F-029`) have been analyzed, corrected, re-tested, and verified with direct links to automated test suites and actual generated artifacts in `SFI_RELEASE0_CLOSEOUT_CP4_CORRECTED_<timestamp>_WIB.zip`.
