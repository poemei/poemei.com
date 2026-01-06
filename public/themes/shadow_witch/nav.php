<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['auth']) && is_array($_SESSION['auth']) && !empty($_SESSION['auth']['id']);
$role       = $isLoggedIn ? (string)($_SESSION['auth']['role'] ?? '') : '';
$isAdmin    = ($role === 'admin');

$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

function nav_active(string $href, string $current): string
{
    return ($href === '/' && $current === '/') || ($href !== '/' && str_starts_with($current, $href))
        ? ' active'
        : '';
}

global $auth;

$nav = utility::nav_state($auth);
?>

<nav class="sw-nav">
    <ul class="sw-nav-list">
        <li><a href="/" class="<?= trim(nav_active('/', $current)); ?>">Home</a></li>
        <li><a href="/media" class="<?= trim(nav_active('/media', $current)); ?>">Media</a></li>
        <li><a href="/posts" class="<?= trim(nav_active('/posts', $current)); ?>">Posts</a></li>
        <li><a href="/projects" class="<?= trim(nav_active('/projects', $current)); ?>">Projects</a></li>
        <li><a href="/resume" class="<?= trim(nav_active('/resume', $current)); ?>">Resume</a></li>
        <?php if ($nav['logged_in']): ?>
        <li>
            <a href="/profile" class="<?= utility::nav_active('/profile'); ?>">
                Account<?= $nav['username'] ? ' (' . e($nav['username']) . ')' : ''; ?>
            </a>
        </li>
        <?php if ($nav['can_admin']): ?>
            <li>
                <a href="/admin" class="<?= utility::nav_active('/admin'); ?>">Admin</a>
            </li>
            <li>
                <a href="/logout" class="<?= utility::nav_active('/logout'); ?>">Logout</a>
            </li>
        <?php endif; ?>
        <?php else: ?>
        <li>
            <a href="/login" class="<?= utility::nav_active('/login'); ?>">Login</a>
        </li>
        <li>
            <a href="/signup" class="<?= utility::nav_active('/signup'); ?>">Sign Up</a>
        </li>
    <?php endif; ?>

        
    </ul>
</nav>

