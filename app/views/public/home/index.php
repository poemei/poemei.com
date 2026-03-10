<?php require APPROOT . '/views/inc/head.php';

// Holday Message
if (!empty($data['holiday_message'])) {
  echo '<div class="row">';
  echo '<section class="home-holiday-wrap">';
  echo '<h3 class="home-holiday">' . $data['holiday_message'] . '</h3>';
  echo '</section>';
  echo '</div>';
}
?>
  <div class="row">
  <section>
  <div class="content-wrap">
    <img src="/assets/icons/icon.png" class="wrap-left" alt="Trans Developer">
    <h2>Greetings and Welcome</h2>
    <p>To Poe Mei dot Com</p>
    <p>This platform is my own custom MVC:
    <ul>
      <li> <strong>M</strong>odel, which handles all database functions.</li>
      <li> <strong>V</strong>iew, which makes the site look how you want it to.</li>
      <li> <strong>C</strong>ontroller, which acts as a traffic cop, directing traffic.
    </ul>
    </p>
  </div>
</section>
</div>
<div class="row">
<?php 
// Announcements
    if(isset($data['featured_announcement']) && $data['featured_announcement'] !== false) : 
    $post = $data['featured_announcement']; ?>
    <section id="latest-announcement">
        <div class="announcement-content">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
            <small>Posted: <?php echo date('Y.m.d', strtotime($post['created_at'])); ?></small>
        </div>
    </section>
<?php endif; ?>
</div>
<div class="row">
  <section>
  <div class="content-wrap">
    <img src="/assets/img/fuk_you.png" class="wrap-right" alt="I dont fucking care">
    <?php
    $about_me = "
## A little about me
I am a semi-retired Soldier, a web developer, a small business owner, creator of web platforms, and I am a **Transgender Woman**. 

From time to time, when the opportunity arises, I will post about trans related issues, and my hatred of man's law, and society in general. I am also a **Witch**, as if you cannot tell from this site's aesthetic. 

That hawt lil bitch in the background? Yeah, that is an AI rendition of me and what my face could look like after some cosmetic facial surgery. 

You will find that I really do not give a fuck about what your opinion is of me or what I do. I get enough of that from the people that I consider within my very small bubble.

This platform is my creation, and I'm always working on it, so if you get here and shit is broke, ~~suck it up buttercup~~ and come back later.

> **REMEMBER:** Hate is a **THEM** problem, their hatred of you is not **YOUR** problem.
---
    ";

    echo $this->render_md->markdown($about_me);
    ?>
    </div>
</section>
</div>

<div class="row">
  <section>
  <div class="content-wrap">
    <img src="/assets/img/pm_developers.png" class="wrap-left" alt="Girlie Witchy Developers">
  <?php
  $recruiting = "## Female or Trans Female Web Developers
    Care to jump on this project? its built with:
    - PHP
    - MySQL
    - HTML5
    - CSS4
    - Some Bootstrap
  ";
echo $this->render_md->markdown($recruiting);
?>
<p>If this is your sort of thing, and you got an interest, always check the <a href="/changelog">Changelog</a>, and send me an email to <a href="mailto:poe@poemei.com">poe@poemei.com</a>. I would love to be able to work with you. <b>Males</b> I am so sorry but you need not apply, no offense, but I was <b>once one of you</b>, and I <b>care not</b>, to be around <b><em>your energy</em></b>.</p>
</div>
  </section>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
