<?php
// path: /app/models/maps_model.php

class maps_model extends model {

    /**
     * Filters members for map display.
     * Public: Strictly excludes Madam and Tactical assets.
     */
     public function get_members_filtered($public = false) {
        // Admin view (default) shows everyone. Public hides Madam/Tactical.
        $sql = "SELECT id, chosen_name, is_v, is_t, is_m, lat, lon, mgrs_coord FROM members";
        
        if ($public) {
            $sql .= " WHERE is_m = 0 AND is_t = 0";
        }

        return $this->fetchAll($sql);
    }

    /**
     * Pulls coven data. 
     * If isAdmin is true, it forces the Auburn coven to use the Madam's 
     * house (Nexus) coordinates from the geo_cache table.
     */
    public function get_covens_data($isAdmin = false) {
        $sql = "SELECT * FROM covens";
        $covens = $this->fetchAll($sql);

        if ($isAdmin) {
            // Pull the Nexus coordinates (Madam's house)
            $sql = "SELECT g.lat, g.lon, m.coven_id
                    FROM members m 
                    JOIN geo_cache g ON m.id = g.id 
                    WHERE m.is_m = 1 LIMIT 1";
            $nexus = $this->fetch($sql);

            if ($nexus) {
                foreach ($covens as &$c) {
                    // Match coven to the Madam's linked coven and snap it to the Nexus
                    if ($c['id'] == $nexus['coven_id']) {
                        $c['lat'] = $nexus['lat'];
                        $c['lon'] = $nexus['lon'];
                        //$c['contact_email'] = $nexus['contact_email'];
                    }
                }
            }
        }
        return $covens;
    }
    
    public function get_by_hash(string $hash)
{
    return $this->query(
        "SELECT * FROM geo_cache WHERE address_hash = ? LIMIT 1",
        [$hash]
    )->fetch();
}

    public function insert_geo_cache(array $data)
{
    $sql = "INSERT INTO geo_cache (
                address_hash,
                full_address,
                municipality,
                zip_code,
                lat,
                lon,
                mgrs_coord,
                is_locked,
                scout_status
            ) VALUES (
                :address_hash,
                :full_address,
                :municipality,
                :zip_code,
                :lat,
                :lon,
                :mgrs_coord,
                :is_locked,
                :scout_status
            )
            ON DUPLICATE KEY UPDATE
                lat = VALUES(lat),
                lon = VALUES(lon),
                mgrs_coord = VALUES(mgrs_coord),
                is_locked = VALUES(is_locked),
                scout_status = VALUES(scout_status)";

    return $this->query($sql, [
        'address_hash' => $data['address_hash'],
        'full_address' => $data['full_address'],
        'municipality' => $data['municipality'],
        'zip_code'     => $data['zip_code'],
        'lat'          => $data['lat'],
        'lon'          => $data['lon'],
        'mgrs_coord'   => $data['mgrs_coord'],
        'is_locked'    => $data['is_locked'] ?? 1,
        'scout_status' => $data['scout_status'] ?? 'locked'
    ]);
}
}
