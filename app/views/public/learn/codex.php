<?php
declare(strict_types=1);

require APPROOT . '/views/inc/head.php';
?>
<main>
<p><small><a href="/learn">Knowledge</a> >> <strong>Codex</strong></small></p>
<div class="container">
  <section>
    <h1>The Ars Rosaic Codex</h1>
    <p>The book that defines the entire tradition.</p>
  </section>
  <section>
    <?php
    $chapters = file_get_contents(APPROOT . '/doctrine/ars_rosaic.txt');
    echo '<div class="doctrine">' . htmlspecialchars($chapters) . '</div>';
    ?>
  </section>
</div>
</main>
<?php require APPROOT . '/views/inc/foot.php'; ?>
