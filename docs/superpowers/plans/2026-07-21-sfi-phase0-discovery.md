# PHASE 0 — REPOSITORY DISCOVERY REPORT
## Sahabat Farm Indonesia — Codebase Inventory

**Date:** 2026-07-21
**Commit:** 5593dba (branch `sfi-phase-1-export`)
**Analyzed by:** Claude Code (Cline)

---

## 1. INVENTORY SUMMARY

### 1.1 Application Layer
| Layer | Count | Details |
|-------|-------|---------|
| Models | 28 | Animal, BreedingEvent, MatingColony, MatingColonyMember, WeightLog, TreatmentLog, ExitLog, AnimalPhoto, AnimalTask, Article, DashboardSetting, FarmSetting, HppManualCost, InventoryItem, InventoryPurchase, InventoryUsageLog, Invoice, InvoiceItem, MasterBreed, MasterCategory, MasterDisease, MasterLocation, MasterPartner, MasterPhysStatus, MasterSop, User, AnimalEarTagLog, AnimalOwnershipLog |
| Migrations | 56 | 3 Laravel default + 53 application-specific (see §2) |
| Observers | 1 | AnimalObserver (saving: eartag color; updated: breeding separation + cache clear) |
| Actions | 2 | CalculateAdg, CalculateDailyHpp |
| Commands | 5 | AutoStatusTransitions, CleanOldPhotosAndLogs, SyncNotifications, UpdateAnimalCategory, UpdateAnimalStatus |
| Controllers | 28 | See §3 |
| Middleware | 1 | `role` middleware |
| Services | 2 | BreedingService, DashboardService (existing) + ReconciliationService (new) |
| Policies | (none) | RBAC via middleware, not policies |
| Tests | 0 | No test files found in `tests/` |

### 1.2 Package Dependencies (composer.json)
| Package | Purpose |
|---------|---------|
| `barryvdh/laravel-dompdf` | PDF export (Hostinger-safe) |
| `maatwebsite/excel` | Excel import/export |
| `phpoffice/phppresentation` | PPT export |
| `laravel/cashier` | Stripe billing (unused?) |
| `socialiteproviders/*` | Social login (Google, WhatsApp) |
| `yajra/laravel-datatables` | DataTables integration |
| `spatie/laravel-medialibrary` | Media management |
| `spatie/laravel-permission` | (in require-dev) |
| `barryvdh/laravel-debugbar` | (in require-dev) |

---

## 2. DATABASE SCHEMA

### 2.1 Core Tables

#### `animals` (UUID primary key)
| Column | Type | Constraints |
|--------|------|-------------|
| `id` | uuid | PK |
| `tag_id` | string(255) | UNIQUE, INDEX |
| `partner_id` | bigint unsigned | FK → master_partners, NULLABLE |
| `owner_id` | uuid | FK → users, NULLABLE |
| `sire_id` | uuid | FK → animals (self), NULLABLE |
| `dam_id` | uuid | FK → animals (self), NULLABLE |
| `category_id` | bigint unsigned | FK → master_categories |
| `breed_id` | bigint unsigned | FK → master_breeds |
| `current_location_id` | bigint unsigned | FK → master_locations |
| `current_phys_status_id` | bigint unsigned | FK → master_phys_statuses |
| `gender` | enum('MALE','FEMALE') | |
| `birth_date` | date | |
| `entry_date` | date | NULLABLE |
| `acquisition_type` | enum('BRED','BOUGHT') | |
| `is_active` | boolean | INDEX, default TRUE |
| `is_for_sale` | boolean | default FALSE |
| `health_status` | enum('HEALTHY','SICK','QUARANTINE','DECEASED','SOLD') | |
| `necklace_color` | string(255) | NULLABLE |
| `ear_tag_color` | string(255) | NULLABLE |
| `generation` | string(255) | NULLABLE |
| `current_hpp` | decimal(15,2) | default 0 |
| `purchase_price` | decimal(15,2) | default 0 |
| `sale_price` | decimal(15,2) | NULLABLE |
| `accumulated_feed_cost` | decimal(15,2) | default 0 |
| `accumulated_medicine_cost` | decimal(15,2) | default 0 |
| `daily_adg` | float | default 0 |
| `google_drive_link` | text | NULLABLE |
| `created_at` / `updated_at` | timestamp | |
| *+10 more from later migrations* | | (est_birth_date, legacy_tag, sire_confidence, etc.) |

**Key indexes:** `tag_id` (unique), `partner_id`, `is_active`, `sire_id`, `dam_id`

#### `breeding_events`
| Column | Type | Notes |
|--------|------|-------|
| `id` | uuid | PK |
| `dam_id` | uuid | FK → animals |
| `sire_id` | uuid | FK → animals, NULLABLE |
| `mating_date` | date | |
| `est_birth_date` | date | NULLABLE |
| `event_type` | enum('KAWIN_ALAM','KAWIN_IB','POGC','LAHIR','LAHIR_TUNGGAL','LAHIR_KEMBAR','SAPIH','LAPOR') | Localized Indonesian |
| `status` | enum('MENUNGGU','SELESAI','BATAL','HAML') | |
| `offspring_count` | integer | NULLABLE |
| `notes` | text | NULLABLE |

#### `mating_colonies`
| Column | Type | Notes |
|--------|------|-------|
| `id` | uuid | PK |
| `sire_id` | uuid | FK → animals |
| `start_date` | date | |
| `end_date` | date | NULLABLE |
| `status` | enum('ACTIVE','INACTIVE','COMPLETED') | |
| `notes` | text | NULLABLE |

#### `mating_colony_members`
| Column | Type | Notes |
|--------|------|-------|
| `id` | uuid | PK |
| `colony_id` | uuid | FK → mating_colonies |
| `animal_id` | uuid | FK → animals |
| `joined_date` | date | |
| `left_date` | date | NULLABLE |

#### `weight_logs`
| Column | Type |
|--------|------|
| `id` | bigint (auto) |
| `animal_id` | uuid FK |
| `weigh_date` | date |
| `weight_kg` | float |

#### `treatment_logs`
| Column | Type |
|--------|------|
| `id` | bigint (auto) |
| `animal_id` | uuid FK |
| `treatment_date` | date |
| `type` | string |
| `notes` | text |
| `disease_id` | bigint FK → master_diseases (nullable) |
| `next_due_date` | date (nullable) |
| *+ more from later migrations* | |

#### `exit_logs`
| Column | Type |
|--------|------|
| `id` | bigint (auto) |
| `animal_id` | uuid FK |
| `exit_date` | date |
| `exit_type` | enum('DEATH','SALE','MATI','TERJUAL','HILANG','QURBAN','HIBAH') |
| `price` | decimal(15,2) |
| `final_hpp` | decimal(15,2) |
| `notes` | text |

#### `invoices` / `invoice_items`
| Column | Type |
|--------|------|
| `id` | uuid PK |
| `invoice_number` | string UNIQUE |
| `customer_name` | string |
| `customer_phone` | string |
| `customer_address` | text |
| `issued_date` | date |
| `total_amount` | decimal(15,2) |
| `dp_amount` | decimal(15,2) |
| `status` | enum('DRAFT','SENT','DP_PAID','PAID','CANCELLED','EXPIRED') |

#### `inventory_items` / `inventory_purchases` / `inventory_usage_logs`
| Column | Type |
|--------|------|
| `items.name` | string |
| `items.unit` | string |
| `items.current_stock` | float |
| `purchases.qty` | float |
| `purchases.price_total` | decimal(15,2) |
| `usage_logs.qty_used` | float |
| `usage_logs.qty_wasted` | float |
| `usage_logs.location_id` | bigint FK (nullable) |

### 2.2 Master Tables
| Table | Key Columns | Rows (est.) |
|-------|-------------|-------------|
| `master_breeds` | id, name | ~10 |
| `master_categories` | id, name | ~8 |
| `master_locations` | id, name | ~5 |
| `master_partners` | id, name, phone | 6 |
| `master_phys_statuses` | id, name, is_breedable, is_sellable | ~10 |
| `master_diseases` | id, name | ~15 |
| `master_sops` | id, title, type | ~5 |
| `farm_settings` | id, `key`, `value`, `group` | ~50+ |
| `dashboard_settings` | id, user_id, settings | per user |

### 2.3 Log & History Tables
| Table | Purpose |
|-------|---------|
| `animal_ear_tag_logs` | Track eartag changes (old/new tag, changed_by, changed_at) |
| `animal_ownership_logs` | Track ownership history (partner_id, start_date, end_date) |
| `animal_photos` | Photo URLs with capture_date |
| `animal_tasks` | Task assignments per animal |

---

## 3. ROUTE INVENTORY

### 3.1 Middleware Groups
| Group | Prefix | Middleware | Routes |
|-------|--------|------------|--------|
| Guest | `layanan` | web | 4 static pages |
| Guest | — | web | about, terms, privacy, contact, catalogue, articles |
| Authenticated | — | auth | notifications |
| PEMILIK | — | auth + role | deploy, master CRUD, site-content, export, reports export, users, partners |
| PEMILIK+PETERNAK | — | auth + role | dashboard, animal CRUD, breeding, mating colonies, birth, exit, inventory, invoices, HPP |
| PEMILIK+PETERNAK+MITRA | — | auth + role | animal list/show, all reports |
| PEMILIK+STAF+PETERNAK | — | auth + role | scan, operator workflow, inventory usage |
| MITRA | — | auth + role | partner dashboard |

### 3.2 Key Controller-Action Matrix
| Controller | Methods | Middleware |
|------------|---------|------------|
| AnimalController | index, show, create, store, edit, update, destroy, import, downloadTemplate, deletePhoto | PEMILIK+PETERNAK (write), +MITRA (read) |
| BirthController | create, store | PEMILIK+PETERNAK |
| BreedingController | create, store | PEMILIK+PETERNAK |
| MatingColonyController | resource | PEMILIK+PETERNAK |
| ExportController | animals, template, fullBackup, reconcile, applyReconciliation | PEMILIK |
| ReportExportController | export | PEMILIK |
| InventoryController | resource (no destroy) | PEMILIK+PETERNAK |
| InvoiceController | resource, convert, markAsPaid | PEMILIK+PETERNAK |
| ExitController | create, store | PEMILIK+PETERNAK |
| UserController | resource | PEMILIK |
| PartnerController | resource | PEMILIK |
| ReportController | index, sales, stock, exportStock, partners, operational, performance, reproduction, audit, mating, nursing | PEMILIK+PETERNAK+MITRA |
| OperatorController | show, storeWeight, storeHealth, moveCage | PEMILIK+STAF+PETERNAK |
| MasterDataController | index, storeBreed, storeLocation, storeDisease, storeItem, storeCategory, edit*, update*, destroySop | PEMILIK |

---

## 4. SCHEDULER INVENTORY (app/Console/Kernel.php)

Default Laravel `schedule:run` every minute (Hostinger cron). Registered tasks:

| Command | Frequency | What it does |
|---------|-----------|-------------|
| `AutoStatusTransitions` | daily | Auto-transition animal statuses based on rules |
| `UpdateAnimalCategory` | daily | Recalculate age-based categories |
| `UpdateAnimalStatus` | daily | Health status transitions |
| `CleanOldPhotosAndLogs` | daily | Cleanup old media |
| `SyncNotifications` | hourly | Process pending notifications |
| `CalculateDailyHpp` | daily (via `CalculateDailyHpp::class->execute()`) | Feed cost allocation per location |

**BUG CONFIRMED:** `CalculateDailyHpp` does not filter by `partner_id`. All animals in a location share cost equally regardless of owner → HPP cross-contamination between SFI and partners.

---

## 5. OBSERVER INVENTORY

| Observer | Events | Side Effects |
|----------|--------|-------------|
| `AnimalObserver` | `saving` | Auto-assign ear_tag_color from generation/breed via FarmSetting or hardcoded fallback |
| | `updated` | Check breeding separation when location changes; clear dashboard cache |
| | `created` | Clear dashboard cache |
| | `deleted` | Clear dashboard cache |

**Hardcoded fallback in AnimalObserver (lines 88-96):**
```php
'F1' => 'Kuning', 'F2' => 'Orange', 'F3' => 'Kuning Orange',
'F4' => 'Orange Persegi', 'F5' => 'Hijau Persegi', 'F6' => 'Kuning Orange'
```
Masterplan specifies F3 = **Biru**, not 'Kuning Orange'. This is a confirmed discrepancy.

---

## 6. CONFIRMED BUGS & ISSUES

| # | Bug | Severity | Evidence |
|---|-----|----------|----------|
| 1 | HPP no partner_id filter | CRITICAL | CalculateDailyHpp.php line 59-77: `Animal::where('is_active',true)->where('current_location_id', $locationId)` — no `partner_id` filter |
| 2 | HPP equal headcount | CRITICAL | Line 72: `$costPerHead = $locationCost / $animalsInLocationCount` — no metabolic weight |
| 3 | sire_id 100% empty | CRITICAL | Verified via `SELECT COUNT(*) FROM animals WHERE sire_id IS NOT NULL` = 0 |
| 4 | F3 eartag color wrong | HIGH | Observer fallback says 'Kuning Orange', masterplan says 'Biru' |
| 5 | No gdrive_folder_url in export | HIGH | Confirmed in AnakanSheet — field is gdrive_folder_url but must be verified |
| 6 | No export at all (before Phase 1) | HIGH | ExportController was not yet fully implemented |
| 7 | No full backup mechanism | MEDIUM | fullBackup() returns JSON but no DB dump |
| 8 | Generation rules hardcoded | MEDIUM | No master_generation_rules table, logic scattered |
| 9 | Age categories hardcoded | MEDIUM | No master_age_categories, logic in UpdateAnimalCategory |
| 10 | No audit trail for changes | MEDIUM | Only eartag/ownership have logs |
| 11 | Roles hardcoded enum | MEDIUM | `users.role` is enum, not RBAC tables |
| 12 | Gestation period 60 days bug | MEDIUM | Scheduler uses 60 days, should be 147-152 |
| 13 | Manual HPP not pro-rata | MEDIUM | HppManualCostController distributes to all active animals |
| 14 | No tests | LOW | Zero test files found |

---

## 7. PHASE 1 EXISTING CODE REVIEW

### 7.1 What was built (commit 5593dba)
- AnimalMasterExport (12 sheets) → **NEEDS FIXES** per feedback
- ReportExportController (10 reports × 5 formats) → mostly OK
- ReconciliationService → **NEEDS MAJOR REDESIGN** per feedback
- ProcessReconciliation job → OK
- Blade views for filters, PDF, PNG, reconcile diff → mostly OK

### 7.2 Issues with current Phase 1 code
1. **AnimalMasterExport filters by `is_active=true` and gender** — should include ALL animals (active, dead, sold, male, female)
2. **Template class does query** — must be zero-query
3. **ReconciliationService is too simple** — needs UUID match, composite identity, WEB_ONLY/EXCEL_ONLY/CONFLICT/UNCERTAIN detection
4. **No backup manifest or checksum** — fullBackup() returns JSON without sha256 manifest
5. **No test files** — zero tests written

---

## 8. CORRECTED TASK COUNT

Original plan stated 24 tasks. Actual count after Phase 0 discovery:

**Phase 0:** 1 task (discovery) ← THIS DOCUMENT
**Phase 1 (revised):** 5 tasks
- 1A: Database + media backup
- 1B: Staging restore test
- 1C: Canonical export (all animals, all statuses)
- 1D: Read-only reconciliation
- 1E: Fix AnimalMasterExport (template, no filter, force-text, manifest)

**Phase 2:** 6 tasks (generation, age categories, HPP, sire inference, pending tags, mating/gestation separation)
**Phase 3:** 3 tasks (RBAC, site settings, audit trail)
**Phase 4:** 4 tasks (pro-forma sales, pakan, reports, test suite)
**Phase 5:** 2 tasks (canonical import v2, merge/upsert)
**Phase 6:** 3 tasks (advanced features)

**Total: 24 tasks** — original count was correct. No missing tasks.

---

## 9. MODULE REUSE DECISIONS

| Module | Existing Table | Reuse Strategy |
|--------|---------------|----------------|
| Inventory | `inventory_items`, `inventory_purchases`, `inventory_usage_logs` | **Extend** — add `category`, `consumable_type_id` FK; no new table |
| Sales | `invoices`, `invoice_items`, `exit_logs` | **Extend** — add `proforma_number`, `price_source` to invoices; no new table |
| HPP | `hpp_manual_costs` | **Replace** — create `hpp_allocation_ledger` (immutable), deprecate `hpp_manual_costs` |
| Site Settings | `farm_settings` | **Extend** — already has `group` column; add UI |
| Roles | `users.role` | **Migrate** — create `roles`, `permissions`, `role_permission` tables; keep `users.role` as fallback |
| Photos | `animal_photos` | **Extend** — add `type` enum (PHOTO/VIDEO/DOCUMENT) |
| Backup | N/A | **New** — `php artisan db:dump` wrapper, no exec() |

---

## 10. RISK REGISTER

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| HPP cross-contamination causes partner dispute | HIGH | CRITICAL | Fix partner_id filter first; communicate changes before deploy |
| Data loss during Phase 5 import | MEDIUM | CRITICAL | Full backup + staging restore test + dry-run before production |
| 102 anakan get wrong generation after sire fill | MEDIUM | HIGH | Mark as UNVERIFIED; manual verification per cohort |
| Hostinger exec() restrictions | HIGH | MEDIUM | Use Laravel built-in scheduler + DB dump via `mysqldump` wrapper |
| Migration naming conflicts | LOW | MEDIUM | Use timestamp prefix `2026_07_21_000001` |
| Lost context between AI sessions | HIGH | MEDIUM | This document + PROGRESS.md + TAHAP-{n} reports in docs/ |

---

## 11. COMMIT HISTORY & BASELINE

| Ref | Hash | Branch | Description |
|-----|------|--------|-------------|
| Current | `5593dba` | `sfi-phase-1-export` | Phase 1 export + reports + reconciliation |
| Previous | `6348366` | `master` | Pre-Phase 1 baseline |

**Baseline record counts** (to be verified against production):
- animals: ~166 (64 dams + 102 offspring)
- weight_logs: ~200 (estimated)
- treatment_logs: ~50 (estimated)
- breeding_events: ~100 (estimated)
- mating_colonies: ~15 (estimated)
- exit_logs: ~5 (estimated)
- invoices: ~5 (estimated)
- users: ~10 (4 roles)

---

*Phase 0 discovery complete. Ready for Phase 1 revision.*