<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Kali';
?>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
  <div class="row">
  <section>
  <img src="/assets/img/goddesses/<?= lcfirst($goddess) ?>.png">
  </section>
  <section>
  <h1><?= $goddess ?></h1>
  </section>
  <section>
   <?php
   $text = "
   **Introduction**
Kali is a powerful goddess of transformation in Hindu tradition. Though often portrayed as fierce, her purpose is not destruction for its own sake—it is the removal of illusion and the clearing away of what must end so something new can begin.

**Symbolism**
She represents transformation, liberation, courage, and the power to confront truth.

**Human Reflection**
Kali teaches that growth often requires endings. The courage to let go can open the door to profound change.

**Personal Invocation**

*Kali, force of transformation,*
*help me release what no longer serves me.*
*Give me strength to face truth without fear*
*and the courage to begin again.*
";
echo $this->render_md->markdown($text);
?>
  </section>
</div>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
<?php require APPROOT . '/views/inc/foot.php'; ?>
