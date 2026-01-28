# MemberLux Certificates Module (mbl-certificates) — Handoff Summary

## Purpose
Handles issuance of certificates in MemberLux and emits the canonical completion event used by external/custom plugins. Does **not** manage access rights directly.

## Core Responsibility
- Create certificate DB record.
- Emit canonical hook `mbl_certificate_issued`.
- Serve as the commit point for course/step completion.

## Core Class & Method
- Class: `Certificate`
- Method: `Certificate::create()`
  - Inserts certificate record.
  - Immediately triggers:
    ```php
    do_action('mbl_certificate_issued', $user_id, $cert_id);
    ```

## Canonical Hook
### `mbl_certificate_issued`
**Signature**
```php
do_action('mbl_certificate_issued', int $user_id, int $cert_id);
```
**Timing**
- Fired immediately after successful DB insert.

**Guarantees**
- User exists.
- Certificate record exists.
- `cert_id` is valid.

**Role**
- Single source of truth for “completion”.
- Entry point for chained logic (program flow, next access grant, integrations, analytics).

## Relation to Access (Term Keys)
- Indirect only.
- Typical flow:
  ```
  wpm_update_user_key_dates
    → (core/official logic)
      → Certificate::create
        → mbl_certificate_issued
  ```
- External code must **not** replicate this flow manually.

## Data Model
- Table: `{prefix}memberlux_certificate`
- Confirmed fields: `id (cert_id)`, `user_id`, `date_issue`.
- Table structure is internal; prefer hooks over direct SQL.

## Integration Boundaries
### Allowed
- Subscribe to `mbl_certificate_issued`.
- Implement idempotent logic based on `user_id` + `cert_id`.
- Trigger business processes after certificate issuance.

### Not Allowed
- Modify core module.
- Insert certificates directly into DB.
- Rely on UI behavior or undocumented constraints.

## Usage Example (ml-bundle-courses)
- Listens to `mbl_certificate_issued`.
- Treats event as step completion.
- Grants next access level.
- Uses idempotency guard (e.g., `user_meta` hash).

## Recommended Patterns
- Treat `mbl_certificate_issued` as the **only** completion signal.
- Always implement idempotency.
- Do not assume certificate == last access.
- Avoid direct DB or UI coupling.

## Confidence Level
- Hook `mbl_certificate_issued`: confirmed and stable.
- DB schema & UI constraints: internal / not guaranteed.

## One-Line Summary
`mbl_certificate_issued` is the canonical commit point of learning completion in MemberLux; all post-certificate logic must start from this hook.
