<?php
declare(strict_types=1);
include __DIR__ . '/utility.php';

class nav
{
    /**
     * helper to handle the active state logic.
     * instantiates the utility class to access nav_active correctly.
     */
    private function render_link(string $href, string $label, string $current): string
    {
        $util = new utility();
        
        // Correctly call the method from the utility instance
        if ($util->nav_active($href, $current) === 'active') {
            return "<span class='fw-bold text-dark text-lowercase' style='cursor: default;'>$label</span>";
        }
        
        return "<a href='$href' class='text-secondary text-lowercase text-decoration-none'>$label</a>";
    }

    public function admin_nav(string $current): void
    {
        $links = [
            '/admin/dashboard'      => 'admin',
            '/admin/announcements'  => 'announcements',
            '/admin/curriculum'     => 'curriculum',
            '/admin/correspondence' => 'correspondence',
            '/admin/modules'        => 'modules',
            '/admin/rolls'          => 'rolls',
            '/admin/staff'          => 'staff'
        ];
        ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.9em;">
                <?php foreach ($links as $href => $label): ?>
                    <li class="breadcrumb-item">
                        <?= $this->render_link($href, $label, $current) ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <hr class="mt-0 mb-4">
        <?php
    }
    
    public function public_nav(string $current)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    ?>
    <nav>
        <ul style="list-style: none; display: flex; gap: 20px; margin: 0; padding: 0;">
            <li><?= $this->render_link('/', 'home', $current) ?></li>
            <li><?= $this->render_link('/curriculum', 'curriculum', $current) ?></li>
            
            <?php if (isset($_SESSION['staff_id'])): ?>
                <li><?= $this->render_link('/admin/dashboard', 'dashboard', $current) ?></li>
                <li><a href="/logout" class="text-lowercase" style="color: #900; text-decoration: none;">logout</a></li>
            <?php else: ?>
                <li><?= $this->render_link('/login', 'login', $current) ?></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
    }
}
