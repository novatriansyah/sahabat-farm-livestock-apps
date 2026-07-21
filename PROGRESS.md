# PROGRESS — SFI Alignment Project

## Completed

### Phase 0: Repository Discovery ✅
- Report at `docs/superpowers/plans/2026-07-21-sfi-phase0-discovery.md`
- 28 models, 56 migrations, 14 bugs, 24 tasks confirmed

## In Progress — Phase 1 Revision

### 1A: Database + Media Backup
- [ ] Create `BackupDatabase` command (no exec(), no credentials in CLI)
- [ ] Generate SHA256 manifest per backup file
- [ ] Create `BackupMedia` command for storage/ backup
- [ ] Create `ListBackups` command to show available backups

### 1B: Staging Restore Test
- [ ] Create `RestoreBackup` command
- [ ] Create `VerifyBackup` command (checksum validation)
- [ ] Document staging restore procedure

### 1C: Canonical Export (fix AnimalMasterExport)
- [ ] Remove `is_active=true` and gender filters — include ALL animals
- [ ] Create zero-query template class
- [ ] Add manifest sheet (schema version, record counts, SHA256)
- [ ] Fix force-text: use format column, not Excel formula
- [ ] Add `gdrive_folder_url` to all relevant sheets

### 1D: Reconciliation Redesign
- [ ] UUID match + active tag + tag history + composite identity
- [ ] 5 statuses: WEB_ONLY, EXCEL_ONLY, SAME, CONFLICT, UNCERTAIN
- [ ] Batch ID + idempotency key
- [ ] Audit log + rollback support
- [ ] Read-only by default

### 1E: Tests
- [ ] Unit tests for export sheets
- [ ] Feature tests for export controller
- [ ] Unit tests for reconciliation service
- [ ] Feature tests for reconciliation endpoint

### Deliverables (per requirement 13)
- [ ] TAHAP-1-LAPORAN.md
- [ ] TAHAP-1-SOURCECODE.md
- [ ] TAHAP-1-FEEDBACK.md
- [ ] Full backup manifest + checksum
- [ ] Successful staging restore evidence
- [ ] Canonical export file
- [ ] Reconciliation report
- [ ] Test output
- [ ] Commit hash

## Blockers
- No production cutover without `APPROVE_PRODUCTION_CUTOVER` token