# PROGRESS — SFI Alignment Project

## Completed

### Phase 0: Repository Discovery ✅
- Report at `docs/superpowers/plans/2026-07-21-sfi-phase0-discovery.md`
- 28 models, 56 migrations, 14 bugs, 24 tasks confirmed

### Phase 1 Sheet Fixes ✅ (commit bad6c82)
- All 12 sheets: removed `is_active`/gender filters — exports ALL animals
- All forceText: replaced `="tag"` formula with `FORMAT_TEXT` column format
- Added ManifestSheet (schema version, record counts, SHA256 checksum)
- Added ManifestSheet as first sheet in AnimalMasterExport
- SummarySheet now counts ALL animals (active + inactive)

## In Progress — Phase 1 Revision

### 1A: Database + Media Backup
- [ ] Create `BackupDatabase` command (Hostinger-compatible, no exec(), no credentials in CLI)
- [ ] Generate SHA256 manifest per backup file
- [ ] Create `BackupMedia` command for storage/ backup
- [ ] Create `ListBackups` command

### 1B: Staging Restore Test
- [ ] Create `RestoreBackup` command
- [ ] Create `VerifyBackup` command (checksum validation)
- [ ] Document staging restore procedure

### 1C: Canonical Export (already fixed sheets, need manifest + zero-query template)
- [ ] Verify all animals export (B43 included as dead/inactive)
- [ ] Create zero-query blank template class (separate from export)
- [ ] Add manifest with actual record counts from production snapshot

### 1D: Reconciliation Redesign
- [ ] UUID match + active tag + tag history + composite identity
- [ ] 5 statuses: SAME, WEB_ONLY, EXCEL_ONLY, CONFLICT, UNCERTAIN
- [ ] Read-only by default (disable applyReconciliation)
- [ ] Batch ID + idempotency key
- [ ] Audit log + rollback support

### 1E: Blank Canonical Import Template
- [ ] Create separate zero-query template class
- [ ] Headers, schema, instructions, reference mapping
- [ ] No production data, no formulas

### Tests
- [ ] Unit tests for export sheets
- [ ] Feature tests for export controller
- [ ] Unit tests for reconciliation service
- [ ] Feature tests for reconciliation endpoint
- [ ] Authorization tests
- [ ] Checksum/manifest tests

### Acceptance Package
- [ ] SFI_PHASE1_CHECKPOINT_<timestamp>.zip with all deliverables
- [ ] TAHAP-1-LAPORAN.md
- [ ] TAHAP-1-SOURCECODE.md
- [ ] TAHAP-1-FEEDBACK.md

## Blockers
- No production cutover without `APPROVE_PRODUCTION_CUTOVER` token