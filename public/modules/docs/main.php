<?php

declare(strict_types=1);

global $db;

$path  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$parts = array_values(array_filter(explode('/', trim($path, '/'))));

$moduleSlug = $parts[0] ?? ''; // docs
$action     = $parts[1] ?? ''; // page
$arg1       = $parts[2] ?? ''; // slug

/**
 * Render docs breadcrumb.
 *
 * @param string $title
 */
function docs_breadcrumb(string $title): void
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    echo '<small><em>';
    echo '<a href="/docs">Docs</a> &raquo; ' . $safeTitle;
    echo '</em></small>';
}

/**
 * Ensure Markdown renderer is available (text -> HTML).
 */
function docs_require_md(): void
{
    if (class_exists('render_md')) {
        return;
    }

    $docroot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)), '/\\');
    $lib     = $docroot . '/app/lib/render_md.php';

    if (is_file($lib)) {
        require_once $lib;
    }
}

/**
 * Render docs body content.
 *
 * Rules:
 * - If body contains HTML tags -> output as-is.
 * - Else -> treat as Markdown text and render via render_md.
 * - If render_md unavailable -> safe fallback.
 *
 * @param string $body
 * @return string
 */
function docs_render_body(string $body): string
{
    $body = trim($body);

    if ($body === '') {
        return '';
    }

    // Load Markdown renderer (DB text -> HTML)
    if (!class_exists('render_md')) {
        $docroot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3)), '/\\');
        $lib     = $docroot . '/app/lib/render_md.php';

        if (is_file($lib)) {
            require_once $lib;
        }
    }

    if (class_exists('render_md')) {
        $md = new render_md();
        return (string) $md->markdown($body);
    }

    // Last resort
    return nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
}


/**
 * Fetch all docs, newest first.
 *
 * @return array<int,array<string,mixed>>
 */
function docs_get_all(): array
{
    global $db;

    if (!$db instanceof db) {
        return [];
    }

    return (array) $db->fetch_all("
        SELECT id, slug, title, body, updated_at
        FROM docs
        ORDER BY updated_at DESC
    ");
}

/**
 * Fetch a single doc by slug (prepared statement).
 *
 * @param string $slug
 * @return array<string,mixed>|null
 */
function docs_get(string $slug): ?array
{
    global $db;

    if (!$db instanceof db) {
        return null;
    }

    $conn = $db->connect();
    if (!$conn instanceof mysqli) {
        return null;
    }

    $stmt = $conn->prepare('SELECT id, slug, title, body, updated_at FROM docs WHERE slug=? LIMIT 1');
    if ($stmt === false) {
        return null;
    }

    $stmt->bind_param('s', $slug);
    $stmt->execute();

    $res = $stmt->get_result();
    $row = $res instanceof mysqli_result ? $res->fetch_assoc() : null;

    $stmt->close();

    return is_array($row) ? $row : null;
}

function docs_index(): void
{
    $items = docs_get_all();

    echo '<div class="container my-4">';
    echo '<h1>Docs</h1>';

    if (!$items) {
        echo '<p class="text-muted">No documents yet.</p>';
        echo '</div>';
        return;
    }

    echo '<ul>';

    foreach ($items as $doc) {
        $slug  = htmlspecialchars((string) ($doc['slug'] ?? ''), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars((string) ($doc['title'] ?? ''), ENT_QUOTES, 'UTF-8');

        echo '<li><a href="/docs/page/' . $slug . '">' . $title . '</a></li>';
    }

    echo '</ul>';
    echo '</div>';
}

function docs_view(string $slug): void
{
    if ($slug === '') {
        docs_not_found();
        return;
    }

    $doc = docs_get($slug);
    if (!is_array($doc)) {
        docs_not_found();
        return;
    }

    $titleRaw = (string) ($doc['title'] ?? '');
    $title    = htmlspecialchars($titleRaw, ENT_QUOTES, 'UTF-8');
    $bodyHtml = docs_render_body((string) ($doc['body'] ?? ''));

    echo '<div class="container my-4">';
    docs_breadcrumb($titleRaw);
    echo '<h1 class="mt-2">' . $title . '</h1>';
    echo '<div class="mt-3">' . $bodyHtml . '</div>';
    echo '</div>';
}

function docs_not_found(): void
{
    echo '<div class="container my-4">';
    echo '<h1>Sorry</h1>';
    echo '<p>That document can not be found in this module.</p>';
    echo '</div>';
}

// Dispatch
switch ($action) {
    case '':
        docs_index();
        break;

    case 'page':
        docs_view($arg1);
        break;

    default:
        docs_not_found();
        break;
}

