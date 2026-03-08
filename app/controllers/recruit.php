<?php
require_once '../core/squire.php';
require_once '../models/recruit_model.php';

class recruit_controller {
    private $squire;
    private $model;

    public function __construct($db) {
        $this->squire = new squire($db);
        $this->model = new recruit_model($db);
    }

    public function process_step() {
        $step = $_POST['step'] ?? 1;
        $response = ['status' => 'success', 'next_step' => $step + 1];

        // step 1: bio-math (dob & zodiac)
        if ($step == 1) {
            $birth_num = $this->squire->calculate_birth_number($_POST['dob']);
            $element = $this->squire->get_element($_POST['zodiac']);
            $response['calc'] = ['num' => $birth_num, 'elem' => $element];
        }

        // step 2: rank & gender gate
        if ($step == 2) {
            if ($_POST['bio_sex'] === 'male' && $_POST['rank_target'] !== 'beginner') {
                // males can only be adeptus-minor/beginner
                die("session terminated: hierarchical non-compliance.");
            }
        }

        // step 3: the wiccan/offense trap
        if ($step == 3) {
            if (!$this->squire->vet_response('name', $_POST['chosen_name']) || 
                !$this->squire->check_offense($_POST['offense'])) {
                die("session terminated: doctrine violation.");
            }
        }

        echo json_encode($response);
    }
}
