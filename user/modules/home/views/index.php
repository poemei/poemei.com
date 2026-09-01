<?php

declare(strict_types=1);

/*
 * [AI:GPT-5.6 Sol | 2026-09-01 03:18:00 UTC]
 */

require APPROOT . '/views/inc/head.php';

$featuredAnnouncement = $data['featured_announcement'] ?? false;
?>

<main>

<?php if (!empty($data['holiday_message'])): ?>
    <section>
        <h3>
            <?= htmlspecialchars(
                (string) $data['holiday_message'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </h3>
    </section>
<?php endif; ?>

<section>
    <img
        src="<?= htmlspecialchars(
            theme::assetUrl('icons/icon.png'),
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
        class="img-left"
        alt="Poe Mei"
    >

    <h1>Greetings and Welcome</h1>

    <p>To Poe Mei dot Com</p>

    <p>
        <em>
            “We met for a reason, either you're a blessing or a lesson”
            — Tu Pac
        </em>
    </p>

    <p>
        This is my little corner of the Internet.
    </p>

    <p>
        It is messy, occasionally ridiculous, perpetually under construction,
        and unmistakably mine.
    </p>
</section>

<?php if (is_array($featuredAnnouncement)): ?>
    <section id="latest-announcement">
        <h2>
            <?= htmlspecialchars(
                (string) ($featuredAnnouncement['title'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </h2>

        <div>
            <?= (string) ($featuredAnnouncement['body'] ?? ''); ?>
        </div>

        <?php if (!empty($featuredAnnouncement['created_at'])): ?>
            <p>
                <small>
                    Posted:
                    <?= htmlspecialchars(
                        date(
                            'Y.m.d',
                            strtotime(
                                (string) $featuredAnnouncement['created_at']
                            )
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </small>
            </p>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section>
    <img
        src="<?= htmlspecialchars(
            theme::assetUrl('img/me_sl.png'),
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
        class="img-right"
        alt="I dont fucking care"
    >

    <h2>A little about me</h2>

    <p>
        Hi. I'm <strong>Poe Mei</strong>.
    </p>

    <p>
        I'm messy, but not careless.
    </p>

    <p>
        Ridiculous, but not frivolous.
    </p>

    <p>
        Serious about the things that matter, but allergic to taking myself
        too seriously.
    </p>

    <p>
        I'm curious enough to wander into weird places and skeptical enough
        not to believe everything I find there.
    </p>

    <p>
        I'm witchy, but no hocus-pocus, ever.
    </p>

    <p>
        Spiritual, but not dogmatic, cause religion is control cloaked in
        salvation.
    </p>

    <p>
        I'm a <strong>Transgender Woman</strong>. That's part of who I am,
        not everything there is to know about me.
    </p>

    <p>
        I make things. I break things. Sometimes I spend entirely too much
        time making something only to look at it and decide,
        <em>nah, that ain't it</em>, and start over.
    </p>

    <p>
        I don't need to know everything.
    </p>

    <p>
        I'd rather say <strong>I don't know</strong> and go find out than
        pretend certainty I haven't earned.
    </p>

    <p>
        This site reflects that.
    </p>

    <p>
        There will be development stuff here because I develop things.
        There will be opinions because occasionally I have those too.
        There will be strange rabbit holes, unfinished ideas, things that
        make me laugh, things I care deeply about, and probably something
        broken because I was fucking with it again.
    </p>

    <p>
        This isn't a corporate homepage.
    </p>

    <p>
        It's mine.
    </p>

    <p>
        So wander around.
    </p>

    <p>
        You might find something interesting.
    </p>

    <blockquote>
        <p>
            <strong>REMEMBER:</strong>
            Hate is a <strong>THEM</strong> problem, their hatred of you is
            not <strong>YOUR</strong> problem.
        </p>
    </blockquote>

    <p>
        Don't be a <strong>THEM</strong>, K? <em>Snowflake</em>?
    </p>
</section>

<section>
    <img
        src="<?= htmlspecialchars(
            theme::assetUrl('img/pm_developers.png'),
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
        class="img-left"
        alt="Girlie Witchy Developers"
    >

    <h2>Sometimes I Build Shit</h2>

    <p>
        Development is one of the things I do, and this website is one of
        the things I've built.
    </p>

    <p>
        It changes.
    </p>

    <p>
        Sometimes intentionally.
    </p>

    <p>
        Sometimes because I touched something I probably should have left
        the fuck alone.
    </p>

    <p>
        If you're interested in the development side of what I do, you can
        poke around there too.
    </p>

    <p>
        Check out the <a href="/changelog">Changelog</a>,
        take a look at <a href="/recruiter">Recruiting</a>,
        or wander into the <a href="/developer">Developers Portal</a>.
    </p>
</section>

</main>

<?php

require APPROOT . '/views/inc/foot.php';

/* [End AI:GPT-5.6 Sol] */