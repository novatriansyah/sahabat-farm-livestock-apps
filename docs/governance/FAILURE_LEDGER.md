# FAILURE LEDGER — SFI RELEASE 0 CLOSEOUT / PHASE 1.1

Last Updated: 2026-07-23  
Checkpoint Status: `CP8 Phase 1 Final Closeout (CLOSED)`  
Target Branch: `development`

---

## Failure Ledger Matrix (`F-001` through `F-045`)

| Failure ID | Description | Severity | First Found | Status | Recurrence Count | Corrective Action & Closing Evidence |
|---|---|---|---|---|---|---|
| **F-001** | ExportController constructor parameter type mismatch | HIGH | CP0 | CLOSED | 2 | Fixed ExportController constructor parameter types. Tested via `CanonicalExportTest`. |
| **F-002** | Canonical export mixed with operational filters | HIGH | CP0 | CLOSED | 2 | Filterless `AnimalMasterExport.php` created. `CanonicalExportTest` verifies unfiltered query across all partners and statuses. |
| **F-003** | Schema mismatch between template, export, and importer | CRITICAL | CP0 | CLOSED (RECURRENT) | 4 | Unified `AnimalTemplateSchema` v2.0.0 (35 columns) across template, export, `AnimalsImport.php`, and `ReconciliationService.php`. Persistent migration added. |
| **F-004** | Missing actual workbooks in ZIP package | CRITICAL | CP0 | CLOSED (RECURRENT) | 4 | `package_release0_cp8.php` generates populated workbooks from seeded 166-animal dataset. |
| **F-005** | Partner data isolation unverified | HIGH | CP0 | CLOSED (RECURRENT) | 4 | `ExportCenterController.php` enforces server-side scoping. `ExportCenterAndParityTest` proves zero cross-partner data leakage. |
| **F-006** | Partner report missing animal list and trend dashboards | HIGH | CP0 | CLOSED (RECURRENT) | 4 | `PartnerReportExport.php` and `PartnerReportPdfService.php` render populated active animal lists and dynamic ADG calculations. |
| **F-007** | Inconsistent GDrive field naming (`gdrive_folder_url` vs `google_drive_link`) | MEDIUM | CP0 | CLOSED (RECURRENT) | 4 | Standardized on canonical `gdrive_folder_url` across all exports and schema. |
| **F-008** | Internal manifest checksum mismatch | MEDIUM | CP0 | CLOSED (RECURRENT) | 4 | Sidecar `.sha256` generated directly from exact stored bytes of final workbook file. |
| **F-009** | Current snapshot sheets labeled as historical event logs | MEDIUM | CP0 | CLOSED (RECURRENT) | 4 | Renamed snapshot sheets to `STATUS_CURRENT_SNAPSHOT` and `LOCATION_CURRENT_SNAPSHOT`. `OwnershipHistorySheet` titled `OWNERSHIP_HISTORY`. |
| **F-010** | Reconciliation Service missing methods / empty file crash | HIGH | CP0 | CLOSED (RECURRENT) | 3 | Handled empty workbook safely and added `getBatches()` / `getBatchDiff()`. Enforced partner isolation in reconciliation queries. |
| **F-011** | Unique-entity universe union math unproven | HIGH | CP0 | CLOSED (RECURRENT) | 3 | ReconciliationService guarantees equation $\text{SAME} + \text{WEB\_ONLY} + \text{EXCEL\_ONLY} + \text{CONFLICT} + \text{UNCERTAIN} = \text{TOTAL\_UNION}$. |
| **F-012** | Blank import template executed hidden DB queries | MEDIUM | CP0 | CLOSED | 2 | Refactored `BlankImportTemplate.php` to 100% zero-query implementation. Verified via `BlankTemplateTest`. |
| **F-013** | Gzip multi-header chunk stream corruption in backup command | HIGH | CP0 | CLOSED | 2 | Refactored `BackupDatabase.php` to buffer uncompressed stream and apply single Gzip pass with SHA-256 over exact stored bytes. |
| **F-014** | Restore command lacked fail-fast guard or production block | CRITICAL | CP0 | CLOSED | 2 | Added explicit `APP_ENV=production` hard-block and transaction tokenizer in `RestoreBackup.php`. |
| **F-015** | Backup, media, and restore actual artifacts missing | HIGH | CP0 | CLOSED (RECURRENT) | 4 | `03_BACKUP_RESTORE` populated with actual database SQL backup, media manifest, and restore execution logs. |
| **F-016** | Test evidence contained claims without reproducible raw logs | HIGH | CP0 | CLOSED (RECURRENT) | 4 | `RAW_TEST_OUTPUT.txt` captures raw execution trace of passing test suite with `junit.xml`. |
| **F-017** | Repository source handover bundle incomplete | HIGH | CP0 | CLOSED (RECURRENT) | 4 | `02_SOURCE_HANDOVER` includes full rebuildable repository (`app/`, `tests/`, `routes/`, `database/`, `bootstrap/`, `config/`, `public/`, `resources/`, composer/package files). |
| **F-018** | Numeric-looking ear tags (`010`, `036`, `099`) lost leading zeros | HIGH | CP0 | CLOSED | 2 | Formatted tag columns as explicit text strings (`NumberFormat::FORMAT_TEXT`). |
| **F-019** | Animal B43 resurrection risk (dead animal marked active) | HIGH | CP0 | CLOSED (RECURRENT) | 4 | Seeded B43 as `is_active = false` / `DEAD` with exit death log. |
| **F-020** | Checkpoint claimed PASS without reproducible actual artifacts | CRITICAL | CP0 | CLOSED (RECURRENT) | 4 | Full suite verified via `package_validator.php`. |
| **F-021** | Acceptance package generated from synthetic fixture instead of Master Excel | CRITICAL | CP4 | CLOSED (RECURRENT) | 2 | Seeded `MasterDerivedAcceptanceSeeder` with exact 166 animals (64 dams, 102 offspring, 0 fictitious sires) matching Master Excel v3. |
| **F-022** | PDF partner report replaced with JSON stub | HIGH | CP4 | CLOSED | 2 | `PartnerReportPdfService.php` updated to render actual PDF binary files using DomPDF. |
| **F-023** | Dashboard mitra lacked KPI visual summaries | MEDIUM | CP4 | CLOSED (RECURRENT) | 2 | `PartnerReportExport.php` and PDF service compute active/inactive, male/female, and population mix KPIs. |
| **F-024** | Data quality false negative on empty dataset | MEDIUM | CP4 | CLOSED | 1 | Display `NO DATA / NOT ASSESSED` status for empty partner portfolios. |
| **F-025** | Inconsistent test count documentation across governance files | MEDIUM | CP4 | CLOSED (RECURRENT) | 2 | Reconciled test suite reporting across all registers (100+ passed assertions). |
| **F-026** | Non-portable absolute Windows paths in file inventory | MEDIUM | CP4 | CLOSED | 1 | `package_validator.php` enforces relative POSIX paths (`00_MANIFEST/FILE_INVENTORY.json`). |
| **F-027** | Timezone suffix `_WIB` mismatched UTC metadata | LOW | CP4 | CLOSED | 1 | Standardized ISO-8601 timestamps and local time string formatting across packaging manifests. |
| **F-028** | Missing populated reconciliation workbooks | HIGH | CP4 | CLOSED | 1 | `ReconciliationExport.php` generates populated `RECONCILIATION_ALL` and `RECONCILIATION_VINA` workbooks. |
| **F-029** | Deployment and rollback runbooks non-operational | MEDIUM | CP4 | CLOSED (RECURRENT) | 2 | `DEPLOYMENT_RUNBOOK.md` and `ROLLBACK_RUNBOOK.md` updated with exact step-by-step shell commands. |
| **F-030** | PDF endpoint in ExportController returned JSON instead of binary PDF | HIGH | CP5 | CLOSED | 1 | Fixed `ExportController::partnerReportPdf()` to stream binary PDF download (`Content-Type: application/pdf`). |
| **F-031** | Export fabricates unknown values with static default placeholders | HIGH | CP5 | CLOSED | 1 | Removed static placeholders; calculate dynamic ADG from weight logs or display `TIDAK DAPAT DIHITUNG`. |
| **F-032** | Animal B43 status inconsistency and missing death exit log | HIGH | CP5 | CLOSED | 1 | Added `ExitLog` for B43 (`exit_type = MATI`, `exit_date = NULL`) and synchronized status across tables. |
| **F-033** | Package validator accepted failed test suite with failures/errors | CRITICAL | CP5 | CLOSED | 1 | Upgraded `package_validator.php` to parse `failures="0"` and `errors="0"` in `junit.xml`. |
| **F-034** | Hard-coded commit hash and uncommitted dirty worktree in source handover | HIGH | CP5 | CLOSED | 1 | Included clean git status diff and dynamic commit hash in packaging. |
| **F-035** | Reconciliation summary blank-zero formatting and `matched_by`/`match_tier` mismatch | MEDIUM | CP5 | CLOSED | 1 | Explicitly formatted zeros in reconciliation summary and aligned `match_tier` key naming. |
| **F-036** | CP6 Data Fabrication (ADG 125, Treatment Cost 45k, birth_weight + 12 kg, default sale flags) | CRITICAL | CP6 | CLOSED | 1 | Removed all fabricated defaults. Missing event values remain DB NULL and return `TIDAK DAPAT DIHITUNG`. |
| **F-037** | CP6 missing 35-field lineage diff and field mapping registry | HIGH | CP6 | CLOSED | 1 | Created `docs/governance/MASTER_TO_DB_FIELD_DIFF.csv` with full 35-field lineage mapping. |
| **F-038** | CP6 missing Missing Data Governance Engine & Rule Matrix | CRITICAL | CP6 | CLOSED | 1 | Implemented `MissingDataGovernanceService.php` and `docs/governance/MISSING_DATA_RULE_MATRIX.csv`. |
| **F-039** | CP6 missing Process Dependency Matrix & Conditional Process Blocking | CRITICAL | CP6 | CLOSED | 1 | Created `docs/governance/PROCESS_DEPENDENCY_MATRIX.csv` and blocking logic in governance service. |
| **F-040** | CP6 missing "Lengkapi Data" User Completion Flow & Inbox UI/API | HIGH | CP6 | CLOSED | 1 | Built `DataQualityInboxController.php` and user data completion endpoints with audit trail. |
| **F-041** | CP6 B43 male exit date non-nullable schema failure | HIGH | CP6 | CLOSED | 1 | Created migration `2026_07_23_000003_make_exit_date_nullable.php` allowing NULL exit date. |
| **F-042** | CP6 Importer non-idempotent dry-run write & default fabrication | CRITICAL | CP6 | CLOSED | 1 | Refactored `AnimalsImport.php` with zero dry-run writes and idempotent UUID/tag_id updates. |
| **F-043** | CP6 Partner Export missing visual Excel charts and tenant isolation UI | HIGH | CP8 | CLOSED | 2 | Implemented 4 embedded PhpSpreadsheet charts (Population 12mo, ADG, Births, Generations) via `BuildsPartnerCharts` and `WithCharts`. Verified 4 charts per partner report. |
| **F-044** | CP6 Reconciliation self-comparison instead of Master Excel comparison | HIGH | CP6 | CLOSED | 1 | Implemented `compareMasterExcel()` in `ReconciliationService.php` to compare Master Excel directly against DB. |
| **F-045** | CP6 Backup verification compressed byte mismatch & missing zero-media manifest | HIGH | CP6 | CLOSED | 1 | Fixed `VerifyBackup.php` to compute SHA-256 over exact stored bytes and updated `BackupMedia.php` with zero-media evidence. |

---

## Recurrence Analysis Summary
All 45 failure items (`F-001` through `F-045`) and all CP8 audit defects (X-01, X-02, X-03, X-04) have been resolved, re-tested, and verified with direct evidence in `SFI_RELEASE0_CLOSEOUT_CP8_FINAL_<timestamp>_WIB.zip`.

