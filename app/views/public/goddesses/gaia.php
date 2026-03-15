<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Gaia';
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
Gaia is the ancient spirit of the Earth itself. In Greek myth she is the primal mother, the living ground from which life emerges. Gaia reminds us that all existence grows from the same soil and returns to it in time.

**Symbolism**
She represents nature, fertility, grounding, and the deep connection between humanity and the living world.

**Human Reflection**
To work with Gaia is to remember where we come from. Every breath, every meal, every shelter begins with the Earth.

**Personal Invocation**

*Gaia, mother of soil and sea*,
*root me in the living world*.
*Teach me to walk gently upon the land.*
*and remember that I belong to the Earth.*
";
echo $this->render_md->markdown($text);
?>
  </section>
</div>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
<?php require APPROOT . '/views/inc/foot.php'; ?>
