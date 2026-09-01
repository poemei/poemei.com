<?php /* [AI:GPT-5 | 2026-08-26 22:49:59 UTC] */ ?>
<nav class="pm-nav" aria-label="Primary navigation">
    <div class="pm-nav-list">
        <div class="pm-nav-primary">
            <a href="/">Home</a>
            <a href="/posts">Posts</a>
            <a href="/current_projects">Projects</a>
            <a href="/resume">Resume</a>
        </div>

        <div class="pm-nav-utility">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (
                    isset($_SESSION['user_level'])
                    && $_SESSION['user_level'] >= 9
                ): ?>
                    <a href="/admin" class="pm-nav-admin">Admin</a>
                <?php endif; ?>

                <?php
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                ?>
                <form action="/logout" method="POST" style="display: inline;">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            (string) $_SESSION['csrf_token'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >
                    <button
                        type="submit"
                        style="background: none; border: 0; color: inherit; cursor: pointer; font: inherit; padding: 0;"
                    >Logout</button>
                </form>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/signup" class="pm-nav-register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /* [End AI:GPT-5] */ ?>
