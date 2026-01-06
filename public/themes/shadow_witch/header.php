<?php
declare(strict_types=1);

/** @var array $SITE */
/** @var array $PAGE */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        <?= htmlspecialchars($PAGE['title'] ?? $SITE['name'] ?? 'Poe Mei', ENT_QUOTES, 'UTF-8'); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Shadow Witch CSS -->
    <link rel="stylesheet" href="/public/themes/shadow_witch/assets/css/shadow_witch.css">
    
    <!-- Site Icon -->
    <link rel="icon" type="image/x-icon" href="/public/themes/shadow_witch/assets/icons/icon.png">
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

