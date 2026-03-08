<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="container mt-5">
    <h1 class="display-6 mb-4">Institutional Rolls</h1>

    <?php if (!empty($data['rolls'])): ?>
        <div class="list-group shadow-sm">
            <?php foreach ($data['rolls'] as $roll): ?>
                <a href="/rolls/<?= htmlspecialchars($roll['slug']); ?>" class="list-group-item list-group-item-action p-4">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 text-primary"><?= htmlspecialchars($roll['title']); ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($roll['roll_id']); ?></small>
                    </div>
                    <p class="mb-1 text-muted small">
                        <?php 
                            // Truncate to 100 characters with an ellipsis
                            $clean_text = strip_tags(content_renderer::render($roll['content']));
                            echo mb_strimwidth($clean_text, 0, 100, "..."); 
                        ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
