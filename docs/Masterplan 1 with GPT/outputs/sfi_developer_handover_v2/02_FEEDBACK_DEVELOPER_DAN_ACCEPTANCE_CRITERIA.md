# Feedback untuk Developer SFI dan Acceptance Criteria

## Pesan utama

Sistem tidak boleh di-reset hanya karena template 166 baris sudah tersedia. Template saat ini tidak mampu membangun kembali histori dan relasi yang diperlukan agar web menjadi source of truth.

## Feedback prioritas P0 — wajib sebelum perubahan produksi

1. **Source dan deployment handover.** Serahkan repository/bundle lengkap, migration, lockfile, test, asset, konfigurasi nonrahasia, scheduler, deployment dan rollback runbook. Lakukan secret scan dan clean-room build.
2. **Backup yang dapat dipulihkan.** Buat snapshot database/media, checksum, dan restore ke staging. Screenshot “backup selesai” tanpa restore test tidak cukup.
3. **Export Center lengkap.** Export wajib mencakup stable UUID, current data, event/history, relasi, HPP, inventory, sales, media link, settings version, dan audit trail; bukan hanya tabel `animals`.
4. **Rekonsiliasi.** Bandingkan count, sum, orphan, duplicate, dan relasi pada sumber → export → hasil restore → hasil import.
5. **Canonical import v2.** Buat template/ZIP berversi yang membawa events/history dan data-quality flags. Adapter 12 kolom hanya untuk legacy dan harus menampilkan kehilangan data.
6. **Data Quality Inbox.** Migrasikan catatan asumsi sebagai issue terstruktur. Jangan meminta pengguna menghapus catatan sebelum upload.
7. **B43.** Pastikan satu ternak mati tidak menjadi ternak aktif setelah migrasi.
8. **Temporary tag.** UUID internal harus stabil; 11 tag sementara menjadi task/notifikasi dan history saat diganti.
9. **Valuation vs cost.** Jangan memasukkan estimated asset value ternak hasil kelahiran sebagai `purchase_price` tanpa policy yang disetujui. Pisahkan acquisition cost, accumulated HPP, estimated/fair value, list price, dan sale price.
10. **Weight semantics.** Bedakan birth/current/entry/weaning/sale weight, tanggal timbang, actual/estimated, sumber, dan recorder.

## Feedback P1 — fungsi yang harus usable lebih dulu

- Filtered animal export dan full backup dari frontend.
- Import preview/dry-run/idempotency/rollback batch.
- Link Google Drive/media per ternak dan per event.
- Business-safe settings di frontend dengan type validation, version, effective date, audit, preview, dan rollback.
- Custom role/permission dengan server-side scope; jangan mengekspos secret atau raw code/SQL.
- Generation rule engine sesuai aturan terbaru dan unknown sire → pending confirmation.
- Age-category engine 0–3, 3–5, 5–8, ≥8 bulan, terpisah dari reproductive/health/inventory status.
- Report Center dengan filter periode/mitra/field, metadata data-as-of, dan angka konsisten antar XLSX/CSV/PDF/PPTX/image.

## Feedback P2 — modul transaksi

- Sales state machine: draft, proforma, reservation, DP, payment, invoice, cancel, refund/credit note; transactional stock release dan pencegahan double sale.
- Inventory ledger dan unit conversion; hindari stok negatif.
- HPP policy/cutoff ledger; allocation basis tergantung cost type dan economic bearer, bukan satu rumus untuk semua.
- Historical HPP per ternak/bulan dengan drill-down dan reversal, bukan edit langsung nilai posted.

## Evidence yang wajib dikembalikan

| Area | Bukti minimum |
|---|---|
| Current state | route list, model/table/ERD, setting/hard-code inventory, role matrix, scheduler/queue |
| Source handover | bundle/zip, commit, file manifest, SHA-256, exclusions, secret-scan report |
| Restore | backup ID, checksum, clean staging target, restore log, smoke test |
| Import/export | schema version, manifest, row counts, dry-run report, idempotency, rollback proof |
| Data | 166 total, 165 hidup, B43 mati, 11 temporary tag, parent/event/orphan checks |
| QA | unit/integration/permission/concurrency/UAT results dan screenshot HP/desktop |
| Deployment | environment prerequisites, deploy/rollback commands, cron/queue/storage, monitoring |
| Handover | updated summary, feedback, changelog, open decisions, known issues, next roadmap |

## Acceptance gate

Status hanya boleh `LULUS` bila ada bukti yang dapat diulang. `Belum diuji`, `seharusnya`, atau `sudah dibuat` tanpa hasil test adalah `BELUM LULUS`.

Cutover produksi tidak boleh dijalankan tanpa token pemilik `APPROVE_PRODUCTION_CUTOVER`, nama environment, backup ID, commit, migration target, dan rollback point yang eksplisit.

