# Admin System (`/app/admin`) — (2.0.5)

## Entry

Admin is routed via:

- `/app/admin/index.php` (admin router)
- `/app/admin/admin.php` (admin wrapper/layout)

Views live in:

- `/app/admin/views/*.php`

Static assets may live under:

- `/app/admin/assets/` (implementation-dependent)
- Admin CSS is served from `/public/assets/css/admin.css`

## Conventions

- Admin pages are wrapped by `admin.php` wrapper.
- Styling is centralized (avoid inline CSS).
- Admin routes are action-driven (example: `/admin?action=pages`).

## Tools

Core tooling pages added/maintained in this version include:

- Topics: `/admin?action=topics`
- Roles: `/admin?action=roles`

### Topics
CRUD over `topics` table:
- slug, label, is_public

### Roles
CRUD over `roles` table with guardrails:
- role id `4` is admin and must not be deletable
- roles cannot be deleted when referenced by `users.role` or `users.role_id`

## CSRF Reality (Important)

Core CSRF behavior can vary across deployments.
For Tools pages where core CSRF policies conflict, the Tools pages use a **view-scoped session CSRF**:
- `$_SESSION['chaos_admin_topics_csrf']`
- `$_SESSION['chaos_admin_roles_csrf']`

This remains secure because:
- admin auth gates access
- CSRF is still enforced for POST
- it avoids mismatched token sources

