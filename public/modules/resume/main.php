<?php
// modules/resume/main.php
declare(strict_types=1);

// Resolve project root from /pages
$filepath = __DIR__ . '/data/resume.json';
$resume     = [];

// Load JSON safely
if (is_file($filepath)) {
    $json = file_get_contents($filepath);
    $resume = json_decode($json, true);
    if (!is_array($resume)) {
        echo '<div class="alert alert-warning">Invalid /data/resume.json: '
           . htmlspecialchars(json_last_error_msg())
           . '</div>';
        $resume = [];
    }
} else {
    echo '<div class="alert alert-info">Missing file: <code>/data/resume.json</code></div>';
}

// Provide module_url() if not globally available
if (!function_exists('module_url')) {
    function module_url(string $module, string $id): string {
        // Adjust to your preferred scheme; this keeps your current anchors
        return '/' . $module . '#' . rawurlencode($id);
    }
}
?>
<div class="container">

  <div class="row md-9">
    <h2>My Resume</h2>

    <div class="container-fluid mt-4">
      <div class="row">
      <!--
        <aside class="col-md-3 mb-4">
          <div class="list-group sticky-top">
            <?php foreach ($resume as $item): 
              $id    = isset($item['id']) ? (string)$item['id'] : '';
              $title = isset($item['title']) ? (string)$item['title'] : $id;
              if ($id === '') continue;
            ?>
              <a href="<?= htmlspecialchars(module_url('resume', $id)) ?>" class="list-group-item list-group-item-action">
                <?= htmlspecialchars($title) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </aside>
        -->

        <main class="col-md-9">
          <?php foreach ($resume as $item): 
            $id    = isset($item['id']) ? (string)$item['id'] : '';
            $title = isset($item['title']) ? (string)$item['title'] : $id;
            $desc  = isset($item['description']) ? (string)$item['description'] : '';
            if ($id === '') continue;
          ?>
            <section id="<?= htmlspecialchars($id) ?>" class="mb-5">
              <h3><?= htmlspecialchars($title) ?></h3>
              <p><?= $desc ?></p>
            </section>
          <?php endforeach; ?>

          <?php if (!$resume): ?>
            <div class="text-muted">No core entries to display.</div>
          <?php endif; ?>
        </main>
      </div>
    </div>
  </div>
</div>
