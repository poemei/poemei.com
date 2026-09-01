<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Hekate';
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
Hecate is the goddess of crossroads and hidden paths. In ancient traditions she was invoked as a guide through darkness, a protector of travelers, and a keeper of sacred thresholds.

**Symbolism**
She embodies transition, intuition, mystery, and the wisdom needed when life reaches a turning point.

**Human Reflection**
Every person eventually stands at a crossroads. Hecate reminds us that uncertainty is not weakness—it is the beginning of transformation.

**Personal Invocation**

*Hecate, keeper of the crossroads,*
*light my path when the road is unclear.*
*Grant me the courage to choose my direction*
*and walk forward without fear.*
";
echo $this->render_md->markdown($text);
?>
  </section>
</div>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
<?php require APPROOT . '/views/inc/foot.php'; ?>
