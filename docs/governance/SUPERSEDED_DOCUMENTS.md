# Governance Index — Superseded Documents & Active Checkpoints

## Current Active Checkpoint
- **Checkpoint**: `CP7 — Data-Truth & Operability Closeout (REV1)`
- **Status**: ACTIVE & IN PROGRESS
- **Release Target**: `Release 0 Closeout`
- **Scope**: Data truth enforcement, 35-field lossless roundtrip, Export Center UI, independent reconciliation, native recovery packaging, missing data governance, content-aware package validator, executable runbooks.

---

## Superseded Documents Log

| Document Name | Original Phase | Date Superseded | Superseded By | Reason for Supersede |
|---|---|---|---|---|
| `SFI_RELEASE0_CLOSEOUT_CP6_FINAL_20260723_161932_WIB.zip` | CP6 | 2026-07-23 | CP7 Execution Plan | **CP6 REJECTED** by independent audit due to fabricated facts, non-roundtrip schema, self-reconciliation, and unrecoverable backup packaging. |
| `AUDIT_RESPONSE_CP5.md` | CP5 | 2026-07-23 | CP7 Execution Plan | CP5 audit replaced by CP6 and CP7 closeouts. |
| `AUDIT_RESPONSE_CP4.md` | CP4 | 2026-07-23 | CP7 Execution Plan | Obsolete legacy checkpoint response. |
| `package_release0_cp6.php` | CP6 | 2026-07-23 | `package_release0_cp7.php` | CP6 builder replaced by CP7 builder script enforcing all 18 hard gates. |

---

## Governance Rules
1. All obsolete or rejected checkpoint packages MUST be marked as superseded.
2. No audit history shall be amended or force-pushed; all corrections must occur via clean corrective commits.
3. Proof of completion requires passing all **18 Hard Acceptance Gates (G1 - G18)** under `package_validator.php`.
