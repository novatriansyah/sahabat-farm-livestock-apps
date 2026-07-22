# COMPARISON_TO_PREVIOUS.md — SFI Release 0 Closeout / Phase 1.1

**Baseline State**: `SFI_PHASE1_CHECKPOINT_CORRECTED_20260722` (Rejected)  
**Final Candidate State**: `CP3_FINAL_CANDIDATE` (Release 0 Closeout Complete)

| Item / Feature | Baseline Status (`SFI_PHASE1_CORRECTED`) | Final Status (`Release 0 Closeout`) | Key Evidence / Deliverable |
| :--- | :--- | :--- | :--- |
| **Canonical Export Endpoint** | ❌ FAILED (`TypeError` on array filter argument) | ✅ PASSED (Filterless, PEMILIK only, sidecar SHA-256) | `AnimalMasterExport.php`, `T-001`, `T-002` |
| **Export Architectural Separation** | ❌ FAILED (Single class attempted all functions) | ✅ PASSED (3 separate products: Canonical, Import, Partner) | `ExportController.php`, `T-002`, `T-006`, `T-013` |
| **Schema Uniformity** | ❌ FAILED (28 export cols vs 25 template cols) | ✅ PASSED (Shared `AnimalTemplateSchema` data dictionary) | `AnimalTemplateSchema.php`, `T-006` |
| **Partner Scope & Isolation** | ❌ FAILED (No per-partner export / UI selection) | ✅ PASSED (Dropdown UI, server-side `PartnerScopeTrait`) | `PartnerScopeTrait.php`, `T-009`, `T-010` |
| **Partner Report & Dashboard** | ❌ FAILED (Missing report controller & XLSX/PDF) | ✅ PASSED (9-sheet XLSX + PDF with preliminary HPP label) | `PartnerReportExport.php`, `T-013` |
| **Tag Cell Formatting** | ❌ FAILED (Used Excel formula `="036"`) | ✅ PASSED (Explicit string cell preserving `"010"`, `"036"`) | `AnimalTemplateSchema.php`, `T-007` |
| **Reconciliation Engine** | ❌ FAILED (Missing methods, empty file `TypeError`) | ✅ PASSED (`getBatches`, `getBatchDiff`, 5 statuses, union math) | `ReconciliationService.php`, `T-015`..`T-019` |
| **Backup Compression Stream** | ❌ FAILED (Chunked `gzencode` stream mismatch) | ✅ PASSED (Single Gzip stream, exact stored-bytes checksum) | `BackupDatabase.php`, `T-021` |
| **Restore Safety Guard** | ❌ FAILED (Assumed atomic DDL rollback in MySQL) | ✅ PASSED (Fail-fast tokenizer, disposable DB guard, FK check) | `RestoreBackup.php`, `T-023`, `T-024` |
| **Test Evidence Quality** | ❌ FAILED (Source inspection/mock claims only) | ✅ PASSED (78 passing automated tests on final commit) | `REQUIREMENT_TEST_MATRIX.md`, `T-001`..`T-030` |
| **Acceptance Package ZIP** | ❌ FAILED (Missing actual workbooks & backups) | ✅ PASSED (Complete ZIP with actual workbooks, PDFs, manifest) | `SFI_RELEASE0_CLOSEOUT_PARTNER_EXPORT_<timestamp>.zip` |
