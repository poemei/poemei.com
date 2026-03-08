<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="fw-bold"><?= htmlspecialchars($title); ?></h1>
            <p class="text-muted small">ID: <?= htmlspecialchars($roll_id); ?></p>

            <?php if (!empty($superseded_by_slug)): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This record has been <strong>superseded</strong> by: 
                    <a href="/rolls/<?= htmlspecialchars($superseded_by_slug); ?>" class="alert-link">
                        <?= htmlspecialchars($superseded_by_title); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!empty($related_slug)): ?>
                <div class="mb-4">
                    <span class="text-muted small text-uppercase"><strong>Linked Roll</strong>:</span>
                    <a href="/rolls/<?= htmlspecialchars($related_slug); ?>" class="badge bg-light text-primary border text-decoration-none ms-2">
                        <?= htmlspecialchars($related_title); ?>
                    </a>
                </div>
            <?php endif; ?>

            <hr>
            <div class="content-body mt-4">
                <?= content_renderer::render($content); ?>
            </div>
            <p>Return to <a href="/rolls">Rolls</a></p>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
