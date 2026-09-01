<?php
/* [AI:GPT-5 | 2026-08-26 22:49:59 UTC] */
if (!function_exists('sw_get_moon_phase')) {
    /**
     * Returns the current moon phase.
     *
     * @return array{index: int, label: string, icon: string}
     */
    function sw_get_moon_phase(?DateTimeInterface $date = null): array
    {
        $date ??= new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $day = (int) $date->format('j');

        if ($month < 3) {
            $year--;
            $month += 12;
        }

        $month++;

        $c = (int) floor(365.25 * $year);
        $e = (int) floor(30.6 * $month);

        // Days since a known new moon.
        $jd = $c + $e + $day - 694039.09;

        // Length of a synodic month.
        $jd /= 29.5305882;

        $b = (int) floor($jd);
        $jd -= $b;

        $phase = (int) round($jd * 8);

        if ($phase >= 8) {
            $phase = 0;
        }

        $phases = [
            0 => [
                'label' => 'New Moon',
                'icon' => '🌑',
            ],
            1 => [
                'label' => 'Waxing Crescent',
                'icon' => '🌒',
            ],
            2 => [
                'label' => 'First Quarter',
                'icon' => '🌓',
            ],
            3 => [
                'label' => 'Waxing Gibbous',
                'icon' => '🌔',
            ],
            4 => [
                'label' => 'Full Moon',
                'icon' => '🌕',
            ],
            5 => [
                'label' => 'Waning Gibbous',
                'icon' => '🌖',
            ],
            6 => [
                'label' => 'Last Quarter',
                'icon' => '🌗',
            ],
            7 => [
                'label' => 'Waning Crescent',
                'icon' => '🌘',
            ],
        ];

        $meta = $phases[$phase] ?? $phases[0];

        return [
            'index' => $phase,
            'label' => $meta['label'],
            'icon' => $meta['icon'],
        ];
    }
}

$moon = sw_get_moon_phase();
?>
</main>

<footer class="pm-footer">
    <div class="pm-footer-copyright">
        <p>
            © <?= date('Y'); ?>
            <?= htmlspecialchars(
                $SITE['copyright_name'] ?? 'Chaos MVC',
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </p>
    </div>

    <div class="pm-footer-center">
        <div class="pm-footer-moon">
            <span
                class="pm-footer-moon-icon"
                aria-hidden="true"
            ><?= $moon['icon']; ?></span>

            <span class="pm-footer-moon-label">
                <?= htmlspecialchars(
                    $moon['label'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </span>

            <span class="pm-footer-moon-note">· UTC</span>
        </div>

        <div class="pm-footer-meta">
            <a href="/feed">RSS Feed</a>
            <span aria-hidden="true">·</span>

            <span>
                Built with
                <a
                    href="https://www.chaos-mvc.org"
                    target="_blank"
                    rel="noopener noreferrer"
                >Chaos MVC</a>
            </span>
        </div>
    </div>

    <nav
        class="pm-footer-links"
        aria-label="Footer navigation"
    >
        <a href="/">Home</a>
        <a href="/legal/terms">Terms</a>
        <a href="/legal/privacy">Privacy</a>
    </nav>
</footer>

<!-- End the container -->
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"
></script>
</body>
</html>
<?php /* [End AI:GPT-5] */ ?>