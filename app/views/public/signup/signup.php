<?php require APPROOT . '/views/inc/head.php'; ?>

<h2>Vetting cleared</h2>
<p>Enter your credentials to finalize enrollment for manual review.</p>
<form action="/signup/finalize" method="post">
    <input type="text" name="username" placeholder="username" required>
    <input type="password" name="password" placeholder="password" required>
    <input type="email" name="email" placeholder="email" required>
    <button type="submit">Complete</button>
</form>

<?php require APPROOT . '/views/inc/foot.php'; ?>
