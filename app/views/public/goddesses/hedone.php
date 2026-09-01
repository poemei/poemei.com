<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Hedone';
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
Hedone, whose name literally means “pleasure,” represents the joy of living. In ancient Greek thought she embodied the fulfillment that comes from experiencing life fully.

**Symbolism**
She stands for joy, sensuality, contentment, and the beauty of human experience.

**Human Reflection**
Hedone reminds us that life is not only about struggle and survival. It is also about laughter, beauty, connection, and delight.

**Personal Invocation**

*Hedone, spirit of joy,*
*remind me that life is meant to be lived.*
*Let me embrace beauty, laughter, and pleasure*
*without guilt or fear.*
";
echo $this->render_md->markdown($text);
?>
  </section>
</div>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
<?php require APPROOT . '/views/inc/foot.php'; ?>
