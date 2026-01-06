# Core Services (`/app/core`) — (2.0.7)

Core service classes (non-exhaustive):

- `db` (`/app/core/db.php`) — MySQLi access layer used across core/admin/modules
- `auth` (`/app/core/auth.php`) — authentication & session checks for admin and protected views
- `version` (`/app/core/version.php`) — local/remote version checks via version domain
- `seo` (`/app/core/seo.php`) — generates/maintains SEO artifacts (sitemap, ror)
- `modules` (`/app/core/modules.php`) — module registry helpers
- `plugins` (`/app/core/plugins.php`) — plugin registry helpers
- `utility` (`/app/core/utility.php`) — common helpers for rendering/paths/etc.

## Version Service Specifics

`version` uses:
- Local: `/app/data/version.json`
- Remote: `https://version.chaoscms.org/db/version.json`

It compares using `version_compare()` and returns status such as:
- `up_to_date`
- `update_available`
- `ahead`
- `unknown`

