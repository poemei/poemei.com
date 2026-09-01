<?php
/* [AI:GPT-5 | 2026-08-26 22:49:59 UTC] */
$og = $og ?? [];

$og_title = $og['title'] ?? 'Chaos MVC';
$og_desc = $og['desc'] ?? 'Lightweight * Model * View * Controller';
$og_url = $og['url'] ?? URLROOT;
$og_image = $og['image'] ?? theme::assetUrl('icons/icon.png');
$og_type = $og['type'] ?? 'article';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($SITE['name'] ?? 'Chaos MVC', ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="poe,mei,transgender,retired,soldier,developer,php,mysql,html,html5,css,css3,go,goloang">
	<meta name="description" content="<?= htmlspecialchars($SITE['description'] ?? 'Chaos MVC', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="<?= htmlspecialchars($SITE['name'] ?? 'Chaos MVC', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="copyright" content="<?= htmlspecialchars($SITE['name'] ?? 'Chaos MVC', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="application-name" content="Chaos MVC">

    <!-- For Facebook -->
    <meta property="og:title" content="<?= htmlspecialchars($og_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?= htmlspecialchars($og_url, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($og_desc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="<?= htmlspecialchars($og_type, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- For Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars($og_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($og_desc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:url" content="<?= htmlspecialchars($og_url, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:type" content="<?= htmlspecialchars($og_type, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >

    <!-- Poe Mei CSS -->
    <link
        rel="stylesheet"
        href="<?= theme::assetUrl('css/site.css') ?>"
    >

    <!-- Site Icon -->
    <link
        rel="icon"
        type="image/png"
        href="<?= theme::assetUrl('icons/icon.png') ?>"
    >
</head>
<body>
<div class="container">
<?php include __DIR__ . '/nav.php'; ?>
<main class="pm-main">
<?php /* [End AI:GPT-5] */ ?>