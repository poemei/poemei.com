<?php require APPROOT . '/views/inc/head.php'; ?>
<h2>Interrogation Stage</h2>
<p style="font-style: italic; color: #888;">The Squire is scrutinizing your response...</p>

<div class="interrogation-box" style="margin-top: 30px; border-left: 3px solid #000; padding-left: 20px;">
    <p><strong>Question:</strong> <?= $data['question']; ?></p>
    
    <form method="POST" action="<?= URLROOT; ?>/signup/step_3">
        <textarea name="answer" rows="4" style="width: 100%;" required placeholder="Type your response..."></textarea>
        <br><br>
        <button type="submit">Submit Response</button>
    </form>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
