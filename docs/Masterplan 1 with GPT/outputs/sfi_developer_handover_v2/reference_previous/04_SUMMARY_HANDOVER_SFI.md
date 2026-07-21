# Summary Handover Proyek SFI

## Tujuan bisnis

Sahabat Farm Indonesia ingin menghentikan master data paralel di Excel dan menjadikan web SFI sebagai satu-satunya source of truth yang dapat dipakai melalui HP dan laptop.

Scope sistem mencakup recording ternak, kelahiran, silsilah, bobot, kesehatan, breeding, kandang, mitra, inventory, pakan/vitamin, HPP, penjualan, proforma/invoice/pembayaran, dashboard, laporan, media Google Drive, user, role, CMS, dan pengaturan bisnis.

## Kondisi bukti saat handover

- [PASTI] Workbook utama memiliki 64 indukan dan 102 anakan; 101 anakan hidup, 1 mati; total ternak hidup 165.
- [PASTI] Ditemukan tiga formula Excel rusak, lima interval kelahiran mustahil, 13 jenis induk tidak sinkron, 11 nomor sementara, empat eartag history tanpa final, dan nol link media terisi.
- [PASTI] Tiga lampiran tersedia sebagai file kosong: template import, instruksi gap lanjutan, dan satu system summary.
- [PASTI] Source code dan database aktual belum disertakan dalam paket awal.
- [MENEBAK] Klaim Laravel/model/job/HPP dari rekomendasi lama belum dianggap terverifikasi.

## Keputusan desain kunci

1. Tidak boleh reset sebelum export lengkap, restore test, reconciliation, import dry-run, UAT, dan rollback lulus.
2. UUID ternak stabil; eartag adalah identifier yang dapat berubah.
3. Birth/litter dimodelkan sebagai event, bukan `litter_size` yang diulang pada setiap anak.
4. Age category dipisahkan dari reproductive, health, dan inventory status.
5. Generasi dihitung dari sire + dam dengan rule version dan confidence; unknown sire tidak boleh ditebak.
6. HPP memisahkan penanggung ekonomi, penerima alokasi, jenis biaya, basis alokasi, periode, dan eligibility.
7. Business-safe setting boleh dikelola frontend; secret/infrastructure/raw code tidak boleh diekspos.
8. Setiap perubahan penting mempunyai history, audit, effective date, dan rollback.

## Urutan kerja

1. Discovery read-only terhadap repository/database/sistem staging.
2. Release 0: backup, restore, Export Center, full portable export, reconciliation.
3. Release 1: canonical data model, Import Center, dry-run/idempotency.
4. Release 2: Data Quality Inbox dan temporary tag workflow.
5. Release 3–4: settings/RBAC/rule engine.
6. Release 5–7: sales, HPP/inventory, report/export.
7. Release 8–9: historical dashboard, media, offline, health, breeding.

## Aturan generasi terbaru

- Sire fullblood × dam lokal/garut/cross/merino/texel → F1.
- Sire fullblood × dam F1 → F2.
- Sire fullblood × dam F2 → F3; seterusnya F(n+1).
- Sire bukan fullblood × dam apa pun → CROSS DORPER.
- Sire tidak diketahui → PENDING CONFIRMATION.
- Fullblood × fullblood masih memerlukan keputusan terminologi/studbook.

## Aturan kategori umur default

- 0 hingga kurang dari 3 bulan: CEMPE.
- 3 hingga kurang dari 5 bulan: CEMPE SAPIH.
- 5 hingga kurang dari 8 bulan: DARA/BAKALAN menurut gender.
- 8 bulan ke atas: BETINA INDUKAN/JANTAN menurut gender.

Gunakan interval half-open dan perhitungan kalender. Jangan samakan label `BETINA INDUKAN` berdasarkan umur dengan bukti sudah pernah kawin/beranak; simpan status reproduksi terpisah.

## Definition of success

- Seluruh data dan histori dapat diekspor dari frontend dan dipulihkan.
- Round-trip export/import tidak kehilangan atau menggandakan data.
- Laporan antarformat mempunyai angka identik dan metadata periode/filter/source.
- Sistem web menjadi source of truth; Excel hanya alat export, audit, dan analisis.
- Owner dapat mengelola seluruh business-safe setting dari frontend dengan guardrail.
- Secret dan developer-only control tetap aman.
