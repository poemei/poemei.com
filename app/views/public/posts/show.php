<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="post-container" style="max-width: 800px; margin: auto;">
    <?php if (!empty($post['image_path'])): ?>
        <div class="featured-image" style="margin-bottom: 20px;">
            <img src="<?= $post['image_path'] ?>" 
                 alt="<?= htmlspecialchars($post['title']) ?>" 
                 style="width: 100%; height: auto; border-radius: 8px; object-fit: cover;">
        </div>
    <?php endif; ?>

    <article>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-body">
            <?= $post['body'] ?>
        </div>
    </article>

    <hr>

    <section class="replies">
        <h3>Replies</h3>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="reply-form" style="margin-bottom: 30px; padding: 20px; background: #f4f4f4; border-radius: 8px;">
                <h4>Leave a Reply</h4>
                <form action="/posts/reply" method="POST">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <div style="margin-bottom: 10px;">
                        <label>Name</label><br>
                        <input type="text" name="author_name" value="<?= $_SESSION['user_name'] ?? 'Member' ?>" required style="width: 100%; padding: 8px;">
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
            <strong><?= ucfirst(htmlspecialchars($comment['author_name'])) ?>:</strong><br> <?= nl2br(htmlspecialchars($comment['body'])) ?>
        </p>
        <small class="text-muted"><?= $comment['created_at'] ?></small>
    </div>
<?php endforeach; ?>
    </section>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
