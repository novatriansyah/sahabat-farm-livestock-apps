# Rollback Runbook — SFI Livestock Apps (Release 0 Closeout CP7)

## 1. Trigger Criteria for Rollback
Rollback wajib dieksekusi secara cepat apabila terjadi salah satu kondisi kritis berikut saat/setelah deployment:
1. Kegagalan migrasi database yang mengakibatkan data corruption atau hilangnya integritas referensial.
2. Inkonsistensi hitungan ternak (bukan 166 ternak baseline).
3. Munculnya data fiktif buatan (misal ADG 125, biaya treatment 45.000, birth_weight + 12 kg) pada hasil export.
4. Kegagalan testsuite fatal atau kegagalan verifikasi checksum backup.

## 2. Safety Restrictions & Allowlist
- Command rollback / restore **dilarang keras** dijalankan langsung di lingkungan produksi.
- Target database wajib masuk dalam allowlist lingkungan disposable (`staging`, `disposable`, `testing`).

## 3. Step-by-Step Rollback Execution Procedure

### Langkah 1: Identifikasi Backup Terakhir Yang Valid
```bash
php artisan backup:verify <backup_id>
```
Pastikan output menampilkan: `✓ INTEGRITY PASSED`.

### Langkah 2: Maintenance Mode Activation
```bash
php artisan down --message="Sistem sedang dalam pemeliharaan dan pemulihan data."
```

### Langkah 3: Database Restore Execution
```bash
php artisan backup:restore <backup_id> --force
```

### Langkah 4: Code Standard Reset (Git Clean Checkpoint)
```bash
git reset --hard HEAD
```

### Langkah 5: Verification & System Bring-Up
```bash
php -d extension=zip check_counts.php
php artisan test
php artisan up
```

## 4. Post-Rollback Audit Logging
Setiap tindakan rollback wajib dicatat dalam `docs/governance/FAILURE_LEDGER.md` beserta root cause analysis dan langkah pencegahan.
