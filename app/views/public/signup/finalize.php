<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="gauntlet-complete">
    <h2>Recruitment Finalized</h2>
    <hr>
    
    <div class="judgment-report" style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 20px 0;">
        <h3>Interrogation Outcome: Verified</h3>
        <p>The Squire has scrutinized your responses, and found it compliant with the Truth</p>
        
        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="font-weight: bold;">Member ID:</td>
                <td><?= $data['member_id']; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Authority:</td>
                <td>The Office</td>
            </tr>
        </table>
    </div>

    <div class="vortex-notice" style="padding: 15px; border-left: 4px solid #000; background: #fffbe6;">
        <p><strong>Observation:</strong> Your geographic anchor is locked.</p>
        <p>Someone will be in touch with you soon.</p>
    </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
