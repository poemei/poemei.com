#Chaos CMS Core
##Changelog
**Last Update**: 20251228-0530Z
**Managed By**: Chaos Dev (`devteam@choascms.org`)
**Date/Time**: UTC.
**Version**: *2.0.5*
----------

##v2.0.5 - Dev
**20251228-0530Z**
**Type**: General Development

**Added**
 - roles, 1-4 for users, editors, moderators and admins
 - delete option on posts replies for moderators (No admin access)
 - minimal admin access (`posts` & `media`) for editors.
 - Version check both local and remote (`version.chaoscms.org/db/version.json`)
 
 **Changed**
 - role decisions based on role additions
 ----------

##v2.0.5 - Dev
**20251226-0330Z**
**Type**: General Development

**Added**
 - members only to the posts module and its admin
 - members only for media and its admin
 
 **Fixed**
 - Options in `admin->maintenance`
 - SEO not triggering
 
**Changed**
 - visibility rules in posts
 - visibility rules in media
 ----------

##v2.0.4 - Dev
**20251224-1530Z**
**Type**: General Development

**Added**
 - Social Media Sharing for individual posts, requires bootstrap icons cdn in the `$site_theme` header
 ```html
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
 ```
 - Gated modules and plugins from loading without first being installed and enabled.
 - Make modules and plugins setup data tables and pre load data when installed via `admin->modules` or `admin->plugins`
 - **Health** to admin to have access to system level health responses.
 
 **Changed**
  - moved `author` columns to `creator` for plugins and modules, authors are for post, creators create.
----------

##v2.0.3 - Dev
**20251223-1750Z**
**Type**: General Development

**Added**
 - Module admin in `/admin?action=modules`
 - Plugin admin in `/admin?action=plugins`
 - Each uses a `/admin/action=(plugin or modules_admin&slug={$slug]` url schema.
 
**Changed**
 - Fixed `admin->themes` to do better handle enabling, disabling of themes (`db` requirements)
 
**Removed**
 - The requirement for a theme preview option.
----------
##v2.0.3 - Dev
**20251223-0205Z**
**Type**: General Development

**Added**
 - Editor role
 
 **Changed**
  - Editor role restrictions (not full admin) to admin dashboard.
  - Changed `auth::check` to a non-static `auth->check()`

##v2.0.2 - Dev
**20251222-0135Z**
**Type**: General Development

**Added**
 - Convert all Previously developed admin pages to new format
   - plugins
   - media
   - modules
   - themes
   - posts
   - users
   - settings
   - maintenace
   - Core media module
 
**Changed**
 - Moved routing setup to index to remove router issues with admin
 - Dis joined `/admin` from main site theme (change in `/app/router`)
 - Moved `SEO` to maintenance

##v2.0.1 - Dev
**20251218-1154Z**
**Type**: General Development

**Added**
 - PHP Mailer in `/app/lib/phpmailer`
 - Linked PHPMailer in `/app/lib/mailer.php` for `$global` usage.
 Use as
```php
$mailer = new mailer($db);
$mail   = $mailer->create();

$mail->addAddress('you@example.com', 'You');
$mail->Subject = 'Test';
$mail->Body    = '<p>Hello.</p>';

$mail->send();
```
 
 ----------

##v2.0.1 - Dev
**20251217-2230Z**
**Type**: General Development

**Added**
 - Plugins core (DB and loader)
 - Modules core (DB and loader)
 - Themes core (DB)
 
**Fixed**
 - Auth ordering @ Bootstrap
 - Posts tables
 ----------

##v2.0.1 - Dev
**20251217-1836Z**
**Type**: General Development

**Added**
 - Auth gate @ admin
 
 ----------

##v2.0.1 - Dev
**20251127-0157Z**
**Type**: General Development

**Added**:
 - Enhanced rendering capabilities for markdown
 - Database support in `/app/core/db.php`
 - Enhanced rendering capabilities for JSON.
 
 **Auth**
   - Login
   - Logout
   - Delete Account
   - Change Password
   - Account Page
   ```php
   <?php
   $db = new db();
   $auth = new auth($db);
   ?>
   ```
  
 **Post**
   - Pull data from posts table
   - auth dependent
   - Present the option to respond if logged in
   - Tied in the account page for last 5 activity

**Fixed**:
 - split rendering into two separate files, one for `.md` and one for `.json` for easier rendering

## v2.0.0 - Dev
**20251126-0525Z**
**Type**: General Development

**Added**
 - rendering for basic markdown language (`.md`)
 - rendering for basic JSON (`.json`) 
 - general routing for pages and modules
 - kept the posts module as a core module
 - routed for `/admin` 
----------

~~This page utilizes Markdown Language (`.md`) is is provided by the Chaos CMS Custom **Markdown Rendering Engine**.~~
