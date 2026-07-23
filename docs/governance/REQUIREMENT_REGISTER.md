# REQUIREMENT_REGISTER.md — SFI Release 0 Closeout (CP8 RC2-A Logic Resilience Core)

| Requirement ID | Acceptance Gate Ref | Description | Category | Acceptance Criteria | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `R0-RC2A-01` | Gate G1 | All 14 baseline fields editable with audit trail & tenant isolation | Baseline Governance | All 14 baseline fields editable; changes written to `animal_field_changes` | **PASS** |
| `R0-RC2A-02` | Gate G2 | Assumption to actual weight updates ADG from PROVISIONAL to ACTUAL | Calculation Engine | ADG badge updates from `PROVISIONAL` to `ACTUAL` upon weight correction | **PASS** |
| `R0-RC2A-03` | Gate G3 | Single weight log results in `NOT_CALCULABLE` (`NULL`), not zero | Growth Engine | Returns `NULL` / `TIDAK DAPAT DIHITUNG` when logs < 2 | **PASS** |
| `R0-RC2A-04` | Gate G4 | Entry date correction rebuilds HPP eligibility and allocations | HPP Allocation Ledger | `hpp_allocations` re-allocates costs deterministically upon entry date change | **PASS** |
| `R0-RC2A-05` | Gate G5 | Ownership correction updates history and clears cache both sides | Cache & Tenant Isolation | `animal_ownership_logs` populated; cache for old and new partner invalidated | **PASS** |
| `R0-RC2A-06` | Gate G6 | Location correction rebuilds location history and HPP | Location History | Field audit trail logged; HPP projections recalculated | **PASS** |
| `R0-RC2A-07` | Gate G7 | Data Quality Inbox correction queue end-to-end | Correction Queue | Complete Data workflow resolves issue and updates animal attributes | **PASS** |
| `R0-RC2A-08` | Gate G8 | Failure recovery creates failed run and retries without duplicate ledgers | Fault Tolerance | `derived_calculation_runs` tracks failures; idempotency keys prevent duplicates | **PASS** |
| `R0-RC2A-09` | Gate G9 | Full rebuild command produces identical checksums on consecutive runs | Determinism & Idempotency | `php artisan sfi:rebuild-derived` yields identical SHA256 checksums | **PASS** |
| `R0-RC2A-10` | Gate G10 | Imported assumed weights preserve ASSUMED status & PROVISIONAL label | Import Governance | `measurement_status = ASSUMED` preserved; ADG badge remains `PROVISIONAL` | **PASS** |
| `R0-RC2A-11` | Gate G11 | Value resolver priority order (`ACTUAL > ESTIMATED > ASSUMED > UNKNOWN`) | Priority Resolver | Highest confidence value resolved deterministically | **PASS** |
| `R0-RC2A-12` | Gate G12 | Full regression passes with zero failures, 0 skipped, 0 incomplete | Quality Assurance | `php artisan test` 100% PASS across 104+ test cases | **PASS** |
