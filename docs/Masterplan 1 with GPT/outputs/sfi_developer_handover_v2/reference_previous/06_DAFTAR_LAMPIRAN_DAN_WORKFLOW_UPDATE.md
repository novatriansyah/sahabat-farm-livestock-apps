# Daftar Lampiran dan Workflow Update Proyek SFI

## 1. Struktur folder portabel

```text
SFI_SYSTEM_PROJECT/
├── 00_README_PAKET_PERBAIKAN_SFI.md
├── PROJECT_STATUS.md
├── CLAUDE.md
├── 01_CONTEXT/
│   ├── 04_SUMMARY_HANDOVER_SFI.md
│   ├── 01_LAPORAN_AUDIT_DAN_ROADMAP_SFI.md
│   └── REGISTER_AUDIT_DAN_PRE_RESET_SFI.xlsx
├── 02_REQUIREMENTS/
│   ├── 02_MASTER_PROMPT_IMPLEMENTASI_SFI.md
│   ├── 03_PROMPT_GAP_ANALYSIS_LANJUTAN_SFI.md
│   └── 05_MASTER_PROMPT_CLAUDE_COWORK_SFI.md
├── 03_SOURCE_EVIDENCE/
│   ├── SFI_MASTER_TERNAK_v3.xlsx
│   ├── IMPORT_TERNAK_SFI_siap_upload.xlsx
│   ├── system_summary.md
│   ├── gap_analysis_lama.md
│   ├── rekomendasi_lama.md
│   ├── examples_reports/
│   ├── ui_recordings/
│   └── wa_birth_evidence/
├── 04_CODEBASE/
│   └── repository atau clone Git aktif
├── 05_DATABASE_STAGING/
│   ├── schema.sql
│   ├── anonymized_dump.sql
│   ├── counts_before.json
│   └── restore_test_log.md
├── 06_EXPORTS/
│   ├── production_export_timestamp.zip
│   ├── manifest.json
│   └── reconciliation.xlsx
└── 07_OUTPUTS/
    ├── current_state_audit.md
    ├── coverage_matrix.xlsx
    ├── test_results/
    └── release_checkpoints/
```

## 2. Isi `CLAUDE.md`/project instruction

```markdown
# SFI Project Rules
- Read 00_README and 04_SUMMARY_HANDOVER before working.
- Web SFI is the target source of truth; do not create a parallel master database.
- Never modify production or secrets directly.
- Start read-only; use branch + staging + tests + rollback.
- Do not reset until every pre-reset gate is LULUS.
- Label important claims PASTI/INFERENSI/MENEBAK and cite file/table/test evidence.
- Update PROJECT_STATUS.md after each checkpoint.
```

## 3. Lampiran yang harus dibawa ke AI lain

### Wajib sekarang

- Seluruh tujuh file hasil paket ini.
- `SFI_MASTER_TERNAK_v3(1).xlsx`.
- Gap analysis dan rekomendasi lama.

### Wajib sebelum technical implementation

- Source code/repository aktif dan commit hash.
- Schema database serta dump staging/anonymized.
- Template import aktual non-kosong.
- Export data produksi lengkap saat ini.
- Screen recording setiap menu untuk setiap role.
- Daftar config/settings/hard-coded constants.
- Hasil test dan deployment/rollback runbook.

### Wajib sebelum sales/HPP

- Kontrak kemitraan dan pembagian biaya/bagi hasil.
- Contoh proforma, invoice, DP, pelunasan, pembatalan, refund.
- Aturan pajak, ongkir, discount, price override, invoice numbering.
- Inventory master, unit conversion, receipt, usage, batch, expiry.

### Wajib untuk kurasi data

- Export produksi sebelum reset.
- Bukti kelahiran WA/foto untuk lima interval konflik.
- Bukti nomor untuk 11 ternak bertag sementara.
- Konfirmasi empat rantai eartag tanpa tag final.
- Data sire/koloni kawin bila tersedia.
- Link/folder Google Drive per ternak/event.

## 4. Workflow update setelah sistem menjadi source of truth

### Input harian

- Kelahiran, timbang, treatment, pakan, movement, dan kematian dimasukkan melalui HP/web.
- Bila offline, input masuk queue lokal dengan idempotency key lalu sinkron otomatis.
- Excel tidak lagi diedit sebagai master.

### Media

- Foto/video diunggah atau ditautkan dari form ternak/event.
- Sistem menyimpan Drive file/folder ID, URL, tipe media, tanggal, uploader, visibility, dan related entity.
- Credential OAuth/service account tetap server-side.

### Rekonsiliasi rutin

- Harian: failed sync, duplicate tag, orphan event, negative inventory.
- Mingguan: temporary tag aging, data-quality issue, health due, unpaid DP.
- Bulanan: inventory cutoff, HPP preview/review/post, report mitra, full export, backup verification.
- Kuartalan: restore drill, role audit, setting audit, rule-version review.

### Update melalui Cowork/AI project

Cowork digunakan untuk:

- audit dan analisis export;
- pembersihan bulk data secara terkontrol;
- membuat template/report/test;
- mengubah code melalui branch;
- menyiapkan import batch ke staging.

Cowork tidak digunakan untuk:

- mengedit database produksi langsung;
- menyimpan password/token;
- menjadi master data ternak paralel;
- melakukan reset tanpa pre-reset gate.

### Jika ingin update data melalui percakapan AI

Gunakan alur:

1. AI menerima rincian atau file.
2. AI menormalkan dan menampilkan preview diff.
3. User mengonfirmasi batch.
4. AI mengirim melalui API/Import Center staging atau produksi sesuai permission.
5. Sistem mengembalikan batch ID, created/updated/conflict/skipped.
6. AI menyimpan reconciliation report; bukan salinan master baru.

API harus memakai service account terbatas, scoped permission, idempotency key, validation, audit, rate limit, dan approval untuk bulk/high-impact update.

## 5. PROJECT_STATUS template

```markdown
# PROJECT STATUS
Updated at:
Environment:
Branch/commit:

## Current release

## Completed

## In progress

## Blockers / owner decisions

## Test and reconciliation results

## Pre-reset gate status

## Next safe action
```
