# PROGRESS.md — SFI System Alignment

## Latest: 2026-07-21 — Test fixes for ReconciliationService

### Completed
- ✅ Master plan analysis (Masterplan 1 GPT + Masterplan 2 Claude vs codebase)
- ✅ Implementation plan created: `docs/superpowers/plans/2026-07-21-sfi-alignment-plan.md`
- ✅ ReconciliationService + Export feature built:
  - Multi-sheet animal export (Indukan, Anakan, WeightHistory, etc.)
  - Blank import template
  - Two-way reconciliation (diff view, batch tracking)
  - Reconciliation UI (index + diff blade views) — fixed `layouts.admin` → standalone views
  - Routes wired under `admin/export/*`
- ✅ Backup commands: `BackupDatabase`, `ListBackups`, `VerifyBackup`, `RestoreBackup`, `BackupMedia`
- ✅ BackupCommandTest **6/6 passing**
- ✅ ReconciliationServiceTest **9/9 passing**
- ✅ ReconciliationTest **6/6 passing**

### Pending (from masterplan alignment)
- Generation rule configurable (`master_generation_rules`)
- Age categories configurable (`master_age_categories`)
- HPP split by partner (metabolic weight allocation)
- Sire auto-inference from mating colony
- Dynamic RBAC (roles/permissions tables)
- Sales module (proforma invoices)
- Feed/medicine purchase & usage tracking
- All parameters frontend-configurable (45 parameters)
- Audit trail (laravel-auditing)
- Pending tag assignments + WhatsApp notifications
- Anti-inbreeding checks
- Animal age category migration (62% change expected)

### Known pre-existing test failures
- `ArticleAndCmsTest` — 4 failures (unrelated to our changes)
  - `admin can create article`
  - `admin can update article`
  - `admin can upload media quill`
  - `admin can update site settings cms`

### Notes
- Animals table uses Indonesian enum values (`BETINA`, `JANTAN`, `HASIL_TERNAK`, `SEHAT`)
- `purchase_price` and `sale_price` are NOT NULL with 0 default
- Ear tag logs use auto-increment `id`, not UUID
- ReconciliationService now skips null/empty upload fields to avoid false conflicts