<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Luna';
?>

<p><small>
<a href="/">Home</a> >> 
<a href="/goddesses">Goddesses</a> >> 
<strong><?= $goddess ?></strong>
</small></p>

<div class="row">

<section>
<img src="/assets/img/goddesses/<?= lcfirst($goddess) ?>.png" alt="<?= $goddess ?>">
</section>

<section>
<h1><?= $goddess ?></h1>
</section>

<section>

<?php

$text = "

**Introduction**

Luna is the spirit of the moon and the cycles that shape time itself. Across cultures the moon has guided calendars, tides, and the rhythms of life. Luna represents the quiet, constant motion of waxing and waning.

**Symbolism**

She embodies cycles, intuition, reflection, and the understanding that life moves in phases.

**Human Reflection**

Luna reminds us that nothing remains fixed. Just as the moon changes shape, so do we.

";

echo $this->render_md->markdown($text);

?>

</section>

</div>

<hr>

<?php

$known_new_moon = strtotime("2000-01-06 18:14:00");
$now = time();
$lunar_cycle = 29.530588853;

$days_since = ($now - $known_new_moon) / 86400;
$cycle_position = fmod($days_since, $lunar_cycle);

if ($cycle_position < 1) {
    $phase = "New Moon";
} elseif ($cycle_position < 7.38) {
    $phase = "Waxing Crescent";
} elseif ($cycle_position < 8.38) {
    $phase = "First Quarter";
} elseif ($cycle_position < 14.77) {
    $phase = "Waxing Gibbous";
} elseif ($cycle_position < 15.77) {
    $phase = "Full Moon";
} elseif ($cycle_position < 22.15) {
    $phase = "Waning Gibbous";
} elseif ($cycle_position < 23.15) {
    $phase = "Last Quarter";
} else {
    $phase = "Waning Crescent";
}

$days_until_full = 14.77 - $cycle_position;
if ($days_until_full < 0) {
    $days_until_full += $lunar_cycle;
}

$days_until_new = $lunar_cycle - $cycle_position;

$cycle_day = floor($cycle_position);
$days_until_full = ceil($days_until_full);
$days_until_new = ceil($days_until_new);

?>

<section style="text-align:center; padding:30px 0;">

<strong>Current Lunar Phase</strong>

<p><strong><?= $phase ?></strong></p>

<p>Day <?= $cycle_day ?> of the lunar cycle</p>

<p><strong>Next Full Moon:</strong> <?= $days_until_full ?> days</p>

<p><strong>Next New Moon:</strong> <?= $days_until_new ?> days</p>

<p style="font-size:28px;">🌑 🌒 🌓 🌔 🌕 🌖 🌗 🌘</p>

</section>

<hr>

<section>

<?php

$text = "

**Lunar Time vs Solar Time**

Early cultures measured time by the moon. The lunar cycle provided a visible rhythm that divided time into natural segments of roughly four weeks.

However, agricultural societies required a calendar aligned with the seasons. Because the Earth takes about 365 days to orbit the sun, solar calendars eventually replaced lunar ones for civil use.

The Gregorian calendar, introduced in 1582, follows the solar year and divides it into twelve months. Even so, the lunar rhythm remains deeply rooted in human culture across the world.

**Personal Invocation**

*Luna, keeper of the night sky,*  
*guide me through the cycles of change.*  
*Teach me patience in the dark phases*  
*and gratitude in the light.*

";

echo $this->render_md->markdown($text);

?>

</section>

<p><small>
<a href="/">Home</a> >> 
<a href="/goddesses">Goddesses</a> >> 
<strong><?= $goddess ?></strong>
</small></p>

<?php require APPROOT . '/views/inc/foot.php'; ?>
