# Database Schema Used by Core — (2.0.7)

This documents the tables core expects and uses directly.

## `settings`
Key/value store. Core reads:
- `site_theme`

Fields:
- `id (PK)`
- `name` (unique)
- `value`

## `themes`
Theme registry.

Fields:
- `id (PK)`
- `slug (unique)`
- `installed`
- `enabled`
- `version`
- `creator`
- `created_at`
- `updated_at`

Notes:
- If no enabled theme exists, bootstrap must fall back to file system `default`.

## `topics`
Global taxonomy.

Fields:
- `id (PK)`
- `slug (unique)`
- `label`
- `is_public`
- `created_at`

## `roles`
Role registry.

Fields:
- `id (PK)`
- `slug`
- `label`

Rule:
- Admin role is id `4`.

## `users`
Auth user table.

Fields:
- `id (PK)`
- `username (unique)`
- `email`
- `role_id`
- `password_hash`
- `created_at`
- `updated_at`

## Content Tables
- `pages`
- `posts`
- `post_replies`

## Media Tables
- `media_files`
- `media_gallery`

## Registry Tables
- `modules`
- `plugins`

