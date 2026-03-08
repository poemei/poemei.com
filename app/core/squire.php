<?php
// path: /app/core/squire.php

class squire {
    private $kill_list = ['doc', 'prison', 'isrb', 'vortex', 'high', 'arch', 'wiccan', 'activist', 'pagan', 'democracy'];

    public function vet(string $input, ?string $address_hash = null) {
        if ($address_hash === $this->master_anchor_hash) return ['status' => 'rejected'];
        foreach ($this->kill_list as $term) {
            if (stripos($input, $term) !== false) return ['status' => 'rejected'];
        }
        return ['status' => 'passed'];
    }
}
