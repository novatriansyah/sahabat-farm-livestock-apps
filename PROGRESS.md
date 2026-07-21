# SFI System Alignment — Progress Tracker

> **Goal:** Align existing Laravel 12 codebase with Masterplan 1 (GPT) and Masterplan 2 (Claude) — 18 gaps identified across 6 phases.

## Analysis Complete ✅

- [x] Masterplan 1 analysis (GPT — governance/verification)
- [x] Masterplan 2 analysis (Claude — technical specs)
- [x] Codebase exploration (28 models, 30+ controllers, migrations, routes)
- [x] Gap identification — 18 gaps found (5 critical, 10 missing tables, 3 logic gaps)
- [x] Overlap resolution — masterplans merged into single execution path
- [x] Implementation plan saved to `docs/superpowers/plans/2026-07-21-sfi-alignment-plan.md`

## Implementation Status

### Phase 1 — Export & Backup (BLOCKER — must finish first)
- [ ] Task 1.1: Multi-sheet animal master export (12 sheets)
- [ ] Task 1.2: Multi-format report export (10 reports × 5 formats)
- [ ] Task 1.3: Two-way reconciliation engine

### Phase 2 — Core Logic Fixes
- [ ] Task 2.1: Generation rules table + resolver service
- [ ] Task 2.2: Configurable age categories
- [ ] Task 2.3: HPP partner separation + metabolic weight allocation
- [ ] Task 2.4: Gestation fix (60→150 days) + auto-infer sire
- [ ] Task 2.5: Pending tag assignments for unnumbered animals

### Phase 3 — Frontend Parameterization
- [ ] Task 3.1: Dynamic RBAC (replace hardcoded 4-role enum)
- [ ] Task 3.2: Site settings (branding, hero, catalog, contact)
- [ ] Task 3.3: Consumable types (feed/vitamin/medicine config)
- [ ] Task 3.4: Audit trail (laravel-auditing)

### Phase 4 — New Modules
- [ ] Task 4.1: Full sales module (proforma → payment → exit)
- [ ] Task 4.2: Feed & vitamin module (purchases + usage + allocation)
- [ ] Task 4.3: 10 report types with unified filter panel

### Phase 5 — Reset & Clean Import
- [ ] Task 5.1: Import template with gdrive_folder_url column
- [ ] Task 5.2: Reset procedure + post-import validation checklist

### Phase 6 — Advanced Features
- [ ] Task 6.1: Anti-inbreeding check (Wright coefficient)
- [ ] Task 6.2: KPI reproduction dashboard (7 metrics)
- [ ] Task 6.3: Withdrawal period check
- [ ] Task 6.4: WhatsApp notifications
- [ ] Task 6.5: Bulk operations (batch weighing)
- [ ] Task 6.6: Digital certificate per animal
- [ ] Task 6.7: Automatic backup schedule

## Blocker Notes
- PHASE 1 must complete before any other phase
- Owner must verify export works before Phase 2 begins
- Generation recalculation must wait until sire_id populated