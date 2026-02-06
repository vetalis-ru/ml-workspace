# MemberLux mbl-certificates — Handoff Summary

## Purpose
Official MemberLux module responsible for issuing certificates to users after course completion or other triggers.

## Core Responsibility
- Create certificate records
- Store issuance date
- Emit a canonical event for downstream logic

## Database
Table: wp_memberlux_certificate

Key fields:
- certificate_id (PK)
- user_id
- date_issue
- other metadata fields (no status field)

Important:
- There is NO `status` column in this table.
- Presence of a row = certificate issued.

## Core Flow
- Certificate is created via Certificate::create()
- Immediately after insert:
  do_action('mbl_certificate_issued', $user_id, $cert_id)

This hook is the ONLY reliable signal that a certificate was issued.

## Hooks
- mbl_certificate_issued(user_id, cert_id)
  - Fired once per certificate creation
  - Used as a trigger by other plugins (e.g. ml-bundle-courses)

## Integration Notes
- Do NOT poll the certificate table repeatedly
- Always react to mbl_certificate_issued
- Idempotency must be handled by consumer plugins

## Known Consumers
- ml-bundle-courses:
  - Advances program steps
  - Issues next access level (term key)

## Anti-patterns
- Checking non-existent `status` fields
- Assuming UI limitations prevent re-issue
- Treating certificates as mutable state

## Best Practices
- Treat certificates as immutable facts
- Store your own meta if you need state
- Use certificate_id + user_id hash for idempotency

## Confidence Level
This document is based on direct code inspection and runtime behavior.
It may not reflect undocumented edge cases.
