<?php
$current = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($current === '/') {
    $current = '/'; 
}
global $auth;

$logged_in = $auth instanceof auth && $auth->check();
$user      = $logged_in ? $auth->user() : null;
$is_admin  = is_array($user) && ($user['role'] ?? '') === 'admin';
?>

<!-- Handle the html demand for an H1 -->
  <div class="row">
    <nav class="navbar">
    <a href="/" class="navbar-brand">
        <span class="navbar-brand-icon">
            <!-- Inline SVG or <img> -->
            <img src="/public/themes/default/assets/icons/icon.svg" alt="Chaos >
        </span>
        <span class="navbar-brand-text">Chaos CMS</span>
    </a>
    
<ul class="navbar-nav">
    <li><a href="/" class="<?php echo ($current === '/') ? 'active' : ''; ?>">Home</a></li>
    <li><a href="/posts" class="<?php echo ($current === '/posts') ? 'active' : ''; ?>">Posts</a></li>
    <li><a href="/pages/changelog" class="<?php echo ($current === '/pages/changelog') ? 'active' : ''; ?>">Changelog</a></li>
    <li><a href="/codex" class="<?php echo ($current === '/codex') ? 'active' : ''; ?>">Codex</a></li>
    <li><a href="/docs" class="<?php echo ($current === '/docs') ? 'active' : ''; ?>">Docs</a></li>
    <li><a href="/media" class="<?php echo ($current === '/media') ? 'active' : ''; ?>">Media</a></li>

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
  </div>


