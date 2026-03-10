<?php
if (!function_exists('sw_get_moon_phase')) {
    /**
     * Returns current moon phase as ['index' => 0-7, 'label' => string, 'icon' => string].
     * Simple approximation, good enough for display.
     */
    function sw_get_moon_phase(?DateTimeInterface $date = null): array
    {
        $date ??= new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $year  = (int)$date->format('Y');
        $month = (int)$date->format('n');
        $day   = (int)$date->format('j');

        if ($month < 3) {
            $year--;
            $month += 12;
        }

        $month++;

        $c  = (int)floor(365.25 * $year);
        $e  = (int)floor(30.6 * $month);
        $jd = $c + $e + $day - 694039.09;      // days since known new moon
        $jd /= 29.5305882;                     // length of synodic month
        $b  = (int)floor($jd);
        $jd -= $b;

        $phase = (int)round($jd * 8);
        if ($phase >= 8) {
            $phase = 0;
        }

        $phases = [
            0 => ['label' => 'New Moon',         'icon' => '🌑'],
            1 => ['label' => 'Waxing Crescent',  'icon' => '🌒'],
            2 => ['label' => 'First Quarter',    'icon' => '🌓'],
            3 => ['label' => 'Waxing Gibbous',   'icon' => '🌔'],
            4 => ['label' => 'Full Moon',        'icon' => '🌕'],
            5 => ['label' => 'Waning Gibbous',   'icon' => '🌖'],
            6 => ['label' => 'Last Quarter',     'icon' => '🌗'],
            7 => ['label' => 'Waning Crescent',  'icon' => '🌘'],
        ];

        $meta = $phases[$phase] ?? $phases[0];

        return [
            'index' => $phase,
            'label' => $meta['label'],
            'icon'  => $meta['icon'],
        ];
    }
}
$moon = sw_get_moon_phase();
?>
</main>

<footer class="sw-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-top: 1px solid #333; font-size: 0.9rem;">
    <div style="flex: 1; text-align: left;">
        <p style="margin-left: 20px;">© <?= date('Y'); ?> <?= htmlspecialchars($SITE['name'] ?? 'Poe Mei', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <div class="sw-footer-moon" style="flex: 1; text-align: center;">
        <span class="sw-footer-moon-icon"><?= $moon['icon']; ?></span>
        <span class="sw-footer-moon-label" style="margin: 0 5px;">
            <?= htmlspecialchars($moon['label'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <span class="sw-footer-moon-note" style="color: #666;">· UTC</span>
    </div>

    <div style="flex: 1; text-align: right;">
        <a href="/legal/terms" style="text-decoration: none; color: inherit; margin-left: 10px;">Terms</a>
        <a href="/legal/privacy" style="text-decoration: none; color: inherit; margin-left: 10px;">Privacy</a>
        <a href="/security" style="text-decoration: none; color: inherit; margin-left: 10px; margin-right: 20px;">Security</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
