<?php
// path: /app/models/signup_model.php

class signup_model extends model
{
     public function get_regions()
    {
        return $this->fetchAll("SELECT id, name FROM regions ORDER BY id ASC");
    }

    public function get_states_by_region($rid)
    {
        return $this->fetchAll(
            "SELECT id, name FROM states WHERE region_id = :rid ORDER BY name ASC", ['rid' => (int)$rid]
        );
    }

    public function get_counties_by_state($sid)
    {
        return $this->fetchAll(
            "SELECT id, name FROM counties WHERE state_id = :sid ORDER BY name ASC", ['sid' => (int)$sid]
        );
    }

    public function register_final_member(array $data)
    {
        $birth_number = (int)($data['birth_number'] ?? 0);

        // --------------------------------------------------
        // SYMBOLIC REFERENCES
        // --------------------------------------------------

        $rune   = $this->fetch("SELECT id FROM rune_reference WHERE id = :v LIMIT 1",   ['v' => $birth_number]);
        $ogham  = $this->fetch("SELECT id FROM ogham_reference WHERE id = :v LIMIT 1",  ['v' => $birth_number]);
        $rosaic = $this->fetch("SELECT id FROM rosaic_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);

        $tarot = null;
        try {
            $tarot = $this->fetch("SELECT id FROM tarot_reference WHERE id = :v LIMIT 1", ['v' => $birth_number]);
        } catch (\Throwable $e) {
            $tarot = null;
        }

        // --------------------------------------------------
        // RESOLVE STATE ABBREVIATION
        // --------------------------------------------------

        $stateRow = $this->fetch(
            "SELECT abbr FROM states WHERE id = :id LIMIT 1",
            ['id' => (int)($data['state_id'] ?? 0)]
        );

        // FIX: column is 'abbr', not 'abbreviation'
        $state = $stateRow['abbr'] ?? '';

        // --------------------------------------------------
        // GEO RESOLUTION
        // --------------------------------------------------

        $address      = trim((string)($data['address'] ?? ''));
        $municipality = trim((string)($data['municipality'] ?? ''));
        $zip_code     = trim((string)($data['zip_code'] ?? ''));

        $lat  = null;
        $lon  = null;
        $mgrs = null;

        if ($address && $municipality && $zip_code) {

            $geo_service = new geo_tool();

            $resolved = $geo_service->resolve_geo(
                $address,
                $zip_code,
                $municipality,
                $state
            );

            $lat  = $resolved['lat']  ?? null;
            $lon  = $resolved['lon']  ?? null;
            $mgrs = $resolved['mgrs'] ?? null;
        }

        // --------------------------------------------------
        // MEMBER INSERT
        // --------------------------------------------------

        $sql = "INSERT INTO members (
                    legal_name_encrypted,
                    chosen_name,
                    initials_display,
                    sex,
                    birth_date,
                    birth_number,
                    primary_glyph_id,
                    tarot_id,
                    ogham_id,
                    rune_id,
                    region_id,
                    state_id,
                    county_id,
                    address,
                    municipality,
                    zip_code,
                    is_v,
                    mgrs_coord,
                    contact_text,
                    email_address,
                    zodiac_name,
                    lat,
                    lon,
                    created_at
                ) VALUES (
                    :legal,
                    :chosen,
                    :initials,
                    :sex,
                    :dob,
                    :birth_num,
                    :rosaic,
                    :tarot,
                    :ogham,
                    :rune,
                    :rid,
                    :sid,
                    :cid,
                    :address,
                    :municipality,
                    :zip_code,
                    :is_v,
                    :mgrs,
                    :contact,
                    :email_address,
                    :zodiac,
                    :lat,
                    :lon,
                    NOW()
                )";

        $params = [
            'legal'        => $data['legal_name'] ?? null,
            'chosen'       => $data['chosen_name'] ?? null,
            'initials'     => $data['initials'] ?? null,
            'sex'          => $data['sex'] ?? '',
            'dob'          => $data['dob'] ?? null,
            'birth_num'    => $birth_number,
            'rune'         => $rune['id'] ?? null,
            'ogham'        => $ogham['id'] ?? null,
            'rosaic'       => $rosaic['id'] ?? null,
            'tarot'        => $tarot['id'] ?? null,
            'rid'          => (int)($data['region_id'] ?? 0),
            'sid'          => (int)($data['state_id'] ?? 0),
            'cid'          => (int)($data['county_id'] ?? 0),
            'address'      => $address,
            'municipality' => $municipality,
            'zip_code'     => $zip_code,
            'is_v'         => $data['is_v'] ?? 0,
            'mgrs'         => $mgrs,
            'contact'      => $data['contact_text'] ?? null,
            'email_address' => $data['email_address'] ?? null,
            'zodiac'       => $data['zodiac'] ?? '',
            'lat'          => $lat,
            'lon'          => $lon
        ];

        $this->query($sql, $params);

        return $this->db->lastInsertId();
    }

    public function get_member_geo($member_id)
    {
        return $this->fetch(
            "SELECT lat, lon, mgrs_coord FROM members WHERE id = :id LIMIT 1",
            ['id' => $member_id]
        );
    }

    public function find_nearby_coven($lat, $lon)
    {
        if (empty($lat) || empty($lon)) {
            return null;
        }

        $sql = "
        SELECT
            id,
            name,
            contact_email,
            (
                3959 * ACOS(
                    COS(RADIANS(:lat_a))
                    * COS(RADIANS(lat))
                    * COS(RADIANS(lon) - RADIANS(:lon_a))
                    + SIN(RADIANS(:lat_b))
                    * SIN(RADIANS(lat))
                )
            ) AS distance
        FROM covens
        WHERE is_active = 1
        HAVING distance <= 10
        ORDER BY distance ASC
        LIMIT 1
        ";

        return $this->fetch($sql, [
            'lat_a' => $lat,
            'lon_a' => $lon,
            'lat_b' => $lat
        ]);
    }
}
