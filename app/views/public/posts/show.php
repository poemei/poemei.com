<?php
require APPROOT . '/views/inc/head.php';
require_once APPROOT . '/lib/share.php';

// Prepare URL for share buttons
$post_url = rtrim(URLROOT, '/') . '/posts/show/' . urlencode((string)($post['slug'] ?? ''));
?>

<div class="post-container" style="max-width: 800px; margin: auto; padding: 20px;">
    <?php if (!empty($post['image_path'])): ?>
        <div class="featured-image" style="margin-bottom: 25px; overflow: hidden; border-radius: 8px;">
            <img src="<?= htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>"
                 style="width: 100%; max-height: 450px; object-fit: cover; display: block;">
        </div>
    <?php endif; ?>

    <article>
        <h1 style="margin-bottom: 10px;"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h1>

        <?php if (function_exists('share_buttons')): ?>
            <div style="margin: 15px 0 25px 0;">
                <?= share_buttons($post_url, (string)($post['title'] ?? '')) ?>
            </div>
        <?php endif; ?>

        <div class="post-body" style="line-height: 1.7; font-size: 1.15rem; white-space: pre-wrap; word-wrap: break-word;">
            <?= $post['body'] ?>
        </div>
    </article>

    <hr style="margin: 40px 0;">

    <section class="replies">
        <h3>Replies</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="reply-form" style="margin-bottom: 30px; padding: 20px; background: #f4f4f4; border-radius: 8px;">
                <h4>Leave a Reply</h4>
                <form action="/posts/reply" method="POST">
                    <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                    <div style="margin-bottom: 10px;">
                        <label>Name</label><br>
                        <input type="text" name="author_name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Member', ENT_QUOTES, 'UTF-8') ?>" required style="width: 100%; padding: 8px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label>Message</label><br>
                        <textarea name="body" required style="width: 100%; height: 100px; padding: 8px;"></textarea>
                    </div>
                    <button type="submit" style="padding: 10px 20px; background: #333; color: #fff; border: none; cursor: pointer;">Post Reply</button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-prompt" style="margin-bottom: 30px; padding: 15px; border: 1px dashed #ccc; text-align: center;">
                <p>Please <a href="/login">log in</a> to join the conversation.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($comments as $comment): ?>
            <div class="comment" style="background: #000; color: #fff; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                <p>
                    <strong><?= ucfirst(htmlspecialchars($comment['author_name'], ENT_QUOTES, 'UTF-8')) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($comment['body'], ENT_QUOTES, 'UTF-8')) ?>
                </p>
                <small class="text-muted" style="color: #888;"><?= htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') ?></small>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
