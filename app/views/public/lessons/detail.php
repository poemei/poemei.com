<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">

    <h1 class="display-5 mb-2">
        <?= htmlspecialchars($lesson['title']); ?>
    </h1>

    <?php if (!empty($lesson['office_name'])): ?>
        <p class="text-muted mb-4">
            <?= htmlspecialchars($lesson['office_name']); ?>
        </p>
    <?php endif; ?>

    <div>
        <?= content_renderer::render($lesson['content']); ?>
    </div>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
