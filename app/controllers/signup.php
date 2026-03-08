<?php
// path: /app/controllers/signup.php

class signup extends controller
{
    public function index()
    {
        $this->view('public/signup/instructions');
    }

    public function step_1()
    {
        $model = $this->model('signup_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['temp_signup'] = [
                // contact + geo anchor
                'contact_text' => trim((string)($_POST['phone'] ?? '')),
                'email_address' => $_POST['email_address'],
                'municipality' => trim((string)($_POST['city'] ?? '')),
                'address'      => trim((string)($_POST['address'] ?? '')),
                'zip_code'     => trim((string)($_POST['zip_code'] ?? '')),
                'is_v'         => (int)($_POST['is_v'] ?? 0),

                // selectors
                'region_id' => (int)($_POST['region_id'] ?? 0),
                'state_id'  => (int)($_POST['state_id'] ?? 0),
                'county_id' => (int)($_POST['county_id'] ?? 0),
            ];

            header('Location: ' . URLROOT . '/signup/step_2');
            exit;
        }

        $data['regions'] = $model->get_regions();
        $this->view('public/signup/step_1', $data);
    }

    public function step_2()
    {
        if (!isset($_SESSION['temp_signup'])) {
            header('Location: ' . URLROOT . '/signup/step_1');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_birth_number'])) {
            // accept either field name
            $dob = trim((string)($_POST['dob'] ?? $_POST['birth_date'] ?? ''));

            // validate YYYY-MM-DD
            $date_parts = explode('-', $dob);
            $yyyy = (int)($date_parts[0] ?? 0);
            $mm   = (int)($date_parts[1] ?? 0);
            $dd   = (int)($date_parts[2] ?? 0);

            if (!$dob || !$yyyy || !$mm || !$dd || !checkdate($mm, $dd, $yyyy)) {
                $_SESSION['signup_error'] = 'invalid_dob';
                header('Location: ' . URLROOT . '/signup/step_2');
                exit;
            }

            $clean_dob = str_replace('-', '', $dob);

            $_SESSION['temp_signup']['dob']          = $dob;
            $_SESSION['temp_signup']['birth_number'] = $this->reduce_to_single($clean_dob);
            $_SESSION['temp_signup']['zodiac']       = $this->get_zodiac($mm, $dd);

            $_SESSION['interrogation_step'] = 0;

            header('Location: ' . URLROOT . '/signup/step_3');
            exit;
        }

        $this->view('public/signup/step_2');
    }

    public function step_3()
    {
        if (!isset($_SESSION['temp_signup'])) {
            header('Location: ' . URLROOT . '/signup/step_1');
            exit;
        }

        $current_q = $_SESSION['interrogation_step'] ?? 0;

        $questions = [
            0 => "State your legal name for encryption.",
            1 => "Do you believe in wiccan and neo-pagan ranks, such as High Priestess or Arch Druid?",
            2 => "Identify your chosen name and initials. (Seperate with a Comma)",
            3 => "Verify your choice to walk a path to sovereignty, Yes or No"
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answer = trim((string)($_POST['answer'] ?? ''));

            if ($current_q == 0) {
                $_SESSION['temp_signup']['legal_name'] = $answer;
            } elseif ($current_q == 2) {
                $parts = explode(',', $answer);
                $_SESSION['temp_signup']['chosen_name'] = trim((string)($parts[0] ?? ''));
                $_SESSION['temp_signup']['initials']    = isset($parts[1]) ? trim((string)$parts[1]) : '';
            }

            $_SESSION['interrogation_step']++;

            if ($_SESSION['interrogation_step'] >= count($questions)) {
                header('Location: ' . URLROOT . '/signup/finalize');
                exit;
            }

            header('Location: ' . URLROOT . '/signup/step_3');
            exit;
        }

        $data['question'] = $questions[$current_q] ?? '';
        $this->view('public/signup/step_3', $data);
    }

      public function finalize()
{
    if (!isset($_SESSION['temp_signup'])) {
        header('Location: ' . URLROOT . '/signup');
        exit;
    }

    $model = $this->model('signup_model');

    $member_id = $model->register_final_member($_SESSION['temp_signup']);

    if (!$member_id) {
        die("critical error: signup insert failed.");
    }

    $signup = $_SESSION['temp_signup'];
    $data['member_id'] = $member_id;

    // Pull geo data written during signup
    $geo = $model->get_member_geo($member_id);

    $mgrs = $geo['mgrs_coord'] ?? 'pending';
    $lat  = $geo['lat'] ?? null;
    $lon  = $geo['lon'] ?? null;

    // Only search covens if geo exists
    $coven = null;
    if ($lat !== null && $lon !== null) {
        $coven = $model->find_nearby_coven($lat, $lon);
    }

    $mailer = new mailer();
    $mail = $mailer->create();

    $mail->addAddress('office@arsrosaic.org');

    if (!empty($coven['contact_email'])) {
        $mail->addAddress($coven['contact_email']);
    }

    $mail->Subject = 'New Ars Rosaic Signup';

    $mail->Body = "
        <h3>New Member Signup</h3>
        <b>Name:</b> {$signup['chosen_name']}<br>
        <b>Municipality:</b> {$signup['municipality']}<br>
        <b>Email:</b> {$signup['email_address']}<br>
        <b>Phone:</b> {$signup['contact_text']}<br>
        <b>Zip Code:</b> {$signup['zip_code']}<br>
        <b>MGRS:</b> {$mgrs}<br>
    ";

    try {
        $mail->send();
    } catch (Exception $e) {
        error_log('Signup mail failed: ' . $mail->ErrorInfo);
    }

    unset($_SESSION['temp_signup'], $_SESSION['interrogation_step']);

    $this->view('public/signup/finalize', $data);
}

    private function reduce_to_single($number)
    {
        $digits = str_split((string)$number);
        $sum = array_sum($digits);
        return ($sum > 9) ? $this->reduce_to_single($sum) : $sum;
    }

    private function get_zodiac($month, $day)
    {
        $signs = [
            ['capricorn', 19], ['aquarius', 18], ['pisces', 20], ['aries', 19],
            ['taurus', 20], ['gemini', 20], ['cancer', 22], ['leo', 22],
            ['virgo', 22], ['libra', 22], ['scorpio', 21], ['sagittarius', 21],
            ['capricorn', 31]
        ];

        $month = (int)$month;
        $day   = (int)$day;

        if ($month < 1 || $month > 12) {
            return '';
        }

        return ($day <= $signs[$month - 1][1]) ? $signs[$month - 1][0] : $signs[$month][0];
    }

     public function get_states($params): void
    {
        $rid = $params[2] ?? 0;

        header('Content-Type: application/json');
        echo json_encode(
            $this->model('signup_model')->get_states_by_region((int)$rid)
        );
        exit;
    }

    public function get_counties($params): void
    {
        $sid = $params[2] ?? 0;

        header('Content-Type: application/json');
        echo json_encode(
            $this->model('signup_model')->get_counties_by_state((int)$sid)
        );
        exit;
    }
}
