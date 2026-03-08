<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container signup-container">
    <h2>Step 1: Geographic Anchor</h2>
    <p>Select your region and contact details to align with the local grid.</p>

    <form action="<?php echo URLROOT; ?>/signup/step_1" method="POST">
        <div class="form-group mb-3">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="for secure transmission..." required>
        </div>
        
        <div class="form-group mb-3">
            <label for="email_address">Email Address</label>
            <input type="text" name="email_address" id="email_address" class="form-control" placeholder="for secure transmission..." required>
        </div>

        <div class="form-group mb-3">
            <label for="address">Street Address</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="street address..." required>
        </div>

        <div class="form-group mb-3">
            <label for="city">City</label>
            <input type="text" name="city" id="city" class="form-control" placeholder="city of residence..." required>
        </div>

        <div class="form-group mb-3">
            <label for="zip_code">ZIP Code</label>
            <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="zip..." required>
        </div>

        <div class="form-group mb-3">
            <label for="region_id">Region</label>
            <select name="region_id" id="region_id" class="form-control" required>
                <option value="">select region...</option>
                <?php foreach ($data['regions'] as $region): ?>
                    <option value="<?php echo $region['id']; ?>"><?php echo $region['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="state_id">State</label>
            <select name="state_id" id="state_id" class="form-control" required disabled>
                <option value="">select state...</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label for="county_id">County</label>
            <select name="county_id" id="county_id" class="form-control" required disabled>
                <option value="">select county...</option>
            </select>
        </div>
        
        <div class="form-group mb-3">
    <label>Are you a Military Veteran?</label>
    <select name="is_v" class="form-control">
        <option value="0" selected>No</option>
        <option value="1">Yes</option>
    </select>
</div>

        <button type="submit" class="btn btn-primary w-100">Proceed to Step 2</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const stateSelect = document.getElementById('state_id');
    const countySelect = document.getElementById('county_id');

    regionSelect.addEventListener('change', function() {
        const rid = this.value;
        stateSelect.innerHTML = '<option value="">loading states...</option>';
        stateSelect.disabled = true;
        countySelect.innerHTML = '<option value="">select county...</option>';
        countySelect.disabled = true;

        if (rid) {
            fetch('<?php echo URLROOT; ?>/signup/get_states/' + rid + '?t=' + Date.now())
                .then(response => response.json())
                .then(data => {
                    stateSelect.innerHTML = '<option value="">select state...</option>';
                    data.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.id;
                        option.textContent = state.name;
                        stateSelect.appendChild(option);
                    });
                    stateSelect.disabled = false;
                })
                .catch(err => console.error('Error fetching states:', err));
        }
    });

    stateSelect.addEventListener('change', function() {
        const sid = this.value;
        countySelect.innerHTML = '<option value="">loading counties...</option>';
        countySelect.disabled = true;

        if (sid) {
            fetch('<?php echo URLROOT; ?>/signup/get_counties/' + sid + '?t=' + Date.now())
                .then(response => response.json())
                .then(data => {
                    countySelect.innerHTML = '<option value="">select county...</option>';
                    data.forEach(county => {
                        const option = document.createElement('option');
                        option.value = county.id;
                        option.textContent = county.name;
                        countySelect.appendChild(option);
                    });
                    countySelect.disabled = false;
                })
                .catch(err => console.error('Error fetching counties:', err));
        }
    });
});
</script>

<?php require APPROOT . '/views/inc/foot.php'; ?>
