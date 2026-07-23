# REQUIREMENT_REGISTER.md — SFI Release 0 Closeout (CP8 Phase 1 Final Closeout)

| Requirement ID | Audit Ref | Description | Category | Acceptance Criteria | CP8 Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `R0-CP8-01` | X-01 | Embed at least 4 visual charts per partner XLSX report (Population, ADG, Births, Generations) | Visual Analytics | `$writer->setIncludeCharts(true)` via `WithCharts` & `BuildsPartnerCharts` | **PASS** |
| `R0-CP8-02` | X-02 | Eliminate hardcoded 125 ADG fallback | Data-Truth | Compute dynamic ADG from `WeightLog` or return `TIDAK DAPAT DIHITUNG` | **PASS** |
| `R0-CP8-03` | X-03 | Eliminate hardcoded 45000 treatment cost fallback | Data-Truth | Compute actual treatment cost from `TreatmentLog` | **PASS** |
| `R0-CP8-04` | X-04 | Populate `KELAHIRAN_REPRODUKSI` sheet in partner reports | Data Completeness | Sheet contains partner birth records (`rows > 1`) | **PASS** |
| `R0-CP8-05` | Gate 5 | Restore full test suite total count and pass status | Quality Assurance | `php artisan test` runs 98 tests, 0 failures, 0 errors | **PASS** |
| `R0-CP8-06` | Gate 7 | Record raw, unedited execution logs | Governance | Logged in `01_VERIFICATION_OUTPUT.txt` | **PASS** |
| `R0-CP8-07` | Gate 8 | Build 100% compliant CP8 release ZIP bundle | Packaging | Programmatic packager creates verified CP8 ZIP | **PASS** |
