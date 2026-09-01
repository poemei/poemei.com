<?php require APPROOT . '/views/inc/head.php'; ?>
<p><small><a href="/admin">Admin</a> >> <strong>Letters</strong></small></p>
<div class="container my-3 text-light"> 
    <h2 class="h5 mb-3">News Broadcast — Management</h2>

    <form method="post" class="card bg-dark text-white border-secondary card-body mb-4">
        <div class="mb-2">
            <label class="form-label text-secondary small">Subject</label>
            <input class="form-control form-control-sm bg-dark text-white border-secondary" value="News" readonly disabled>
        </div>
        <div class="mb-2">
            <label class="form-label">Message Body</label>
            <textarea name="body" 
                      class="form-control form-control-sm bg-dark text-white border-secondary" 
                      rows="12" 
                      style="white-space: pre-wrap; font-family: monospace;" 
                      placeholder="Type normally or use HTML tags..." 
                      required></textarea>
        </div>
        <button class="btn btn-sm btn-outline-primary">Add & Broadcast Letter</button>
    </form>

    <table class="table table-sm table-dark table-hover align-middle border-secondary">
        <thead>
            <tr class="text-secondary">
                <th>Date</th>
                <th>Subject</th>
                <th>Snippet</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['items'])): ?>
            <?php foreach ($data['items'] as $it): ?>
                <tr>
                    <td class="text-secondary small"><?= $it['created_at'] ?></td>
                    <td><?= htmlspecialchars($it['subject']) ?></td>
                    <td class="text-secondary small">
                        <?= substr(strip_tags($it['body']), 0, 60) ?>...
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $it['id'] ?>">
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Delete?')">×</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center text-secondary py-4">No letters found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
