<?php
declare(strict_types=1);
if (function_exists('plugin_slot')) { plugin_slot('home_time'); }

// -- Latest announcement helper (home-local, silent on failure) -----------
if (!function_exists('get_latest_announcement_home')) {
    /**
     * Returns newest published announcement from the plugin JSON, or null.
     */
    function get_latest_announcement_home(): ?array
    {
        $docroot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        if ($docroot === '') {
            return null;
        }

        $file = $docroot . '/public/plugins/announcements/data/announcements.json';
        if (!is_readable($file)) {
            return null;
        }

        $raw = @file_get_contents($file);
        if ($raw === false || $raw === '') {
            return null;
        }

        // Strip UTF-8 BOM if present
        if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
            $raw = substr($raw, 3);
        }

        $data = json_decode($raw, true);
        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($data) ||
            empty($data['items']) ||
            !is_array($data['items'])
        ) {
            return null;
        }

        // Only published items
        $items = array_values(array_filter(
            $data['items'],
            static fn(array $item): bool => !empty($item['published'])
        ));

        if ($items === []) {
            return null;
        }

        // Newest first by ISO-like id (string compare is fine for ISO 8601)
        usort(
            $items,
            static fn(array $a, array $b): int =>
                strcmp((string)($b['id'] ?? ''), (string)($a['id'] ?? ''))
        );

        return $items[0] ?? null;
    }
}

echo '<!-- Holidays -->';
$h   = new \plugins\holidays\holiday();
$msg = $h->get_message(date('Y-m-d'));
if ($msg !== '') { ?>
    <section class="home-holiday-wrap">
        <h3 class="home-holiday">
            <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
        </h3>
    </section>
<?php } ?>

<!-- Home -->
<div class="container home-container">
  <div class="row">
    <section class="home-intro">
      <h1 class="home-title">Welcome</h1>
      <p>This is my personal domain, where I build, break, and fix, on repeat, all the time. If you come here and shit's broken, come back later.</p>
      <p>I am a PHP back end, and Go developer, a business owner, and a transgendered felon. See the Notice below.. Yes, I did say that I am a felon.</p>
    </section>
    <!-- Announcements -->
<section>
<div id="latest-announcement" class="my-3 border rounded bg-ligt">
<h3>Announcements</h3>

<?php
if (function_exists('announcements_latest')) {
    announcements_latest();
    //plugin_slot('announcement_latest');
}
?>
</div>
</section>
    <section class="home-intro">
      <h1 class="home-title">Witchy</h1>
      <p>This sites astetic appearance is based on my witchy ass and my gothy, shadow chaotic ways, it very much part of my life and will be part of alot of content.</p>
    </section>
    <section class="home-intro">
      <h1 class="home-title">Media</h1>
      <p>Media and Posts were designed the same way, members only content requires login, this was done to match common usage request.</p>
    </section>
</div>

<!-- Notices -->
<section>
<div class="home-intro">
  <h2>Notices</h2>
</div>
<div id="latest-announcement" class="my-3 p-3 border rounded bg-light">
  <h3 class="h6 mb-1">
    A Warning
  </h3>
  <div class="small">
    <p>I am a Transgendered Woman and I will often post about Transgendered issues from my own perspective, if my opinions or philosophy offends you in anyway, sorry, no, not sorry, this seem like a <strong>YOU</strong> problem. Kindly eject yourself my my domain, snowflake.
    </p>
    <p><small><em>Posted on December 24, 2025</em></small></p>
  </div>
</div>
</section>

<!-- Sentinel -->
<section>
<?php
$docroot  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '.', '/\\');
$sentinel = $docroot . '/public/plugins/sentinel/plugin.php';

if (is_file($sentinel)) {
?>
    <div id="latest-announcement" class="my-3 p-3 border rounded bg-light">
    <h3 class="h6 mb-1">Sentinel is Tracking</h3>
    <div class="small">
    <p>This site is protected by the <a href="/projects/sentinel"><strong>Sentinel</strong></a> Plugin for the Chaos CMS<br><em>What this means, is your activity on my domain is being looked at, and if you are doing some shady shit, ima catch you.</em></p>
   <p><small><em>Posted December 24, 2025</em></small></p>
   <p><small><em>Another one of my creations</em></small></p>
   </div>
   </div>
<?php
}
?>
</section>

<!-- Business -->
<section>
<p>I am the Owner of <a href="https://www.stn-labz.com" target="_blank">Stn-Labz, LLC</a> and the Creator of the <a href="https://www.chaoscms.org" target=_blank">ChAoS CMS</a>, which this domain is run by.</p>
</section>

