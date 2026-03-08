<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container signup-container">
    <h2>Step 2: Birth Alignment</h2>
    <p>Enter your date of birth to calculate your birth number and zodiac.</p>

    <form action="<?php echo URLROOT; ?>/signup/step_2" method="POST">
        <div class="form-group mb-4">
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" class="form-control" required>
        </div>

        <!-- THIS NAME MUST MATCH CONTROLLER CHECK -->
        <button type="submit" name="calculate_birth_number" value="1" class="btn btn-primary w-100">
            Calculate & Proceed
        </button>
    </form>

    <?php if (!empty($_SESSION['signup_error'])): ?>
        <div class="alert alert-danger mt-3">
            Invalid date of birth. Use YYYY-MM-DD format.
        </div>
        <?php unset($_SESSION['signup_error']); ?>
    <?php endif; ?>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
