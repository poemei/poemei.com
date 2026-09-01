<?php

declare(strict_types=1);

/**
 * Site-owned home controller running from the Chaos MVC userland namespace.
 */
final class home extends controller
{
    public function index($url_params = null): void
    {
        /* User modules intentionally do not fall back to Core models. */
        require_once APPROOT . '/models/announcements_model.php';

        $announcements = new announcements_model();

        $data = [
            'featured_announcement' => $announcements->get_latest_single(),
            'holiday_message' => $this->get_holiday(),
        ];

        $this->view('index', $data);
    }

    private function get_holiday(): string
    {
        $date = gmdate('Y-m-d');
        $year = substr($date, 0, 4);

        return match ($date) {
            $year . '-01-01' => 'A new cycle begins.<br><em>Bene Sit.</em>',
            $year . '-02-01' => 'The first stirrings of spring and the New Year. Greetings on Imbolc.<br><em>Bene Sit.</em>',
            $year . '-03-21' => 'Night and day in balance. Joyous Ostara.<br><em>Aequinoctium Vernum.</em>',
            $year . '-05-01' => 'The fires are lit. Happy Beltane.<br><em>Vivat Vita.</em>',
            $year . '-06-21' => 'The longest day. Standing in the light of Litha.<br><em>Solstitium Aestivum.</em>',
            $year . '-08-01' => 'The grain is cut. Blessings of the first harvest.<br><em>Macte Virtute.</em>',
            $year . '-09-21' => 'The harvest home. Abundance on Mabon.<br><em>Aequinoctium Autumnale.</em>',
            $year . '-10-31' => 'The veil is thin. Honor to the ancestors on Samhain.<br><em>Memoria Aeterna.</em>',
            $year . '-12-21' => 'The longest night. The sun returns at Yule.<br><em>Sol Invictus.</em>',
            default => '',
        };
    }
}
