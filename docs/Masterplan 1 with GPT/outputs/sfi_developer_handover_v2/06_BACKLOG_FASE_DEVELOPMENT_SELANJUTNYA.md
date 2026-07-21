# Backlog dan Tahapan Development SFI

## Urutan rekomendasi

### Release 0 — Data safety dan independensi operasional

- Source/repository handover dan clean-room build.
- Backup/restore teruji.
- Export Center lengkap, manifest, checksum, reconciliation.
- Current-state inventory dan baseline test.

**Gate:** tidak ada reset/cutover sebelum lulus.

### Release 1 — Canonical data dan migrasi aman

- Canonical event/history model.
- Canonical import v2 + legacy adapter.
- Preview/dry-run/idempotency/rollback.
- Data Quality Inbox, temporary tag, B43/death event, parent reconciliation.
- Media/Google Drive link.

### Release 2 — Quick wins frontend dan governance

- Settings registry dan frontend business-safe settings.
- Role/permission scope dan audit.
- UI content/theme/translation business controls.
- Generation dan age-category rules versioned.
- Historical dashboard foundation.

### Release 3 — Sales dan reporting

- Search/filter/multi-select ternak.
- Proforma → reservation → DP/payment → invoice → complete/cancel/refund.
- PDF proforma/invoice.
- Report Center periode/mitra/kolom/KPI dan multi-format dari snapshot yang sama.

### Release 4 — Inventory dan HPP ledger

- Inventory batch/receipt/usage/waste/adjustment/unit conversion.
- HPP policy engine dan monthly cutoff workflow.
- Historical HPP, audit drill-down, reversal/repost, simulation.
- Contract/accounting decisions dan partner statements.

### Release 5 — Breeding, health, offline, dan decision intelligence

- Birth/mating/pedigree coverage dan anti-inbreeding confidence threshold.
- Preventive health, withdrawal warnings, sale block.
- Mobile PWA/offline queue dan conflict resolution.
- Reproductive cohort KPI dengan numerator/denominator/rule version.

## Keputusan pemilik yang masih diperlukan

1. Fullblood × Fullblood: hasil tetap Fullblood/Pure atau butuh registrasi studbook?
2. HPP: siapa economic bearer per jenis biaya dan bagaimana aturan kemitraan/bagi hasil?
3. Ternak hasil kelahiran: definisi acquisition cost, asset valuation, dan revenue recognition.
4. Pajak, numbering, invoice trigger, DP cancellation/refund, dan credit note.
5. Approval model untuk high-impact settings, posting HPP, sale override, dan data merge.
6. Retention media/audit/export dan RPO/RTO.

Keputusan belum tersedia tidak boleh menghambat pembangunan fondasi. Implementasikan schema/UI/feature flag, tetapi jangan aktifkan policy finansial yang belum disetujui.

## Items yang tidak perlu diprioritaskan sebelum fondasi

- Generic AI analytics tanpa data-quality coverage.
- PPTX/image untuk semua laporan; batasi pada report template yang benar-benar presentable.
- Full Google Drive automation sebelum link metadata, permission, ownership, retention, dan failure handling jelas.
- Anti-inbreeding hard block saat pedigree coverage masih rendah.
- Optimasi skala besar sebelum baseline query dan target populasi terukur.

