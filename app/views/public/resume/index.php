<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="container">
  <div class="row">
    <h1>Resume</h1>
    <table class="resume-table">
    <?php foreach ($data['get_resume'] as $item => $description): ?>
        <tr>
            <td><?= $render_md->markdown($item) ?></td>
        </tr>
        <tr>
            <td><?= $render_md->markdown($description) ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <small><em>This page is MarkDown, rendered by my custom Markdown Rendering Engine</em></small>
  </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
