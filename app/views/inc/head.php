<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        Poe Mei
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="" />

    <meta name="author" content="Poe Mei" />
    <meta name="copyright" content="Poe Mei" />
    <meta name="application-name" content="Poe Mei" />

    <!-- For Facebook -->
    <meta property="og:title" content="<?= htmlspecialchars($PAGE['title'] ?? $SITE['name'] ?? 'Poe Mei', ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:image" content="https://www.poemei.com/assets/icons/icon.png" />
    <meta property="og:url" content="https://www.poemei.com" />
    <meta property="og:description" content="Code * systems * Chaos * Witch" />

    <!-- For Twitter -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?= htmlspecialchars($PAGE['title'] ?? $SITE['name'] ?? 'Poe Mei', ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:description" content="Code * systems * Chaos * Witch" />
    <meta name="twitter:url" content="https://www.poemei.com" />
    <meta name="twitter:image" content="https://www.poemei.com/assets/icons/icon.png" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Shadow Witch CSS -->
    <link rel="stylesheet" href="<?= URLROOT ?>/assets/css/poemei.css">
    
    <!-- Site Icon -->
    <link rel="icon" type="image/x-icon" href="<?= URLROOT ?>/assets/icons/icon.png">
</head>
<body class="sw-body">

<header class="sw-hero">
    <div class="sw-hero-image"></div>
    <div class="sw-hero-overlay"></div>

    <div class="sw-hero-content">
        <h1 class="sw-hero-title">
            <?= htmlspecialchars($SITE['name'] ?? 'Poe Mei', ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <p class="sw-hero-subtitle">
            PHP Developer · System Architect · Shadow Witch
        </p>

        <div class="sw-hero-meta">
            <span>Code · Systems · Chaos</span>
        </div>
    </div>
</header>
<?php
include __DIR__ . '/nav.php';
?>
<main class="sw-main">
