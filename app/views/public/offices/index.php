<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">
    
    <?php if (isset($office)): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/offices">Academy Faculty</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($office['name']); ?></li>
            </ol>
        </nav>
        <h1 class="display-4 mb-4"><?= htmlspecialchars($office['name']); ?></h1>
    <?php else: ?>
        <h1 class="display-4 mb-2">Institutional Departments</h1>
        <p class="lead text-muted mb-5">Professional faculties dedicated to the study of human lineage, behavior, and philosophy.</p>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($offices)): ?>
            <?php foreach ($offices as $item): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 bg-light">
                        <div class="card-body p-4">
                            <h2 class="h3 card-title text-dark mb-3">
                                <?= htmlspecialchars($item['name']); ?>
                            </h2>
                            
                            <div class="office-description text-secondary">
                                <?= content_renderer::render($item['description']); ?>
                            </div>

                            <div class="mt-4">
                                <a href="/offices/<?= htmlspecialchars($item['slug']); ?>" class="btn btn-primary">
                                    Access <?= isset($office) ? 'Curriculum' : 'Department'; ?> &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No departments are currently listed for this faculty.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
