<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">

    <h1 class="display-5 mb-4">Lessons</h1>

    <?php if (!empty($lessons)): ?>

        <div class="list-group">
            <?php foreach ($lessons as $lesson): ?>

                <a href="/lessons/<?= htmlspecialchars($lesson['slug']); ?>"
                   class="list-group-item list-group-item-action">

                    <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($lesson['title']); ?></strong>

                        <?php if (!empty($lesson['office_name'])): ?>
                            <small class="text-muted">
                                <?= htmlspecialchars($lesson['office_name']); ?>
                            </small>
                        <?php endif; ?>
                    </div>

                </a>

            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <p class="text-muted">No lessons published.</p>

    <?php endif; ?>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
