# Paket Eksekusi Developer SFI — 21 Juli 2026

## Keputusan utama

[PASTI] Jangan reset atau mengunggah `IMPORT_TERNAK_SFI_siap_upload.xlsx` ke produksi sekarang. File tersebut memuat 166 tag yang benar, tetapi hanya 12 field dan akan menghilangkan relasi induk-anak, status kematian, histori, media, serta 57 catatan kualitas data.

## Cara menggunakan

1. Berikan **folder/ZIP ini secara utuh** kepada developer dan buka repository SFI di Gemini Antigravity.
2. Tempel isi `01_MASTER_PROMPT_GEMINI_ANTIGRAVITY_END_TO_END.md` sebagai instruksi utama.
3. Izinkan Antigravity membaca repository dan clone database staging sesuai hak akses, bukan secret produksi dalam chat.
4. Minta developer menyerahkan seluruh output pada struktur `expected_developer_outputs/` dan paket source handover yang dijelaskan dalam file `05`.
5. Review `OPEN_DECISIONS.md` sekali secara terkonsolidasi. Bagian lain harus tetap dikerjakan tanpa menunggu keputusan yang tidak terkait.
6. Uji staging. Jangan mengirim token `APPROVE_PRODUCTION_CUTOVER` sebelum backup, restore, reconciliation, UAT, rollback, dan source handover lulus.

## Isi paket

- `01_MASTER_PROMPT_GEMINI_ANTIGRAVITY_END_TO_END.md` — satu perintah end-to-end untuk audit, coding, QA, handover, dan persiapan go-live.
- `02_FEEDBACK_DEVELOPER_DAN_ACCEPTANCE_CRITERIA.md` — feedback langsung dan daftar bukti yang wajib dikembalikan developer.
- `03_SUMMARY_SISTEM_SFI_CURRENT_STATE.md` — ringkasan sistem untuk onboarding AI/developer lain.
- `04_AUDIT_TEMPLATE_IMPORT_DAN_PRE_RESET.md` — validasi template terbaru dan konsekuensi migrasi.
- `05_MANIFEST_HANDOVER_SOURCE_CODE_DAN_DATABASE.md` — isi source/repository/database/runbook yang harus diserahkan secara transparan.
- `06_BACKLOG_FASE_DEVELOPMENT_SELANJUTNYA.md` — prioritas rilis dan items fase berikutnya.
- `source_inputs/` — workbook dan dokumen input asli yang tersedia.
- `reference_previous/` — audit/prompt/register putaran sebelumnya.

## Batas bukti paket ini

- [PASTI] Workbook master dan template terbaru telah diperiksa.
- [TERLAPOR] Arsitektur Laravel, model, controller, rumus HPP, dan scheduler berasal dari summary yang diberikan, belum diverifikasi terhadap repository saat ini.
- [PASTI] Paket ini belum berisi source code aplikasi atau database aktual karena keduanya belum diberikan.
- Source code tidak boleh diminta secara terselubung. Developer harus menyerahkannya sebagai deliverable handover/disaster recovery yang diketahui pemilik dan sesuai kewenangan.

