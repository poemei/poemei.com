<?php require APPROOT . '/views/inc/head.php'; ?>
<div id="squire-portal">
    <h2>Squire Vetting Portal</h2>
    <p class="warning">NOTICE: You are being scrutinized by a Squire-Logic AI. Obfuscation results in a permanent block.</p>

    <form id="vetting-form">
        <div class="step" id="step-1">
            <label>Date of Birth:</label>
            <input type="date" name="dob" required>
            <label>Zodiac Sign:</label>
            <select name="zodiac">
                <option value="aries">Aries</option>
                <option value="taurus">Taurus</option>
                </select>
            <button type="button" onclick="nextStep(1)">Proceed</button>
        </div>

        <div class="step" id="step-2" style="display:none;">
            <label>Bio-Sex:</label>
            <select name="bio_sex">
                <option value="female">Female</option>
                <option value="trans_woman">Trans-Woman</option>
                <option value="male">Male</option>
            </select>
            <button type="button" onclick="nextStep(2)">Verify Rank</button>
        </div>

        <div class="step" id="step-3" style="display:none;">
            <p>Does the label "Prioress" or its Catholic origins offend your politics?</p>
            <input type="radio" name="offense" value="yes"> Yes
            <input type="radio" name="offense" value="no"> No
            <label>Chosen Name:</label>
            <input type="text" name="chosen_name" placeholder="Enter identifier...">
            <button type="button" onclick="submitVetting()">Finalize</button>
        </div>
    </form>
</div>

<script>
function nextStep(current) {
    // logic to fade out current step and show next via AJAX to controller
}
</script>
<?php require APPROOT . '/views/inc/foot.php'; ?>
