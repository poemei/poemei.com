<?php
declare(strict_types=1);

global $db;

if (!$db instanceof db) {
    echo '<div class="container my-4"><p>Codex unavailable.</p></div>';
    return;
}

$path  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$parts = array_values(array_filter(explode('/', trim($path, '/'))));

$action = $parts[1] ?? ''; // topic | page | search | api
$arg    = $parts[2] ?? '';

/**
 * Escape HTML.
 */
function codex_e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

/**
 * Ensure text-based Markdown renderer is loaded.
 * (Chaos core lib: /app/lib/render_md.php)
 */
function codex_require_md(): void
{
    if (class_exists('render_md')) {
        return;
    }

    $docroot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
    if ($docroot === '') {
        $docroot = rtrim(dirname(__DIR__, 3), '/\\');
    }

    $lib = $docroot . '/app/lib/render_md.php';

    if (is_file($lib)) {
        require_once $lib;
    }
}

/**
 * Render content based on format.
 * - md  => uses core text markdown engine (render_md)
 * - html => raw html (trusted content only)
 * - fallback => escaped text with nl2br
 */
function codex_render(string $body, string $format): string
{
    $format = strtolower(trim($format));

    if ($format === 'html') {
        return $body;
    }

    if ($format === 'md') {
        // Prevent accidental HTML tags being interpreted before markdown handles them
        // (Matches core behavior you’ve been using elsewhere.)
        //$body = (string) preg_replace('~<([a-z0-9_-]+)>~i', '&lt;$1&gt;', $body);

        codex_require_md();

        if (class_exists('render_md')) {
            $md = new render_md();
            $out = $md->markdown($body);

            return is_string($out) ? $out : nl2br(codex_e($body));
        }

        return nl2br(codex_e($body));
    }

    return nl2br(codex_e($body));
}

/**
 * Build breadcrumb.
 *
 * @param array<int,array{label:string,url:string}> $items
 */
function codex_breadcrumb(array $items): string
{
    $out  = '<small><em>';
    $last = count($items) - 1;

    foreach ($items as $i => $it) {
        $label = codex_e($it['label']);
        $url   = (string) $it['url'];

        if ($i > 0) {
            $out .= ' &raquo; ';
        }

        if ($i === $last || $url === '') {
            $out .= $label;
        } else {
            $out .= '<a href="' . codex_e($url) . '">' . $label . '</a>';
        }
    }

    $out .= '</em></small>';

    return $out;
}

/**
 * Search form.
 */
function codex_search_form(string $q, string $actionUrl = '/codex/search'): string
{
    $q = trim($q);

    $out  = '<form method="get" action="' . codex_e($actionUrl) . '" class="mb-3">';
    $out .= '  <div class="input-group">';
    $out .= '    <input class="form-control" name="q" value="' . codex_e($q) . '" placeholder="Search the Codex...">';
    $out .= '    <button class="btn btn-outline-secondary" type="submit">Search</button>';
    $out .= '  </div>';
    $out .= '</form>';

    return $out;
}

/**
 * Get public topics.
 *
 * @return array<int,array{id:int,slug:string,label:string}>
 */
function codex_topics_public(): array
{
    global $db;

    $rows = $db->fetch_all("
        SELECT id, slug, label
        FROM codex_topics
        WHERE is_public=1
        ORDER BY sort_order ASC, label ASC
    ");

    return is_array($rows) ? $rows : [];
}

/**
 * Find public topic by slug.
 *
 * @return array{id:int,slug:string,label:string}|null
 */
function codex_topic_by_slug(string $slug): ?array
{
    $topics = codex_topics_public();

    foreach ($topics as $t) {
        if (($t['slug'] ?? '') === $slug) {
            return $t;
        }
    }

    return null;
}

/**
 * Count public entries per topic.
 *
 * @return array<int,int>
 */
function codex_topic_counts(): array
{
    global $db;

    $rows = $db->fetch_all("
        SELECT topic_id, COUNT(*) AS c
        FROM codex
        WHERE status=1 AND visibility=0 AND topic_id IS NOT NULL
        GROUP BY topic_id
    ");

    $rows = is_array($rows) ? $rows : [];

    $map = [];
    foreach ($rows as $r) {
        $tid = (int) ($r['topic_id'] ?? 0);
        $cnt = (int) ($r['c'] ?? 0);

        if ($tid > 0) {
            $map[$tid] = $cnt;
        }
    }

    return $map;
}

/**
 * Get entries for topic (public+enabled).
 *
 * @return array<int,array{slug:string,title:string}>
 */
function codex_entries_by_topic(int $topicId): array
{
    global $db;

    $rows = $db->fetch_all("
        SELECT slug, title
        FROM codex
        WHERE topic_id={$topicId}
          AND status=1
          AND visibility=0
        ORDER BY title ASC
    ");

    return is_array($rows) ? $rows : [];
}

/**
 * Recent entries (public+enabled).
 *
 * @return array<int,array{slug:string,title:string}>
 */
function codex_recent_entries(int $limit = 10): array
{
    global $db;

    $limit = max(1, min(50, $limit));

    $rows = $db->fetch_all("
        SELECT slug, title
        FROM codex
        WHERE status=1 AND visibility=0
        ORDER BY COALESCE(updated_at, created_at) DESC
        LIMIT {$limit}
    ");

    return is_array($rows) ? $rows : [];
}

/**
 * Search entries (public+enabled).
 *
 * @return array<int,array{slug:string,title:string,topic_label:string|null}>
 */
function codex_search_entries(string $q): array
{
    global $db;

    $q = trim($q);
    if ($q === '') {
        return [];
    }

    $link = $db->connect();
    $safe = $link->real_escape_string($q);

    $rows = $db->fetch_all("
        SELECT c.slug, c.title, t.label AS topic_label
        FROM codex c
        LEFT JOIN codex_topics t ON t.id=c.topic_id
        WHERE c.status=1
          AND c.visibility=0
          AND (c.title LIKE '%{$safe}%' OR c.slug LIKE '%{$safe}%' OR c.body LIKE '%{$safe}%')
        ORDER BY COALESCE(c.updated_at, c.created_at) DESC
        LIMIT 100
    ");

    return is_array($rows) ? $rows : [];
}

/**
 * Get single entry (public+enabled).
 *
 * @return array<string,mixed>|null
 */
function codex_entry(string $slug): ?array
{
    global $db;

    $link = $db->connect();
    $safe = $link->real_escape_string($slug);

    $row = $db->fetch("
        SELECT c.*, t.label AS topic_label, t.slug AS topic_slug
        FROM codex c
        LEFT JOIN codex_topics t ON t.id=c.topic_id
        WHERE c.slug='{$safe}'
          AND c.status=1
          AND c.visibility=0
        LIMIT 1
    ");

    return is_array($row) ? $row : null;
}

/* ============================================================
 * ROUTES
 * ============================================================
 */

// /codex/search?q=...
if ($action === 'search') {
    $q = (string) ($_GET['q'] ?? '');
    $q = trim($q);

    echo '<div class="container my-5">';
    echo codex_breadcrumb([
        ['label' => 'Codex', 'url' => '/codex'],
        ['label' => 'Search', 'url' => ''],
    ]);
    echo '<h1 class="mt-2 mb-3">Search</h1>';

    echo codex_search_form($q);

    if ($q === '') {
        echo '<p class="text-muted">Type something to search titles, slugs, and content.</p>';
        echo '</div>';
        return;
    }

    $results = codex_search_entries($q);

    if (!$results) {
        echo '<p class="text-muted">No matches for <strong>' . codex_e($q) . '</strong>.</p>';
        echo '</div>';
        return;
    }

    echo '<div class="list-group">';
    foreach ($results as $r) {
        $title = codex_e((string) ($r['title'] ?? 'Untitled'));
        $slug  = codex_e((string) ($r['slug'] ?? ''));
        $topic = codex_e((string) ($r['topic_label'] ?? 'Uncategorized'));

        echo '<a class="list-group-item list-group-item-action" href="/codex/page/' . $slug . '">';
        echo '  <div class="fw-semibold">' . $title . '</div>';
        echo '  <div class="text-muted small">' . $topic . '</div>';
        echo '</a>';
    }
    echo '</div>';
    echo '</div>';
    return;
}

// /codex/api
if ($action === 'api') {
    $apiTopic = codex_topic_by_slug('api-reference');

    echo '<div class="container my-5">';
    echo codex_breadcrumb([
        ['label' => 'Codex', 'url' => '/codex'],
        ['label' => 'API Reference', 'url' => ''],
    ]);
    echo '<h1 class="mt-2 mb-3">API Reference</h1>';

    if (!$apiTopic) {
        echo '<div class="alert alert-warning">';
        echo '<div class="fw-semibold mb-1">API Reference topic not found.</div>';
        echo '<div class="small">Create a Codex topic with slug <code>api-reference</code> and label <code>API Reference</code>.</div>';
        echo '</div>';
        echo '</div>';
        return;
    }

    $entries = codex_entries_by_topic((int) $apiTopic['id']);

    if (!$entries) {
        echo '<p class="text-muted">No API entries yet.</p>';
        echo '</div>';
        return;
    }

    echo '<ul class="mt-3">';
    foreach ($entries as $e) {
        echo '<li><a href="/codex/page/' . codex_e($e['slug']) . '">' . codex_e($e['title']) . '</a></li>';
    }
    echo '</ul>';

    echo '</div>';
    return;
}

// /codex (home)
if ($action === '') {
    $topics = codex_topics_public();
    $counts = codex_topic_counts();
    $recent = codex_recent_entries(10);

    echo '<div class="container my-5">';
    echo '<h1 class="mb-2">Codex</h1>';
    echo '<div class="text-muted mb-3">Developer reference: modules, plugins, themes, and core utilities.</div>';

    echo codex_search_form('');

    echo '<div class="row g-3">';

    // Topics
    echo '<div class="col-12 col-lg-8">';
    echo '<div class="row g-3">';

    foreach ($topics as $t) {
        $tid   = (int) ($t['id'] ?? 0);
        $label = codex_e((string) ($t['label'] ?? ''));
        $slug  = codex_e((string) ($t['slug'] ?? ''));
        $cnt   = (int) ($counts[$tid] ?? 0);

        echo '<div class="col-12 col-md-6">';
        echo '  <a href="/codex/topic/' . $slug . '" class="text-decoration-none text-reset d-block">';
        echo '    <div class="card h-100">';
        echo '      <div class="card-body">';
        echo '        <div class="d-flex justify-content-between align-items-center mb-2">';
        echo '          <div class="fw-semibold">' . $label . '</div>';
        echo '          <span class="badge rounded-pill text-bg-secondary">' . $cnt . '</span>';
        echo '        </div>';
        echo '        <div class="text-muted small">Browse entries under this topic.</div>';
        echo '      </div>';
        echo '    </div>';
        echo '  </a>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';

    // Tools
    echo '<div class="col-12 col-lg-4">';

    echo '<div class="card mb-3">';
    echo '  <div class="card-body">';
    echo '    <div class="fw-semibold mb-1">API Reference</div>';
    echo '    <div class="text-muted small mb-2">Index of callable helpers, functions, and interfaces.</div>';
    echo '    <a class="btn btn-sm btn-outline-secondary" href="/codex/api">Open API Index</a>';
    echo '  </div>';
    echo '</div>';

    echo '<div class="card">';
    echo '  <div class="card-body">';
    echo '    <div class="fw-semibold mb-2">Recently Updated</div>';

    if (!$recent) {
        echo '    <div class="text-muted small">No entries yet.</div>';
    } else {
        echo '    <ul class="mb-0">';
        foreach ($recent as $r) {
            $title = codex_e((string) ($r['title'] ?? 'Untitled'));
            $slug  = codex_e((string) ($r['slug'] ?? ''));
            echo '      <li><a href="/codex/page/' . $slug . '">' . $title . '</a></li>';
        }
        echo '    </ul>';
    }

    echo '  </div>';
    echo '</div>';

    echo '</div>'; // tools

    echo '</div>'; // row
    echo '</div>'; // container
    return;
}

// /codex/topic/<slug>
if ($action === 'topic' && $arg !== '') {
    $topic = codex_topic_by_slug($arg);

    if (!$topic) {
        echo '<div class="container my-4"><h1>Topic not found</h1></div>';
        return;
    }

    $entries = codex_entries_by_topic((int) $topic['id']);

    echo '<div class="container my-5">';
    echo codex_breadcrumb([
        ['label' => 'Codex', 'url' => '/codex'],
        ['label' => (string) $topic['label'], 'url' => ''],
    ]);
    echo '<h1 class="mt-2 mb-3">' . codex_e((string) $topic['label']) . '</h1>';

    echo codex_search_form('', '/codex/search');

    if (!$entries) {
        echo '<p class="text-muted">No entries yet.</p>';
        echo '</div>';
        return;
    }

    echo '<ul class="mt-3">';
    foreach ($entries as $e) {
        echo '<li><a href="/codex/page/' . codex_e($e['slug']) . '">' . codex_e($e['title']) . '</a></li>';
    }
    echo '</ul>';

    echo '</div>';
    return;
}

// /codex/page/<slug>
if ($action === 'page' && $arg !== '') {
    $entry = codex_entry($arg);

    if (!$entry) {
        echo '<div class="container my-4"><h1>Document not found</h1></div>';
        return;
    }

    $topicLabel = (string) ($entry['topic_label'] ?? 'Codex');
    $topicSlug  = (string) ($entry['topic_slug'] ?? '');

    $crumb = [
        ['label' => 'Codex', 'url' => '/codex'],
    ];

    if ($topicSlug !== '' && $topicLabel !== '') {
        $crumb[] = ['label' => $topicLabel, 'url' => '/codex/topic/' . $topicSlug];
    }

    $crumb[] = ['label' => (string) ($entry['title'] ?? 'Document'), 'url' => ''];

    echo '<div class="container my-5">';
    echo codex_breadcrumb($crumb);

    echo '<h1 class="mt-2">' . codex_e((string) $entry['title']) . '</h1>';
    echo '<div class="mt-4">';
    echo codex_render((string) ($entry['body'] ?? ''), (string) ($entry['format'] ?? 'md'));
    echo '</div>';

    echo '</div>';
    return;
}

echo '<div class="container my-4"><p>Invalid Codex route.</p></div>';

