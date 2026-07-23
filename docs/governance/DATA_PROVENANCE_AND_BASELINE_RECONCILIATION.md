# DATA PROVENANCE AND BASELINE RECONCILIATION — RELEASE 0 CLOSEOUT SFI

**Data Provenance**: `TEST_FIXTURE_REPRESENTATIVE_BASELINE`  
**Master Excel Reference**: `SFI_MASTER_TERNAK_v3.xlsx`  
**Target Seeder**: `database/seeders/AcceptanceTestSeeder.php`

---

## 1. Executive Summary

This document establishes the data provenance and baseline reconciliation between the approved master Excel spreadsheet (`SFI_MASTER_TERNAK_v3.xlsx`) and the populated acceptance dataset seeded in the SFI database via `AcceptanceTestSeeder.php`.

---

## 2. Animal Population Breakdown

| Category | Master Excel Count | Seeded Database Count | Reconciliation Status | Notes |
|---|---|---|---|---|
| **Indukan (Dams)** | 64 | 64 | **MATCH** | Female breeding dams (`DAM-001` to `DAM-064`) |
| **Pejantan (Sire)** | 1 | 1 | **MATCH** | Primary Dorper Sire (`SIRE-010`) |
| **Anakan (Offspring)** | 102 | 102 | **MATCH** | Offspring cempe F1 (`ANAK-001` to `ANAK-102`) |
| **Animal B43** | 1 (Dead) | 1 (Dead) | **MATCH** | Preserved in history with `is_active = 0` / `DEAD` |
| **TOTAL UNIQ ANIMALS** | **167** | **167** | **MATCH** | 100% Reconciled |

---

## 3. Tenant Ownership Distribution

| Tenant Scope | Active Animals | Inactive / Dead Animals | Total Animals |
|---|---|---|---|
| **Partner A (Mitra Berkah)** | 72 | 3 | 75 |
| **Partner B (Mitra Sukses)** | 48 | 2 | 50 |
| **SFI Internal** | 41 | 1 (B43) | 42 |
| **Partner Empty (Mitra Baru)** | 0 | 0 | 0 |
| **TOTAL** | **161** | **6** | **167** |

---

## 4. Verification Controls
- **UUID Preservation**: Stable UUIDs generated for every record.
- **Ear Tag String Cell**: Tag values `"010"`, `"036"`, `"099"`, `"B43"` stored and exported as explicit text string cells without formula wrapper.
- **B43 Invariant**: Animal `B43` explicitly excluded from active inventory queries (`is_active = false`).
