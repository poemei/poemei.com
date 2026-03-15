<?php 
declare(strict_types=1);
/**
 * [AI: Gemini] | 2026-03-15 21:15:00 UTC] 
 * [Human: Mei | 2026-03-15 21:20 UTC | APPROVED] 
 * UI: Recruitment Intake Form with visible labeling.
 */
require_once APPROOT . '/views/inc/head.php'; 
?>

<div class="container mt-5">
    <div id="recruitment-portal" class="content-wrapper p-lg-5" style="background: #1e1e1e; border: 1px solid #333; border-radius: 8px;">
        
        <div id="gate-1">
            <h1 class="display-5 mb-4 text-white">The Gateway of Compliance</h1>
            <p class="lead text-light">
            <b>Gurls, Girls, Witches and Ghouls</b><br>
            Should you choose to develop with me....<br>
            Please understand, that the <b>Soldier</b> in me, demands:
            <ul>
              <li> Compliance</li>
              <li> Security</li>
              <li> and reporting</li>
            </ul>
            <br>
            <b>Security & Compliance</b><br>
            <b>Annotation Policy</b>: All architectual changes are signed and timestamped per AI & Developer Modification Protocols.<br>
            <b>Annotation</b>: [AI: Gemini | 2026-03-15 21:10 UTC] [Human: Mei | 2026-03-15 21:15 UTC | APPROVE]<br>
            What this means, is every bit of code that <b>YOU</b> create that may deal with anything in <code>/app/core</code> or this <b>MVC</b>, must go through a process of Approval via <em>commit</em> to <b>GitHub</b>,
            </p>
            <button class="btn btn-outline-light" onclick="document.getElementById('gate-1').style.display='none'; document.getElementById('gate-2').style.display='block';">I Accept the Terms</button>
        </div>

        <div id="gate-2" style="display:none;">
            <h2 class="mb-4 text-white">The Protocol of the Core</h2>
            <ul class="list-unstyled text-light">
                <li>• Mandatory Annotations</li>
                <li>• Zero Tolerance for 'Shit Code'</li>
                <li>• Three-Strike Removal Policy</li>
            </ul>
            <button class="btn btn-outline-light" onclick="document.getElementById('gate-2').style.display='none'; document.getElementById('recruiting-form').style.display='block';">I Will Comply</button>
        </div>

        <form id="recruiting-form" action="<?php echo URLROOT; ?>/recruiting/submit" method="POST" style="display:none;">
            <div class="mb-4">
                <label for="name" class="form-label text-light fw-bold" style="opacity: 0.9;">Name or Alias</label>
                <input type="text" id="name" name="name" class="form-control bg-dark text-light border-secondary" placeholder="e.g. GhostInTheShell" required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="form-label text-light fw-bold" style="opacity: 0.9;">Secure Email Address</label>
                <input type="email" id="email" name="email" class="form-control bg-dark text-light border-secondary" placeholder="e.g. entity@domain.com" required>
            </div>
            
            <div class="mb-4">
                <label for="github" class="form-label text-light fw-bold" style="opacity: 0.9;">GitHub Username</label>
                <input type="text" id="github" name="github" class="form-control bg-dark text-light border-secondary" placeholder="e.g. octocat" required>
            </div>
            
            <div class="mb-4">
                <label for="projects" class="form-label text-light fw-bold" style="opacity: 0.9;">High-Signal Project History</label>
                <textarea id="projects" name="projects" class="form-control bg-dark text-light border-secondary" rows="5" placeholder="Briefly list your contributions to custom or core-level MVC architectures..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary px-4">Submit for Audit</button>
        </form>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/foot.php'; ?>
