# Recovery Runbook — SFI Livestock Apps (Release 0 Closeout CP7)

## 1. Overview & Objectives
Dokumen ini mengatur prosedur pemulihan bencana (Disaster Recovery), integritas backup data, dan verifikasi checksum mandiri untuk database MySQL/SQLite dan media storage SFI.

## 2. Backup & Media Verification CLI Commands

### Command Backup Database (Streaming + SHA-256 Manifest)
```bash
php artisan backup:database --compress
```
- Menghasilkan file `database.sql.gz`, `database.sql.gz.sha256`, dan `manifest.json` di `storage/app/backups/<timestamp>/`.

### Command Verifikasi Integritas Backup
```bash
php artisan backup:verify <timestamp>
```
- Memverifikasi checksum SHA-256 dari stored bytes persis dan mendekode stream kompresi tanpa mengubah file sumber.

### Command Backup & Verifikasi Media (Photos/Docs)
```bash
php artisan backup:media
```
- Menghasilkan `manifest.json` berisi per-file SHA-256 checksum atau bukti `zero_media_evidence: true` apabila tidak ada media.

### Command Restore Safe Environment
```bash
php artisan backup:restore <timestamp> --force
```
- Memulihkan data pada database staging/testing terverifikasi.

## 3. Disaster Recovery Scenario Checklist

### Skenario A: Data Inconsistency / Missing Data Issue
1. Jalankan rekonsiliasi mandiri Master-to-DB:
   ```bash
   php -d extension=zip check_reconciliation.php
   ```
2. Evaluasi daftar `data_quality_issues` melalui Inbox Governance.
3. Jalankan `MasterDerivedAcceptanceSeeder` jika perbaikan master diperlukan:
   ```bash
   php artisan db:seed --class=MasterDerivedAcceptanceSeeder --force
   ```

### Skenario B: Complete Database Corruption
1. Pastikan backup terbaru terverifikasi via `php artisan backup:verify <timestamp>`.
2. Jalankan `php artisan backup:restore <timestamp> --force`.
3. Verifikasi ulang 166 ternak via `php -d extension=zip check_counts.php`.
