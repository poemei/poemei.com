<?php require_once APPROOT . '/views/inc/head.php';
//require_once APPROOT . '/core/content_renderer.php';
?>

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-5">

            <h1 class="display-5 text-primary mb-4">
                <?= content_renderer::render($data['title']); ?>
            </h1>

            <div class="lead" style="line-height: 1.8;">
                <?= content_renderer::render($data['content']); ?>
            </div>

        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/foot.php'; ?>
