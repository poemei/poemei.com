<?php require APPROOT . '/views/inc/head.php';
$goddess = 'Lilith';
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
Lilith is often remembered as a figure of independence and refusal to submit. In mythology and later folklore she became a symbol of the woman who chooses autonomy over obedience. Whether viewed as demonized rebel or liberated spirit, Lilith stands for personal sovereignty and the courage to exist on one's own terms.

**Symbolism**
Lilith represents freedom, personal authority, and the rejection of imposed roles. She appears wherever someone chooses authenticity over acceptance.

**Human Reflection**
To walk with Lilith is to remember that your identity belongs to you alone. She reminds us that self-definition is an act of strength.

**Personal Invocation**

*Lilith, spirit of the untamed self*,
*teach me to stand in my own truth*.
*Let me walk my path without fear of judgment*,
*and claim the life that is truly mine.*
";
echo $this->render_md->markdown($text);
?>
  </section>
</div>
<p><small><a href="/">Home</a> >> <a href="/goddesses">Goddesses</a> >> <strong><?= $goddess ?></strong></small></p>
<?php require APPROOT . '/views/inc/foot.php'; ?>
