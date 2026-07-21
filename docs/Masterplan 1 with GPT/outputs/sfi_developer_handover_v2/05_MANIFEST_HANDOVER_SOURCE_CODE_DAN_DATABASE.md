# Manifest Handover Source Code, Database, dan Operasional

## Status saat paket ini dibuat

[PASTI] Source code aplikasi dan database aktual **belum tersedia** dalam lampiran. Dokumen ini adalah kontrak deliverable yang harus diisi developer secara terbuka setelah menjalankan master prompt.

## Struktur output wajib

```text
SFI_HANDOVER_<timestamp>/
├── source_code/
│   ├── application source lengkap
│   ├── composer.json + composer.lock
│   ├── package.json + lockfile
│   ├── tests/
│   ├── .env.example (tanpa values rahasia)
│   └── build/deploy scripts
├── repository/
│   ├── repository.bundle
│   ├── HEAD_commit.txt
│   ├── branches_tags.txt
│   ├── git_status.txt
│   └── uncommitted_changes.patch
├── database/
│   ├── migrations_seeders/
│   ├── schema_only.sql
│   ├── ERD_and_data_dictionary.md
│   ├── index_fk_constraint_report.md
│   └── secure_backup_reference.md
├── operations/
│   ├── ENVIRONMENT_VARIABLE_NAMES.md
│   ├── DEPLOYMENT_RUNBOOK.md
│   ├── ROLLBACK_RUNBOOK.md
│   ├── BACKUP_RESTORE_RUNBOOK.md
│   ├── CRON_QUEUE_STORAGE.md
│   └── MONITORING_INCIDENT_RECOVERY.md
├── system_inventory/
│   ├── SYSTEM_SUMMARY_CURRENT.md
│   ├── ROUTES_AND_PERMISSIONS.md
│   ├── MODELS_TABLES_RELATIONSHIPS.md
│   ├── SETTINGS_AND_HARDCODED_RULES.md
│   ├── DEPENDENCIES_AND_LICENSES.md
│   └── KNOWN_ISSUES_AND_TECH_DEBT.md
├── verification/
│   ├── SECRET_SCAN_REPORT.md
│   ├── TEST_REPORT.md
│   ├── CLEAN_ROOM_RESTORE_TEST.md
│   ├── RECONCILIATION_REPORT.md
│   └── UAT_REPORT.md
└── MANIFEST.json
```

## Include minimum

Semua custom source, view, migration, seeder, config nonrahasia, public custom assets, translations, tests, job/command, service, policy, observer, import/export, report template, deployment script, schema, dan dependency lockfile yang diperlukan untuk build.

## Exclude minimum

`.env`, passwords, tokens, keys, session/cookie, credential file, private production dump dalam paket umum, personal data yang tidak diperlukan, `vendor/`, `node_modules/`, build cache, runtime log, temporary upload, dan backup lama yang tidak terkontrol.

Exclusion tidak boleh digunakan untuk menahan custom source atau migration. Setiap exclusion dicatat dengan reason.

## `MANIFEST.json` minimum

- project/system name;
- generated_at dan timezone Asia/Jakarta;
- producer dan authorized recipient;
- commit, branches/tags, dirty status;
- PHP/MySQL/Node/framework versions;
- migration state;
- file count/size dan excluded patterns;
- SHA-256 setiap artefak utama;
- secure database backup reference/checksum;
- clean-room result;
- known limitations;
- handover acknowledgement.

## Verifikasi penerimaan

1. SHA-256 cocok.
2. Secret scan bersih atau exception terdokumentasi dan dienkripsi.
3. Repository dapat di-checkout/extract.
4. Dependency install memakai lockfile.
5. Asset build berhasil.
6. Migration/restore staging berhasil.
7. Automated tests dan smoke test berjalan.
8. Cron/queue/storage diketahui dan dapat diaktifkan.
9. Rollback dapat dijalankan.
10. Pemilik menyimpan paket dan backup di akun/penyimpanan yang berada dalam kendalinya.

