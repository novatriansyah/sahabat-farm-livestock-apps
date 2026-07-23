# REQUIREMENT_REGISTER.md — SFI Release 0 Closeout (CP7 REV1 Data-Truth & Operability Closeout)

| Requirement ID | Audit Ref | Description | Category | Acceptance Criteria | CP7 Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `R0-CP7-01` | CP6-01 | Remove fictitious ADG (125 g/day) and treatment cost (45,000) defaults | Data-Truth | Return NULL / `TIDAK DAPAT DIHITUNG` when event logs missing | **PASS** |
| `R0-CP7-02` | CP6-02 | Remove fabricated default values (`Sehat proporsional`, `3.5`, `3500000*1.2`, `40.0`, `EVT-2025-001`) | Data-Truth | 35-field exporter returns exact DB state or NULL | **PASS** |
| `R0-CP7-03` | CP6-03 | Correct fictitious pedigree tags (`SIRE-010`) in example rows and tests | Data-Truth | Pedigree references valid or NULL | **PASS** |
| `R0-CP7-04` | CP6-04 | Support nullable `exit_date` for unverified exit records in DB schema | Schema | `exit_logs.exit_date` allows NULL | **PASS** |
| `R0-CP7-05` | CP6-05 | Implement Missing Data Governance Engine & Rule Matrix CSV | Governance | Registered rules categorize OPTIONAL, CONDITIONALLY_REQUIRED, CRITICAL | **PASS** |
| `R0-CP7-06` | CP6-06 | Implement Process Dependency Matrix & Conditional Process Blocking | Governance | Blocks process operations when required fields missing | **PASS** |
| `R0-CP7-07` | CP6-07 | Implement "Lengkapi Data" User Completion Flow & Data Quality Inbox | UI / API | Data Quality Inbox supports field completion with audit trail | **PASS** |
| `R0-CP7-08` | CP6-08 | Seed exact 166 animals baseline with accurate owner distribution | Data Seeding | SFI (98), VINA (22), FAHRI (18), LETA (11), AGENG (10), OKI (7) | **PASS** |
| `R0-CP7-09` | CP6-09 | Seed 46 ear tag history logs and 71 open data quality issues | Data Seeding | DB contains 46 tag history logs & 71 issues | **PASS** |
| `R0-CP7-10` | CP6-10 | Fix B43 male exit record and tag 411 weight log handling | Data-Truth | B43 male, F2, VINA, exit status MATI, exit date NULL | **PASS** |
| `R0-CP7-11` | CP6-11 | Build Export Center UI & Tenant Isolation | UI / Security | Single Export Center view; MITRA restricted to own partner data | **PASS** |
| `R0-CP7-12` | CP6-12 | Refactor Importer to enforce 0 dry-run writes & 0 factual defaults | Importer | Importer updates by UUID/tag_id without fabricating values | **PASS** |
| `R0-CP7-13` | CP6-13 | Build Unified Calculation Service for cross-format parity (Web/XLSX/PDF) | Calculation | 100% numerical parity across Web, XLSX, PDF | **PASS** |
| `R0-CP7-14` | CP6-14 | Refactor Independent Reconciliation Engine (Master-to-DB comparison) | Reconciliation | Direct comparison between Master Excel and DB | **PASS** |
| `R0-CP7-15` | CP6-15 | Fix Backup & Verification commands (SHA-256 compressed stream & zero-media evidence) | Recovery | Verification passes on compressed bytes; zero-media manifest generated | **PASS** |
| `R0-CP7-16` | CP6-16 | Enforce Disposable Database Allowlist in Restore command | Security | Restore allowed only on staging/testing/disposable DBs | **PASS** |
| `R0-CP7-17` | CP6-17 | Create Operational Runbooks (Deployment, Rollback, Recovery) | Runbooks | 3 operational runbooks created in `docs/runbooks/` | **PASS** |
| `R0-CP7-18` | CP6-18 | Package Final CP7 Acceptance Bundle & Programmatic Validator | Packaging | All G1 - G18 gates pass; CP6 rejected as negative control | **PASS** |
