<?php require_once APPROOT . '/views/inc/head.php'; ?>

<div class="account-wrapper" style="background: #f4f4f4; min-height: 80vh; padding: 40px 20px; font-family: 'Inter', sans-serif;">
    <div class="account-container" style="max-width: 550px; margin: 0 auto; padding: 40px; border: 1px solid #e0e0e0; border-radius: 4px; background: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        
        <header style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
            <h2 style="margin: 0; color: #222; font-weight: 600; letter-spacing: -0.5px;">Account Management</h2>
            <p style="color: #666; font-size: 0.9em; margin-top: 5px;">Manage your institutional credentials and profile settings.</p>
        </header>
        
        <?php if (isset($_GET['updated'])): ?>
            <div style="color: #3c763d; background: #dff0d8; padding: 12px; border-radius: 3px; margin-bottom: 20px; font-size: 0.9em; border: 1px solid #d6e9c6;">
                Profile successfully synchronized.
            </div>
        <?php endif; ?>

        <form action="/auth/update_account" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 0.85em; color: #444; margin-bottom: 8px; text-transform: uppercase;">Username</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" disabled style="width: 100%; padding: 10px; background: #fafafa; border: 1px solid #ddd; border-radius: 3px; color: #888; cursor: not-allowed;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="display_name" style="display: block; font-weight: 600; font-size: 0.85em; color: #444; margin-bottom: 8px; text-transform: uppercase;">Public Display Name</label>
                <input type="text" name="display_name" id="display_name" placeholder="<?= htmlspecialchars($_SESSION['display_name'] ?? 'Set display name') ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 30px;">
                <label for="password" style="display: block; font-weight: 600; font-size: 0.85em; color: #444; margin-bottom: 8px; text-transform: uppercase;">Update Password</label>
                <input type="password" name="password" id="password" placeholder="Leave empty to maintain current" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #333; color: white; padding: 12px; border: none; border-radius: 3px; cursor: pointer; font-weight: 600; transition: background 0.2s;">
                Save Changes
            </button>
        </form>

        <div style="margin: 40px 0; border-top: 1px solid #eee;"></div>

        <div class="danger-zone" style="background: #fafafa; border: 1px solid #eee; padding: 25px; border-radius: 3px;">
            <h3 style="margin-top: 0; font-size: 1em; color: #333;">Deactivate Account</h3>
            <p style="color: #777; font-size: 0.85em; line-height: 1.5; margin-bottom: 20px;">
                Deleting your account is permanent. All associated records and permissions will be revoked immediately.
            </p>
            
            <form action="/auth/delete_account" method="POST" onsubmit="return confirm('Confirm permanent account deactivation?');">
                <button type="submit" style="background: transparent; color: #a94442; border: 1px solid #a94442; padding: 8px 16px; border-radius: 3px; cursor: pointer; font-size: 0.85em; font-weight: 600;">
                    Delete Account
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/foot.php'; ?>
