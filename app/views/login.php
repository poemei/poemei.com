<?php require_once APPROOT . '/views/head.php'; ?>
<div class="login-container" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc;">
    <h2>Institutional Access</h2>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form action="/auth/login" method="POST">
        <div style="margin-bottom: 15px;">
            <label for="username">Username:</label><br>
            <input type="text" name="username" id="username" required style="width: 100%;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" required style="width: 100%;">
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>
<?php require_once APPROOT . '/views/foot.php'; ?>
