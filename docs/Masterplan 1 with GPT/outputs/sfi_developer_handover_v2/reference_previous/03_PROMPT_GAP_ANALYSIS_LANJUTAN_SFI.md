# Prompt Gap Analysis Lanjutan SFI — Read Only

Gunakan prompt ini lebih dulu bila AI pembangun sistem belum pernah membaca source code dan database aktual.

---

Anda bertindak sebagai Software Architect, Laravel/PHP Reviewer, Database Auditor, Security Reviewer, QA Lead, Product Analyst, dan Livestock Data Model Reviewer.

Lakukan **gap analysis read-only** terhadap sistem SFI yang sudah berjalan. Jangan mengubah code, database, konfigurasi, file, atau produksi.

## Input wajib

- Repository/commit aktif.
- Database migrations/schema dan dump staging/anonymized.
- Route list, scheduler/cron, queue, storage, env example tanpa secret.
- Screenshot/recording menu per role.
- Sample export/import/proforma/invoice/report.
- Dokumen audit SFI dan workbook master.

Jika ada input yang tidak tersedia, beri label `[BLOCKER]`; jangan mengisi dengan asumsi.

## Metode kerja

1. Catat teknologi dan versi aktual.
2. Jalankan test existing dan lint/static analysis yang sudah ada tanpa menambah dependency sembarangan.
3. Petakan seluruh module end-to-end: UI → route → policy → validation → service/action → model/table → job/observer → report/export.
4. Petakan setiap business rule hard-coded dan setiap setting backend-only.
5. Query integritas database staging: count, null, duplicate, orphan FK, enum invalid, state inconsistency, audit coverage.
6. Bandingkan implementasi terhadap `02_MASTER_PROMPT_IMPLEMENTASI_SFI.md`.
7. Untuk setiap gap, berikan bukti file/path/symbol/table/query/screenshot.
8. Bedakan `[PASTI]`, `[INFERENSI]`, dan `[MENEBAK]`.

## Pertanyaan audit minimum

### Data safety

- Apakah backup konsisten tersedia dan pernah diuji restore?
- Apakah semua table/file/media dapat dipulihkan?
- Apakah export memiliki stable ID, history, schema version, manifest, checksum, dan reconciliation?
- Apakah export mengecualikan password/token/secret?
- Apakah import dry-run, idempotent, resumable, dan rollbackable?

### Data model

- Apakah eartag dipakai sebagai PK atau natural key yang berisiko?
- Bagaimana birth event/litter dimodelkan?
- Apakah dam/sire dan confidence tersedia?
- Apakah weight birth/weaning/mating dapat dibedakan?
- Apakah ownership/location/status/tag memiliki history efektif?
- Nilai turunan mana yang disimpan manual dan berpotensi stale?

### Business rule

- Di mana rule generation, age category, eartag color, breeding eligibility, status, HPP, dan cutoff berada?
- Apakah rule memiliki version/effective date/audit/rollback?
- Apakah aturan generasi sesuai matrix sire-fullblood yang diberikan user?
- Apakah unknown sire menghasilkan pending, bukan tebakan?
- Apakah age category terpisah dari reproductive/health/inventory status?

### HPP/inventory

- Trace satu transaksi pembelian → stok → pemakaian → cost pool → allocation → HPP ternak → sale margin.
- Apa dasar alokasi per cost type?
- Bagaimana ternak lahir/mati/terjual di tengah periode diperlakukan?
- Bagaimana cempe menyusu diperlakukan?
- Apakah penanggung ekonomi biaya terpisah dari penerima alokasi?
- Apakah posted ledger immutable dan correction memakai reversal?

### Sales

- Apakah ada proforma, reservation, expiry, partial payment, invoice, cancellation, refund?
- Apakah stock bisa double-reserved/double-sold?
- Apa side effect setiap status dan apakah transactional?
- Apakah override harga/discount/tax/audit tercatat?

### Frontend/settings/RBAC

- Setting apa yang aman untuk frontend, high-impact, secret, atau developer-only?
- Apakah role custom dan permission scope diuji server-side?
- Apakah admin terakhir/owner role terlindungi?
- Apakah UI mobile, accessibility, error recovery, bulk action, filter, sort, search, pagination memadai?

### Report/export

- Apakah filter periode/mitra/kolom tersedia?
- Apakah PDF/PPTX/image dan data export menggunakan query snapshot yang sama?
- Apakah output mencantumkan filter, period, timezone, generated-at, source/version?
- Apakah historical dashboard memakai event/snapshot atau current state saja?

### Operasional/security

- Apakah queue/cron benar-benar berjalan di Hostinger?
- Apakah job idempotent dan mempunyai failure monitoring?
- Apakah CSRF, authorization, validation, upload security, rate limit, audit, encryption, backup, retention memadai?
- Apakah credential atau raw backend control terekspos?

## Output wajib

### A. Executive summary

- 10 risiko tertinggi dengan impact, likelihood, evidence, dan tindakan.
- keputusan `GO/NO-GO` untuk reset.

### B. Current-state map

- architecture/module map;
- database ERD ringkas;
- role/permission matrix;
- data lifecycle dari lahir sampai exit/sale;
- cost lifecycle dan sales lifecycle.

### C. Requirements coverage matrix

Kolom:

`REQ-ID | Requirement | Current State | Evidence | Coverage (Full/Partial/Missing/Broken) | Severity | Dependency | Recommended Fix | Acceptance Criteria | Test`

### D. Backend-only settings inventory

Kolom:

`Setting | Current Location | Type | Default | Consumer | Frontend Classification | Validation | Risk | Versioning Need | Recommendation`

### E. Data quality report

- count per entity/state/owner;
- null/duplicate/orphan/state inconsistencies;
- workbook vs database differences;
- unresolved records dan evidence yang dibutuhkan.

### F. Migration/export plan

- canonical schema;
- mapping source → target;
- import order;
- dry-run rules;
- reconciliation queries;
- rollback;
- pre-reset gate.

### G. Roadmap

- Release 0 safety/export;
- Release 1 data/import;
- Release 2 frontend/rules;
- Release 3 sales/HPP/report;
- Release 4 breeding/offline/optimization.

Berikan dependency dan risk-based order. Jangan memberi estimasi hari tanpa mengukur codebase/test/infra.

### H. Open decisions

Daftar pertanyaan yang benar-benar membutuhkan keputusan owner, terutama:

- kontrak penanggung biaya dan bagi hasil;
- fullblood × fullblood;
- pajak/ongkir/discount/refund;
- laporan dan audience;
- privacy dan media visibility;
- policy koreksi HPP historis.

Berhenti setelah menyerahkan gap analysis. Jangan melakukan coding sebelum owner menyetujui scope Release 0.

---
