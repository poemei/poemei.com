<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>Poe Mei | Core Log</title>
    <link><?php echo URLROOT; ?></link>
    <description>The latest updates from the Chaos MVC Core.</description>
    <language>en-us</language>
    <atom:link href="<?php echo URLROOT; ?>/feed" rel="self" type="application/rss+xml" />

    <?php foreach($data['posts'] as $post) : ?>
    <item>
        <title><?php echo htmlspecialchars($post['title']); ?></title>
        <link><?php echo URLROOT; ?>/posts/show/<?php echo $post['slug'] ?? $post['id']; ?></link>
        <guid isPermaLink="true"><?php echo URLROOT; ?>/posts/show/<?php echo $post['slug'] ?? $post['id']; ?></guid>
        <pubDate><?php echo date(DATE_RSS, strtotime($post['created_at'])); ?></pubDate>
        <description><![CDATA[<?php echo $post['body']; ?>]]></description>
    </item>
    <?php endforeach; ?>

</channel>
</rss>
