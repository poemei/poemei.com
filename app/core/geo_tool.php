<?php
declare(strict_types=1);

class geo_tool
{
    private const SEMI_MAJOR_AXIS = 6378137.0;

    /**
     * Resolve address to lat/lon/MGRS
     */
    public function resolve_geo(
        string $address,
        string $zip,
        string $municipality,
        string $state = ''
    ): array
    {
        require_once APPROOT . '/models/maps_model.php';
        $db = new maps_model();

        // Normalize + hash
        $full = strtolower(trim("$address, $municipality, $state $zip"));
        $full = preg_replace('/\s+/', ' ', $full);
        $hash = md5($full);

        // Check cache
        $existing = $db->get_by_hash($hash);

        if ($existing && !empty($existing['lat']) && !empty($existing['lon'])) {
            return [
                'lat'    => (float)$existing['lat'],
                'lon'    => (float)$existing['lon'],
                'mgrs'   => $existing['mgrs_coord'],
                'hash'   => $hash,
                'status' => 'resolved'
            ];
        }
        //error_log("GEOCODE QUERY: " . $full);
        // Nominatim via cURL
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($full);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'ArsRosaic/1.0 (office@arsrosaic.org)',
            CURLOPT_TIMEOUT        => 10
        ]);

        $response = curl_exec($ch);
        

        if ($response === false) {
            curl_close($ch);
            return [
                'lat'    => null,
                'lon'    => null,
                'mgrs'   => null,
                'hash'   => $hash,
                'status' => 'pending'
            ];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (empty($data[0]['lat']) || empty($data[0]['lon'])) {
            return [
                'lat'    => null,
                'lon'    => null,
                'mgrs'   => null,
                'hash'   => $hash,
                'status' => 'pending'
            ];
        }

        $lat = (float)$data[0]['lat'];
        $lon = (float)$data[0]['lon'];

        $mgrs = self::ll_to_mgrs($lat, $lon);

        // Store in geo_cache
        $db->insert_geo_cache([
            'address_hash' => $hash,
            'full_address' => $address,
            'municipality' => $municipality,
            'zip_code'     => $zip,
            'lat'          => $lat,
            'lon'          => $lon,
            'mgrs_coord'   => $mgrs,
            'is_locked'    => 1,
            'scout_status' => 'locked'
        ]);

        return [
            'lat'    => $lat,
            'lon'    => $lon,
            'mgrs'   => $mgrs,
            'hash'   => $hash,
            'status' => 'resolved'
        ];
    }

    /**
     * Convert Lat/Lon to MGRS (your simplified engine)
     */
    public static function ll_to_mgrs(float $lat, float $lon): string
    {
        $zone = (int)(($lon + 180) / 6) + 1;
        $band = self::get_latitude_band($lat);

        $lon0 = ($zone - 1) * 6 - 180 + 3;
        $lat_rad = deg2rad($lat);
        $lon_rad = deg2rad($lon - $lon0);
        $k0 = 0.9996;

        $easting = 500000 + ($k0 * self::SEMI_MAJOR_AXIS * $lon_rad * cos($lat_rad));
        $northing = ($k0 * self::SEMI_MAJOR_AXIS * $lat_rad);

        $grid_letters = self::get_100km_id($zone, $easting, $northing);

        $e_final = str_pad((string)(int)fmod($easting, 100000), 5, '0', STR_PAD_LEFT);
        $n_final = str_pad((string)(int)fmod($northing, 100000), 5, '0', STR_PAD_LEFT);

        return "{$zone}{$band} {$grid_letters} {$e_final} {$n_final}";
    }

    private static function get_latitude_band(float $lat): string
    {
        $bands = "CDEFGHJKLMNPQRSTUVWXX";
        return $bands[(int)floor(($lat + 80) / 8)];
    }

    private static function get_100km_id(int $zone, float $e, float $n): string
    {
        $set = $zone % 6;
        if ($set === 0) $set = 6;

        $e_idx = (int)floor($e / 100000);
        $n_idx = (int)floor(fmod($n, 2000000) / 100000);

        $col_chars = ["ABCDEFGH", "JKLMNPQR", "STUVWXYZ"];
        $row_chars = ["ABCDEFGHJKLMNPQRSTUV", "FGHJKLMNPQRSTUVABCDE"];

        return $col_chars[($set - 1) % 3][$e_idx - 1]
             . $row_chars[($set - 1) % 2][$n_idx % 20];
    }
}
