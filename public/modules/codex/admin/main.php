<?php

declare(strict_types=1);

global $db;

if (!$db instanceof db) {
    echo '<div class="container my-4"><div class="alert alert-danger">DB not available.</div></div>';
    return;
}

$conn = $db->connect();
if (!$conn instanceof mysqli) {
    echo '<div class="container my-4"><div class="alert alert-danger">DB connection failed.</div></div>';
    return;
}

/**
 * Escape HTML.
 *
 * @param string $v
 * @return string
 */
 
 //error_log('CODEX topic_id POST=' . var_export($_POST['topic_id'] ?? null, true));
function codex_e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

/**
 * Slug sanitizer.
 *
 * @param string $slug
 * @return string
 */
function codex_slug_clean(string $slug): string
{
    $slug = strtolower(trim($slug));
    $slug = (string) preg_replace('~[^a-z0-9_\-]~i', '-', $slug);
    $slug = (string) preg_replace('~-+~', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}

/**
 * Load codex topics.
 *
 * @return array<int,array{id:int,slug:string,label:string,sort_order:int,is_public:int}>
 */
function codex_topics_all(): array
{
    global $db;

    if (!$db instanceof db) {
        return [];
    }

    $rows = $db->fetch_all("
        SELECT id, slug, label, sort_order, is_public
        FROM codex_topics
        ORDER BY sort_order ASC, label ASC
    ");

    return is_array($rows) ? $rows : [];
}

/**
 * Load entries (filtered).
 *
 * @param string $q
 * @param int $topicId
 * @return array<int,array<string,mixed>>
 */
function codex_entries_list(string $q, int $topicId): array
{
    global $db;

    if (!$db instanceof db) {
        return [];
    }

    $q = trim($q);

    $where = "1=1";

    if ($topicId > 0) {
        $where .= " AND c.topic_id=" . (int) $topicId;
    }

    if ($q !== '') {
        $link = $db->connect();
        if ($link instanceof mysqli) {
            $safe = $link->real_escape_string($q);
            $where .= " AND (c.title LIKE '%{$safe}%' OR c.slug LIKE '%{$safe}%')";
        }
    }

    $rows = $db->fetch_all("
        SELECT c.id, c.slug, c.title, c.format, c.status, c.visibility,
               COALESCE(c.updated_at, c.created_at) AS touched_at,
               t.label AS topic_label
        FROM codex c
        LEFT JOIN codex_topics t ON t.id=c.topic_id
        WHERE {$where}
        ORDER BY touched_at DESC
        LIMIT 200
    ");

    return is_array($rows) ? $rows : [];
}

$view = (string) ($_GET['view'] ?? 'dashboard');
$do   = (string) ($_GET['do'] ?? '');
$id   = (int) ($_GET['id'] ?? 0);

$csrf = function_exists('csrf_token') ? (string) csrf_token() : '';

$flashErr = '';
$flashOk  = '';

// -----------------------------------------------------------------------------
// POST handling
// -----------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $token = (string) ($_POST['csrf'] ?? '');

    if (function_exists('csrf_ok') && !csrf_ok($token)) {
        $flashErr = 'Invalid CSRF token.';
    } else {
        $act = (string) ($_POST['act'] ?? '');

        // Topics
        if ($act === 'topic_save') {
            $tid       = (int) ($_POST['id'] ?? 0);
            $slug      = codex_slug_clean((string) ($_POST['slug'] ?? ''));
            $label     = trim((string) ($_POST['label'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $isPublic  = (int) ($_POST['is_public'] ?? 1);

            if ($slug === '' || $label === '') {
                $flashErr = 'Topic slug and label are required.';
            } else {
                if ($tid > 0) {
                    $stmt = $conn->prepare("UPDATE codex_topics SET slug=?, label=?, sort_order=?, is_public=? WHERE id=? LIMIT 1");
                    if ($stmt) {
                        $stmt->bind_param('ssiii', $slug, $label, $sortOrder, $isPublic, $tid);
                        $stmt->execute();
                        $stmt->close();
                        $flashOk = 'Topic saved.';
                    }
                } else {
                    $stmt = $conn->prepare("INSERT INTO codex_topics (slug, label, sort_order, is_public) VALUES (?, ?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param('ssii', $slug, $label, $sortOrder, $isPublic);
                        $stmt->execute();
                        $stmt->close();
                        $flashOk = 'Topic created.';
                    }
                }
            }

            $view = 'topics';
        }

        if ($act === 'topic_delete') {
            $tid = (int) ($_POST['id'] ?? 0);
            if ($tid > 0) {
                $stmt = $conn->prepare("DELETE FROM codex_topics WHERE id=? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('i', $tid);
                    $stmt->execute();
                    $stmt->close();
                    $flashOk = 'Topic deleted.';
                }
            }

            $view = 'topics';
        }

        // Entries
        if ($act === 'entry_save') {
            $eid        = (int) ($_POST['id'] ?? 0);
            $slug       = codex_slug_clean((string) ($_POST['slug'] ?? ''));
            $title      = trim((string) ($_POST['title'] ?? ''));
            $body       = (string) ($_POST['body'] ?? '');
            $format     = strtolower(trim((string) ($_POST['format'] ?? 'md')));
            $topicId    = (int) ($_POST['topic_id'] ?? 0);
            $status     = (int) ($_POST['status'] ?? 1);
            $visibility = (int) ($_POST['visibility'] ?? 0);

            if ($format !== 'html') {
                $format = 'md';
            }

            // HARD GUARD: do not allow edit-save to wipe content
            if ($eid > 0 && trim($body) === '') {
                $prev = null;

                $stmt = $conn->prepare("SELECT body FROM codex WHERE id=? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('i', $eid);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res instanceof mysqli_result ? $res->fetch_assoc() : null;
                    $stmt->close();

                    if (is_array($row) && isset($row['body'])) {
                        $prev = (string) $row['body'];
                    }
                }

                if (is_string($prev) && trim($prev) !== '') {
                    $flashErr = 'Save blocked: body was empty. This prevents accidental deletion.';
                }
            }

            if ($flashErr === '') {
                if ($slug === '' || $title === '') {
                    $flashErr = 'Entry slug and title are required.';
                }
            }

            if ($flashErr === '') {
                // KISS: avoid nullable binding weirdness; use explicit SQL
                if ($eid > 0) {
                    if ($topicId > 0) {
                        $stmt = $conn->prepare("
                            UPDATE codex
                            SET topic_id=?, slug=?, title=?, body=?, format=?, status=?, visibility=?
                            WHERE id=? LIMIT 1
                        ");

                        if ($stmt) {
                            $stmt->bind_param('issssiii', $topicId, $slug, $title, $body, $format, $status, $visibility, $eid);
                            $stmt->execute();
                            $stmt->close();
                            $flashOk = 'Entry saved.';
                        }
                    } else {
                        $stmt = $conn->prepare("
                            UPDATE codex
                            SET topic_id=NULL, slug=?, title=?, body=?, format=?, status=?, visibility=?
                            WHERE id=? LIMIT 1
                        ");

                        if ($stmt) {
                            $stmt->bind_param('ssssiii', $slug, $title, $body, $format, $status, $visibility, $eid);
                            $stmt->execute();
                            $stmt->close();
                            $flashOk = 'Entry saved.';
                        }
                    }
                } else {
                    if ($topicId > 0) {
                        $stmt = $conn->prepare("
                            INSERT INTO codex (topic_id, slug, title, body, format, status, visibility)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");

                        if ($stmt) {
                            $stmt->bind_param('issssii', $topicId, $slug, $title, $body, $format, $status, $visibility);
                            $stmt->execute();
                            $stmt->close();
                            $flashOk = 'Entry created.';
                        }
                    } else {
                        $stmt = $conn->prepare("
                            INSERT INTO codex (topic_id, slug, title, body, format, status, visibility)
                            VALUES (NULL, ?, ?, ?, ?, ?, ?)
                        ");

                        if ($stmt) {
                            $stmt->bind_param('ssssii', $slug, $title, $body, $format, $status, $visibility);
                            $stmt->execute();
                            $stmt->close();
                            $flashOk = 'Entry created.';
                        }
                    }
                }
            }

            $view = 'entries';
            $do   = 'edit';
            $id   = $eid > 0 ? $eid : 0;
        }

        if ($act === 'entry_delete') {
            $eid = (int) ($_POST['id'] ?? 0);
            if ($eid > 0) {
                $stmt = $conn->prepare("DELETE FROM codex WHERE id=? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('i', $eid);
                    $stmt->execute();
                    $stmt->close();
                    $flashOk = 'Entry deleted.';
                }
            }

            $view = 'entries';
            $do   = '';
            $id   = 0;
        }
    }
}

// -----------------------------------------------------------------------------
// Data for views
// -----------------------------------------------------------------------------
$topics = codex_topics_all();

$cntTopics = count($topics);

$cntEntriesRow = $db->fetch("SELECT COUNT(*) AS c FROM codex");
$cntEntries    = (int) (($cntEntriesRow['c'] ?? 0));

$recent = $db->fetch_all("
    SELECT id, slug, title, COALESCE(updated_at, created_at) AS touched_at
    FROM codex
    ORDER BY touched_at DESC
    LIMIT 8
");

$q       = (string) ($_GET['q'] ?? '');
$topicId = (int) ($_GET['topic_id'] ?? 0);

$entries = codex_entries_list($q, $topicId);

$edit = null;
if ($view === 'entries' && $do === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM codex WHERE id=? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res  = $stmt->get_result();
        $edit = $res instanceof mysqli_result ? $res->fetch_assoc() : null;
        $stmt->close();
    }
}

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------
echo '<div class="container my-4">';
echo '<div class="text-muted small">Admin &raquo; Module Admin &raquo; Codex</div>';
echo '<h1 class="h3 mt-2 mb-3">Codex</h1>';

if ($flashErr !== '') {
    echo '<div class="alert alert-danger">' . codex_e($flashErr) . '</div>';
}

if ($flashOk !== '') {
    echo '<div class="alert alert-success">' . codex_e($flashOk) . '</div>';
}

// Nav
echo '<div class="d-flex gap-2 mb-3">';
echo '<a class="btn btn-sm ' . ($view === 'dashboard' ? 'btn-primary' : 'btn-outline-secondary') . '" href="/admin?action=module_admin&amp;slug=codex&amp;view=dashboard">Dashboard</a>';
echo '<a class="btn btn-sm ' . ($view === 'topics' ? 'btn-primary' : 'btn-outline-secondary') . '" href="/admin?action=module_admin&amp;slug=codex&amp;view=topics">Topics</a>';
echo '<a class="btn btn-sm ' . ($view === 'entries' ? 'btn-primary' : 'btn-outline-secondary') . '" href="/admin?action=module_admin&amp;slug=codex&amp;view=entries">Entries</a>';
echo '</div>';

// Dashboard
if ($view === 'dashboard') {
    echo '<div class="row g-3">';
    echo '  <div class="col-12 col-lg-6"><div class="card"><div class="card-body">';
    echo '    <div class="fw-semibold mb-1">Codex Builder</div>';
    echo '    <div class="text-muted small mb-2">Developer documentation: modules, plugins, themes, and core utilities.</div>';
    echo '    <div class="text-muted small">Topics: <strong>' . $cntTopics . '</strong> &nbsp;|&nbsp; Entries: <strong>' . $cntEntries . '</strong></div>';
    echo '    <div class="mt-3 d-flex gap-2">';
    echo '      <a class="btn btn-sm btn-primary" href="/admin?action=module_admin&amp;slug=codex&amp;view=entries">Write an Entry</a>';
    echo '      <a class="btn btn-sm btn-outline-secondary" href="/admin?action=module_admin&amp;slug=codex&amp;view=topics">Manage Topics</a>';
    echo '      <a class="btn btn-sm btn-outline-secondary" href="/codex" target="_blank" rel="noopener noreferrer">View Codex</a>';
    echo '    </div>';
    echo '  </div></div></div>';

    echo '  <div class="col-12 col-lg-6"><div class="card"><div class="card-body">';
    echo '    <div class="fw-semibold mb-2">Recently Updated</div>';

    if (!is_array($recent) || count($recent) === 0) {
        echo '<div class="text-muted small">No entries yet.</div>';
    } else {
        echo '<ul class="mb-0">';
        foreach ($recent as $r) {
            $rid   = (int) ($r['id'] ?? 0);
            $title = codex_e((string) ($r['title'] ?? 'Untitled'));
            echo '<li><a href="/admin?action=module_admin&amp;slug=codex&amp;view=entries&amp;do=edit&amp;id=' . $rid . '">' . $title . '</a></li>';
        }
        echo '</ul>';
    }

    echo '  </div></div></div>';
    echo '</div>';
    echo '</div>';
    return;
}

// Topics view
if ($view === 'topics') {
    echo '<div class="row g-3">';

    echo '<div class="col-12 col-lg-6"><div class="card"><div class="card-body">';
    echo '<div class="fw-semibold mb-2">Topics</div>';

    if (!$topics) {
        echo '<div class="text-muted small">No topics yet.</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm align-middle">';
        echo '<thead><tr><th>Label</th><th>Slug</th><th class="text-end">Actions</th></tr></thead><tbody>';

        foreach ($topics as $t) {
            $tid   = (int) $t['id'];
            $label = codex_e((string) $t['label']);
            $slug  = codex_e((string) $t['slug']);

            echo '<tr>';
            echo '<td class="fw-semibold">' . $label . '</td>';
            echo '<td><code>' . $slug . '</code></td>';
            echo '<td class="text-end">';
            echo '<a class="btn btn-sm btn-outline-secondary" href="/admin?action=module_admin&amp;slug=codex&amp;view=topics&amp;do=edit&amp;id=' . $tid . '">Edit</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    }

    echo '</div></div></div>';

    $topicEdit = null;
    if ($do === 'edit' && $id > 0) {
        $stmt = $conn->prepare("SELECT * FROM codex_topics WHERE id=? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $topicEdit = $res instanceof mysqli_result ? $res->fetch_assoc() : null;
            $stmt->close();
        }
    }

    $tid   = (int) ($topicEdit['id'] ?? 0);
    $slug  = (string) ($topicEdit['slug'] ?? '');
    $label = (string) ($topicEdit['label'] ?? '');
    $sort  = (int) ($topicEdit['sort_order'] ?? 0);
    $pub   = (int) ($topicEdit['is_public'] ?? 1);

    echo '<div class="col-12 col-lg-6"><div class="card"><div class="card-body">';
    echo '<div class="fw-semibold mb-2">' . ($tid > 0 ? 'Edit Topic' : 'New Topic') . '</div>';

    echo '<form method="post">';
    echo '<input type="hidden" name="csrf" value="' . codex_e($csrf) . '">';
    echo '<input type="hidden" name="act" value="topic_save">';
    echo '<input type="hidden" name="id" value="' . $tid . '">';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ct_label">Label</label>';
    echo '<input id="ct_label" class="form-control" name="label" value="' . codex_e($label) . '" placeholder="Module Development">';
    echo '</div>';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ct_slug">Slug</label>';
    echo '<input id="ct_slug" class="form-control" name="slug" value="' . codex_e($slug) . '" placeholder="module-development">';
    echo '</div>';

    echo '<div class="row g-2 mb-2">';
    echo '<div class="col-6">';
    echo '<label class="form-label" for="ct_sort">Sort Order</label>';
    echo '<input id="ct_sort" class="form-control" name="sort_order" value="' . (int) $sort . '">';
    echo '</div>';

    echo '<div class="col-6">';
    echo '<label class="form-label" for="ct_pub">Public</label>';
    echo '<select id="ct_pub" class="form-select" name="is_public">';
    echo '<option value="1"' . ($pub === 1 ? ' selected' : '') . '>Yes</option>';
    echo '<option value="0"' . ($pub === 0 ? ' selected' : '') . '>No</option>';
    echo '</select>';
    echo '</div>';
    echo '</div>';

    echo '<div class="d-flex gap-2">';
    echo '<button class="btn btn-primary" type="submit">Save Topic</button>';
    echo '<a class="btn btn-outline-secondary" href="/admin?action=module_admin&amp;slug=codex&amp;view=topics">New</a>';
    echo '</div>';

    echo '</form>';

    if ($tid > 0) {
        echo '<form method="post" class="mt-2" onsubmit="return confirm(\'Delete this topic?\');">';
        echo '<input type="hidden" name="csrf" value="' . codex_e($csrf) . '">';
        echo '<input type="hidden" name="act" value="topic_delete">';
        echo '<input type="hidden" name="id" value="' . $tid . '">';
        echo '<button class="btn btn-outline-danger" type="submit">Delete Topic</button>';
        echo '</form>';
    }

    echo '</div></div></div>';

    echo '</div>';
    echo '</div>';
    return;
}

// Entries view
if ($view === 'entries') {
    $eid        = (int) ($edit['id'] ?? 0);
    $slugVal    = (string) ($edit['slug'] ?? '');
    $titleVal   = (string) ($edit['title'] ?? '');
    $bodyVal    = (string) ($edit['body'] ?? '');
    $formatVal  = strtolower(trim((string) ($edit['format'] ?? 'md')));
    $topicVal   = (int) ($edit['topic_id'] ?? 0);
    $statusVal  = (int) ($edit['status'] ?? 1);
    $visVal     = (int) ($edit['visibility'] ?? 0);

    if ($formatVal !== 'html') {
        $formatVal = 'md';
    }

    echo '<div class="row g-3">';

    echo '<div class="col-12 col-lg-5"><div class="card"><div class="card-body">';
    echo '<div class="d-flex justify-content-between align-items-center mb-2">';
    echo '<div class="fw-semibold">Entries</div>';
    echo '<a class="btn btn-sm btn-primary" href="/admin?action=module_admin&amp;slug=codex&amp;view=entries">New</a>';
    echo '</div>';

    echo '<form class="mb-2" method="get">';
    echo '<input type="hidden" name="action" value="module_admin">';
    echo '<input type="hidden" name="slug" value="codex">';
    echo '<input type="hidden" name="view" value="entries">';
    echo '<div class="row g-2">';
    echo '<div class="col-7"><input class="form-control" name="q" value="' . codex_e($q) . '" placeholder="Search title or slug"></div>';
    echo '<div class="col-5"><select class="form-select" name="topic_id">';
    echo '<option value="0">All Topics</option>';
    foreach ($topics as $t) {
        $tid = (int) $t['id'];
        $lbl = codex_e((string) $t['label']);
        $sel = ($topicId === $tid) ? ' selected' : '';
        echo '<option value="' . $tid . '"' . $sel . '>' . $lbl . '</option>';
    }
    echo '</select></div>';
    echo '</div>';
    echo '<div class="mt-2"><button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button></div>';
    echo '</form>';

    if (!$entries) {
        echo '<div class="text-muted small">No entries yet.</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm align-middle">';
        echo '<thead><tr><th>Title</th><th class="text-end">Edit</th></tr></thead><tbody>';
        foreach ($entries as $r) {
            $rid   = (int) ($r['id'] ?? 0);
            $title = codex_e((string) ($r['title'] ?? 'Untitled'));
            echo '<tr>';
            echo '<td class="fw-semibold">' . $title . '</td>';
            echo '<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="/admin?action=module_admin&amp;slug=codex&amp;view=entries&amp;do=edit&amp;id=' . $rid . '">Edit</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }

    echo '</div></div></div>';

    echo '<div class="col-12 col-lg-7"><div class="card"><div class="card-body">';
    echo '<div class="fw-semibold mb-2">' . ($eid > 0 ? 'Edit Entry' : 'New Entry') . '</div>';

    echo '<form method="post">';
    echo '<input type="hidden" name="csrf" value="' . codex_e($csrf) . '">';
    echo '<input type="hidden" name="act" value="entry_save">';
    echo '<input type="hidden" name="id" value="' . $eid . '">';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ce_topic">Codex Topic</label>';
    echo '<select id="ce_topic" class="form-select" name="topic_id">';
    echo '<option value="0">None</option>';
    foreach ($topics as $t) {
        $tid = (int) $t['id'];
        $lbl = codex_e((string) $t['label']);
        $sel = ($topicVal === $tid) ? ' selected' : '';
        echo '<option value="' . $tid . '"' . $sel . '>' . $lbl . '</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ce_slug">Slug</label>';
    echo '<input id="ce_slug" class="form-control" name="slug" value="' . codex_e($slugVal) . '" placeholder="codex-introduction">';
    echo '</div>';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ce_title">Title</label>';
    echo '<input id="ce_title" class="form-control" name="title" value="' . codex_e($titleVal) . '" placeholder="Codex Introduction">';
    echo '</div>';

    echo '<div class="row g-2 mb-2">';
    echo '<div class="col-4">';
    echo '<label class="form-label" for="ce_format">Format</label>';
    echo '<select id="ce_format" class="form-select" name="format">';
    echo '<option value="md"' . ($formatVal === 'md' ? ' selected' : '') . '>Markdown</option>';
    echo '<option value="html"' . ($formatVal === 'html' ? ' selected' : '') . '>HTML</option>';
    echo '</select>';
    echo '</div>';

    echo '<div class="col-4">';
    echo '<label class="form-label" for="ce_status">Status</label>';
    echo '<select id="ce_status" class="form-select" name="status">';
    echo '<option value="1"' . ($statusVal === 1 ? ' selected' : '') . '>Enabled</option>';
    echo '<option value="0"' . ($statusVal === 0 ? ' selected' : '') . '>Disabled</option>';
    echo '</select>';
    echo '</div>';

    echo '<div class="col-4">';
    echo '<label class="form-label" for="ce_vis">Visibility</label>';
    echo '<select id="ce_vis" class="form-select" name="visibility">';
    echo '<option value="0"' . ($visVal === 0 ? ' selected' : '') . '>Public</option>';
    echo '<option value="1"' . ($visVal === 1 ? ' selected' : '') . '>Members</option>';
    echo '</select>';
    echo '</div>';
    echo '</div>';

    echo '<div class="mb-2">';
    echo '<label class="form-label" for="ce_body">Body</label>';
    echo '<textarea id="ce_body" class="form-control" name="body" rows="16">' . codex_e($bodyVal) . '</textarea>';
    echo '</div>';

    echo '<div class="d-flex gap-2">';
    echo '<button class="btn btn-primary" type="submit">Save Entry</button>';
    echo '</div>';

    echo '</form>';

    if ($eid > 0) {
        echo '<form method="post" class="mt-2" onsubmit="return confirm(\'Delete this entry?\');">';
        echo '<input type="hidden" name="csrf" value="' . codex_e($csrf) . '">';
        echo '<input type="hidden" name="act" value="entry_delete">';
        echo '<input type="hidden" name="id" value="' . $eid . '">';
        echo '<button class="btn btn-outline-danger" type="submit">Delete Entry</button>';
        echo '</form>';
    }

    echo '</div></div></div>';
    echo '</div>';
    echo '</div>';
    return;
}

echo '</div>';

