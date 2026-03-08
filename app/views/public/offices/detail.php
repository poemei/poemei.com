<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">

    <h1 class="display-5 mb-3">
        <?= content_renderer::render($office['name']); ?>
    </h1>

    <?php if (!empty($office['description'])): ?>
        <div class="mb-4">
            <?= content_renderer::render($office['description']); ?>
        </div>
    <?php endif; ?>

    <hr>

    <?php if (!empty($lessons)): ?>

        <ul class="list-group mt-4">
            <?php foreach ($lessons as $lesson): ?>
                <li class="list-group-item">
                    <a href="/lessons/<?= htmlspecialchars($lesson['slug']); ?>">
                        <?= content_renderer::render($lesson['title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php else: ?>
        <p class="text-muted mt-4">No curriculum published yet.</p>
    <?php endif; ?>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
