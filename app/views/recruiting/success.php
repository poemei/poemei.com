<?php 
declare(strict_types=1);
/**
 * [AI: Gemini] | 2026-03-15 21:15:00 UTC] 
 * [Human: Mei | 2026-03-15 21:20 UTC | APPROVED]
 * UI Success page for the recruiting module
*/
require_once APPROOT . '/views/inc/head.php';
?>

<div class="container mt-5 text-center">
    <div class="content-wrapper p-lg-5">
        <h1 class="display-4 text-success mb-4">Submission Logged</h1>
        <p class="lead">Your credentials have been added to the audit queue.</p>
        <p>A confirmation email has been dispatched. Please wait for the manual vetting process.</p>
        <hr class="border-secondary">
        <a href="<?php echo URLROOT; ?>" class="btn btn-link text-light">Return to Base</a>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/foot.php'; ?>
