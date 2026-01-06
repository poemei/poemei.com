<?php
// Vars expected from bootstrap:
// $site_name (string), $site_theme (string), $nav (array), $current_slug (string)

// Harden types & fallbacks (no one-letter vars)
$siteName    = isset($site_name)    ? (string)$site_name    : 'Chaos CMS Lite';
$siteTheme   = isset($site_theme)   ? (string)$site_theme   : 'minimal';
$navItems    = (isset($nav) && is_array($nav)) ? $nav : [];
$currentSlug = isset($slug) ? (string)$slug : 'home';

// Simple escaper ? avoids htmlspecialchars(null) deprecations
if (!function_exists('e')) {
  function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/public/themes/<?= $site_theme ?>/assets/images/icon.png">
  <link rel="stylesheet" href="/public/themes/<?= $site_theme ?>/assets/css/minimal.css">
  <link rel="stylesheet" href="/app/assets/css/site.css">
  
  <!-- Sitemaps, Google, ROR: Generated from the SEO Plugin -->
  <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
  <link rel="alternate" type="application/rss+xml" title="ROR" href="/ror.xml" />
  <title><?= $site_name ?></title>
</head>
<body>

<header class="header container py-3 d-flex align-items-center justify-content-between">
  <div class="d-flex align-items-center gap-2">
    <a class="brand navbar-brand" href="/">
      <img src="/public/themes/<?= $site_theme ?>/assets/images/icon.svg"
           alt="<?= $site_name ?>" width="30" height="24">
    </a>
    <strong><?= $site_name ?></strong>
  </div>
  <?php
  include __DIR__ . '/nav.php';
  ?>
</header>
<main class="container py-3">
  <div class="row">