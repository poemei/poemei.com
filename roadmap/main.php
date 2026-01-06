<?php
// public/modules/roadmap/main.php
// Roadmap module using module-local data only (with overall % at top).

/* ---------- helpers ---------- */
if (!function_exists('e')) {
    function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('json_read_assoc')) {
    function json_read_assoc(string $file, $default = null) {
        if (!is_file($file) || !is_readable($file)) return $default;
        $raw = @file_get_contents($file);
        if (!is_string($raw) || $raw === '') return $default;
        if (substr($raw, 0, 3) === "\xEF\xBB\xBF") $raw = substr($raw, 3); // strip BOM
        $data = json_decode($raw, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($data)) ? $data : $default;
    }
}

/* ---------- module-local data ---------- */
$moduleDir  = __DIR__;
$candidates = [
    $moduleDir . '/roadmap.json',
    $moduleDir . '/data/roadmap.json',
];

$roadmapPath = null;
$roadmap     = ['items' => []];
foreach ($candidates as $p) {
    $tmp = json_read_assoc($p, null);
    if (is_array($tmp)) { $roadmap = $tmp; $roadmapPath = $p; break; }
}

/* debug crumb (view-source) */
echo "\n<!-- roadmap data path=" . e((string)$roadmapPath) . " keys=" . e(implode(',', array_keys($roadmap))) . " -->\n";

/* ---------- extract ---------- */
$title = (string)($roadmap['title'] ?? '');
$items = is_array($roadmap['items'] ?? null) ? $roadmap['items'] : [];

/* ---------- group & order ---------- */
$groups = [];
foreach ($items as $it) {
    if (!is_array($it)) continue;
    $cat = (string)($it['category'] ?? 'Misc');
    $groups[$cat][] = $it;
}
$categoryOrder = ['Core', 'Features', 'Support', 'Misc'];
uksort($groups, function ($a, $b) use ($categoryOrder) {
    $pa = array_search($a, $categoryOrder, true);
    $pb = array_search($b, $categoryOrder, true);
    $pa = ($pa === false) ? PHP_INT_MAX : $pa;
    $pb = ($pb === false) ? PHP_INT_MAX : $pb;
    return $pa <=> $pb;
});
foreach ($groups as &$bucket) {
    usort($bucket, function ($x, $y) {
        $px = (int)($x['percent'] ?? 0);
        $py = (int)($y['percent'] ?? 0);
        return $py <=> $px; // 100 first
    });
}
unset($bucket);

/* ---------- overall (average across all items) ---------- */
$all_sum = 0; $all_n = 0;
foreach ($items as $it) {
    if (!is_array($it)) continue;
    $all_sum += (int)($it['percent'] ?? 0);
    $all_n++;
}
$overall = $all_n ? (int)round($all_sum / $all_n) : 0;

/* ---------- visuals ---------- */
$barClassFor = static function (int $value): string {
    if ($value < 25) return 'text-bg-danger';
    if ($value < 50) return 'text-bg-warning';
    if ($value < 75) return 'text-bg-info';
    return 'text-bg-success';
};

$renderItem = function (array $it) use ($barClassFor) {
    $label   = (string)($it['label'] ?? '');
    $percent = max(0, min(100, (int)($it['percent'] ?? 0)));
    $class   = $barClassFor($percent);

    // description: prefer trusted 'html', else escaped 'text'
    $descHtml = '';
    if (!empty($it['html'])) {
        $descHtml = (string)$it['html']; // trusted local HTML
    } elseif (!empty($it['text'])) {
        $descHtml = '<p class="mb-0">' . e((string)$it['text']) . '</p>';
    }

    echo '<div class="mb-3">';
    if ($label !== '') {
        echo '<div class="d-flex justify-content-between align-items-center mb-1">';
        echo '  <strong>' . e($label) . '</strong>';
        echo '  <span class="badge ' . $class . '">' . $percent . '%</span>';
        echo '</div>';
    }
    echo '<div class="progress mb-2" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100">';
    echo '  <div class="progress-bar ' . $class . '" style="width:' . $percent . '%"></div>';
    echo '</div>';
    if ($descHtml !== '') {
        echo '<div class="text-muted small">' . $descHtml . '</div>';
    }
    echo '</div>';
};

/* ---------- render ---------- */
echo '<section class="container my-4">';
if ($title !== '') echo '<h1 class="h4 mb-3">' . e($title) . '</h1>';

/* overall bar (new, dynamic only) */
echo '<div class="mb-4">';
echo '  <div class="d-flex justify-content-between align-items-center mb-1">';
echo '    <strong>Overall Progress</strong>';
echo '    <span class="badge ' . $barClassFor($overall) . '">' . $overall . '%</span>';
echo '  </div>';
echo '  <div class="progress" role="progressbar" aria-valuenow="' . $overall . '" aria-valuemin="0" aria-valuemax="100">';
echo '    <div class="progress-bar ' . $barClassFor($overall) . '" style="width:' . $overall . '%"></div>';
echo '  </div>';
echo '</div>';

if (empty($groups)) {
    echo '<div class="alert alert-info">No roadmap items yet.</div>';
    echo '</section>';
    return;
}

foreach ($groups as $category => $bucket) {
    // average for heading
    $sum = 0; $n = 0;
    foreach ($bucket as $it) { $sum += (int)($it['percent'] ?? 0); $n++; }
    $avg = $n ? (int)round($sum / $n) : 0;

    echo '<div class="d-flex align-items-center justify-content-between mt-4 mb-2">';
    echo '  <h2 class="h5 mb-0">' . e($category) . '</h2>';
    echo '  <span class="text-muted small">' . $n . ' item' . ($n === 1 ? '' : 's') . ' — avg ' . $avg . '%</span>';
    echo '</div>';

    foreach ($bucket as $it) {
        $renderItem($it);
    }
}
