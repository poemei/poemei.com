# STN-Labz Development Protocol
 - Code Editing, AI Usage, and Commenting Standards
 - Ratified and Passed, Wednesday, March 11, 2026 01:56Z

## 1. Purpose

This document defines the rules for modifying source code within this project.
**The goal is to ensure**:
 - traceable edits
 - code integrity
 - architectural stability
 - safe use of AI tools

These rules apply to all contributors, including humans and AI systems.

## 2. Core System Protection
The core architecture is locked and may not be modified by AI systems under any circumstance.

Restricted areas include but are not limited to:
 - `/app/core`
    - `router.php`
 - `/app/bootstrap.php`
 - database layer (`model.php`)
 - authentication (`auth.php`)
These areas are human-authoritative only.
AI tools must not propose, generate, or modify code in these areas.

## 3. AI Usage Rules

AI tools may assist development only outside the protected core.

**Permitted areas typically include**:
`/assets`
`/docs`
`/tests`
`/app/models`
`/app/views`
`/app/controllers`

AI generated code must always be reviewed by a human before being accepted.
 - Once a file has achieved it desired state, locked headers get inserted.
 - AI annotation rules
 - The propose/origin/approve/render protocol
   - AI Propose Fix/Patch
   - Original Code gets presented and patch explained
   - Human Approval or Denial
   - On Approval, file gets rendered.

## 4. Mandatory AI Annotation

Any code written or modified by an AI must be annotated.

**Format**:
/* [AI:MODEL_NAME | YYYY-MM-DD HH:MM:SS UTC] */
<modified or generated code>
/* [End AI:MODEL_NAME] */

**Example**:
/* [AI:GPT | 2026-03-10 18:15:00 UTC] */
function generateMetaTags($page)
{
    return "<meta name='description' content='{$page->description}'>";
}
/* [End AI:GPT] */

This ensures all AI modifications are traceable.

## 5. Human Edit Annotation

Human modifications that change logic should also be annotated.

**Example**:
/* [Human:Mei | 2026-03-10 18:30:00 UTC] */
/* Disabled CRUD operations pending audit */

## 6. Database Modification Restrictions

AI systems may not introduce or modify database write operations unless explicitly instructed.

Restricted operations include:

INSERT
UPDATE
DELETE
ALTER

This rule exists to prevent unauthorized data mutation.

## 7. Router Protection

The routing system is considered architectural infrastructure.

It is locked and may not be modified by AI systems.

Attempts by AI tools to refactor or rewrite routing logic must be rejected.

## 8. Comment Preservation

Existing comments must never be removed unless replaced with updated documentation.

Comments provide historical context and must be preserved whenever possible.

## 9. Evidence Preservation

When disabling code during debugging or auditing:

Code must be commented out, not deleted.

**Example**:
/* Disabled during SEO audit
$seoModel->insert($data);
*/

This preserves the investigation history.

## 10. Philosophy

The system architecture must remain human-directed.

AI tools are assistants, not architects.

Core infrastructure is built and maintained by humans to ensure reliability, traceability, and long-term maintainability.
