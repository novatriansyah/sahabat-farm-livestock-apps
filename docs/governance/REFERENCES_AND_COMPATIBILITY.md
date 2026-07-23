# REFERENCES_AND_COMPATIBILITY.md — SFI Release 0 Closeout / Phase 1.1

**Last Updated**: 2026-07-22  
**Target Environment**: PHP 8.3 / MySQL 8.0 / Laravel 12 / Ubuntu 24.04 (Hostinger & GitHub Actions)

| Dependency / Standard | Version in Lockfile | Official Documentation URL | Key Usage / Implementation Rule | Compatibility Verification Test |
| :--- | :--- | :--- | :--- | :--- |
| **PHP** | `8.3.x` | https://www.php.net/manual/en/migration83.php | Strict type checking (`string`, `array`, `Collection`) | PHP 8.3 type error checks in `ExportController` |
| **Laravel Framework** | `12.x` | https://laravel.com/docs/12.x/authorization | Roles/Policies (`PEMILIK`, `MITRA`, `PETERNAK`, `STAF`) & HTTP tests | Authorization & direct URL negative tests (`T-026`) |
| **Maatwebsite Excel** | `3.1.x` | https://docs.laravel-excel.com/3.1/exports/column-formatting.html | `WithMultipleSheets`, `WithColumnFormatting`, Explicit text cells | True-string cell formatting test (`T-007`) |
| **PhpSpreadsheet** | `^1.29` or `^2.0` | https://phpspreadsheet.readthedocs.io/ | `Shared\Date::excelToDateTimeObject`, `DataType::TYPE_STRING` | Template & reconciliation workbook tests (`T-005`, `T-015`) |
| **MySQL Server** | `8.0` | https://dev.mysql.com/doc/refman/8.0/en/backup-and-recovery.html | Foreign key constraints, charset `utf8mb4`, non-atomic DDL behavior | Backup & restore command test on staging MySQL (`T-020` - `T-025`) |
| **OWASP Security** | Standard | https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html | File upload validation, tenant isolation, access control, audit logs | Tenant isolation & partner scope tests (`T-009`, `T-010`) |
