<?php
if (!function_exists('escape_html')) {
    function escape_html($v): string {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

if (!isset($nav) || !is_array($nav) || count($nav) === 0) {
    echo '<nav class="nav"></nav>';
    return;
}

echo '<nav class="nav">';
foreach ($nav as $navItem) {
    $rawHref   = $navItem['href']  ?? '#';
    $label     = $navItem['label'] ?? '';

    // Normalize href to compare
    $hrefTrim = trim((string)$rawHref, '/');   // "/" -> ""
    $slugPart = $hrefTrim !== '' ? $hrefTrim : 'home';

    // Active if current slug starts with the href slug
    $isActive = ($slugPart === 'home' && $slug === 'home')
                || ($slugPart !== 'home' && str_starts_with($slug, $slugPart));

    $class = $isActive ? 'active' : '';
    $aria  = $isActive ? ' aria-current="page"' : '';

    echo '<a class="' . $class . '" href="' . escape_html($rawHref) . '"' . $aria . '>'
       . escape_html($label)
       . '</a>';
}
?>
<div class="ms-auto d-flex align-items-center gap-2">
  <?php if (!empty($_SESSION['admin'])): ?>
    <a href="/admin">Admin</a>
    <a href="/admin?action=account">My Account</a>
    <a href="/admin?action=logout">Logout</a>
  <?php else: ?>
    <a href="/admin?action=login">Login</a>
    <a class="btn btn-sm btn-outline-secondary" href="/admin?action=register">Register</a>
  <?php endif; ?>
</div>

<?php
echo '</nav>';
?>
