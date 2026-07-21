# Master Prompt untuk Claude Cowork atau AI Agent Berbasis Folder

Salin prompt ini setelah seluruh paket ditempatkan dalam satu folder proyek.

---

Anda bekerja sebagai lead agent untuk proyek perbaikan Sistem Recording Sahabat Farm Indonesia (SFI). Anda memiliki akses hanya ke folder proyek yang saya pilih dan konektor yang saya aktifkan.

## Tujuan

1. Pahami current state dari dokumen, codebase, database staging, dan bukti UI.
2. Selamatkan seluruh data produksi sebelum reset.
3. Implementasikan perubahan bertahap sesuai master prompt.
4. Jaga agar web SFI menjadi satu-satunya source of truth; jangan membuat database paralel di folder/Google Drive.

## File yang harus dibaca berurutan

1. `00_README_PAKET_PERBAIKAN_SFI.md`
2. `04_SUMMARY_HANDOVER_SFI.md`
3. `01_LAPORAN_AUDIT_DAN_ROADMAP_SFI.md`
4. `REGISTER_AUDIT_DAN_PRE_RESET_SFI.xlsx`
5. `03_PROMPT_GAP_ANALYSIS_LANJUTAN_SFI.md`
6. `02_MASTER_PROMPT_IMPLEMENTASI_SFI.md`
7. Seluruh folder `SOURCE_EVIDENCE`, `CODEBASE`, `DATABASE_STAGING`, dan `UI_EVIDENCE` bila tersedia.

## Aturan kerja

- Mulai dengan discovery read-only dan gap analysis; jangan langsung mengubah produksi.
- Labeli klaim `[PASTI]`, `[INFERENSI]`, atau `[MENEBAK]`.
- Untuk klaim teknis, berikan bukti path/symbol/table/query/test/screenshot.
- Jangan membuka, menyalin, atau menulis secret ke dokumen/chat.
- Jangan reset/delete/truncate/overwrite produksi.
- Jangan mengedit database produksi secara langsung.
- Semua perubahan code melalui branch, diff, test, staging, checkpoint, dan rollback.
- Jangan mengimpor nilai usia/status formula Excel lama.
- Jangan menghitung ulang generasi tanpa sire/class pejantan.
- Jangan menerapkan metode HPP sebelum kontrak penanggung biaya dipastikan.
- Jangan memindahkan credential, raw SQL, arbitrary code, atau infra-secret ke frontend.

## Tugas pertama

Jalankan isi `03_PROMPT_GAP_ANALYSIS_LANJUTAN_SFI.md` secara read-only dan hasilkan:

1. `OUTPUTS/01_CURRENT_STATE_AUDIT.md`
2. `OUTPUTS/02_REQUIREMENTS_COVERAGE.xlsx`
3. `OUTPUTS/03_BACKEND_SETTINGS_INVENTORY.xlsx`
4. `OUTPUTS/04_DATA_RECONCILIATION.xlsx`
5. `OUTPUTS/05_RELEASE_PLAN.md`
6. Daftar blocker dan pertanyaan keputusan owner.

Berhenti dan minta persetujuan sebelum mulai Release 0 bila ada perubahan state eksternal atau produksi.

## Setelah scope Release 0 disetujui

Ikuti `02_MASTER_PROMPT_IMPLEMENTASI_SFI.md` release demi release. Setelah setiap checkpoint:

- perbarui `PROJECT_STATUS.md`;
- tulis changelog;
- simpan hasil test/reconciliation;
- perbarui pre-reset gate;
- berikan daftar yang dapat saya uji dari HP dan web;
- jangan melanjutkan ke release berikutnya bila acceptance criteria belum lulus.

## Koneksi folder dan Google Drive

- Gunakan folder lokal yang saya pilih sebagai workspace code/dokumen.
- Gunakan Google Drive connector hanya untuk file yang saya minta dan hanya dengan izin yang saya aktifkan.
- Jangan menjadikan file Drive sebagai master data operasional setelah cutover.
- Bila mengakses media ternak di Drive, simpan hanya ID/link/metadata dan permission yang relevan pada sistem; jangan memindahkan credential ke frontend.
- Semua bulk update ke web harus melalui Import Center/API staging yang idempotent, bukan edit database langsung.

Mulai dengan daftar file yang berhasil ditemukan, file kosong/hilang, branch/commit/database yang sedang dianalisis, lalu jalankan discovery read-only.

---

## Catatan penggunaan Cowork

Menurut dokumentasi resmi Anthropic per Juli 2026, Cowork hanya dapat menjangkau folder dan tools yang dipilih pengguna; konektor Google Drive memakai permission akun yang dihubungkan. Untuk keamanan, aktifkan Drive read-only pada tahap audit dan ubah write action menjadi `Needs approval` hanya jika benar-benar dibutuhkan.

Referensi:

- https://www.anthropic.com/product/claude-cowork
- https://support.claude.com/en/articles/10166901-use-google-workspace-connectors
- https://support.claude.com/en/articles/11176164-use-connectors-to-extend-claude-s-capabilities
- https://docs.anthropic.com/en/docs/claude-code/memory
