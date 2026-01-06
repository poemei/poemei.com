<?php
// Load the JSON data (e.g. data/roadmap.json)
$jsonFile = __DIR__ . '/../data/roadmap.json';
$data = json_decode(file_get_contents($jsonFile), true) ?? ['items' => []];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data['items'] as $i => $item) {
        $data['items'][$i]['label']    = $_POST["label_$i"]    ?? $item['label'];
        $data['items'][$i]['percent']  = (float)($_POST["percent_$i"] ?? $item['percent']);
        $data['items'][$i]['category'] = $_POST["category_$i"] ?? $item['category'];
        $data['items'][$i]['text']     = $_POST["text_$i"]     ?? $item['text'];
    }
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = 'Items updated.';
}

 if (!empty($message)) echo "<p>$message</p>"; ?>
<form method="post">
    <?php foreach ($data['items'] as $i => $item): ?>
        <fieldset>
            <legend>Item <?= $i + 1; ?></legend>
            <label>
                Label:
                <input type="text" name="label_<?= $i ?>" value="<?= htmlspecialchars($item['label']) ?>">
            </label>
            <label>
                Percent:
                <input type="number" name="percent_<?= $i ?>" value="<?= htmlspecialchars($item['percent']) ?>" min="0" max="100">
            </label>
            <label>
                Category:
                <input type="text" name="category_<?= $i ?>" value="<?= htmlspecialchars($item['category']) ?>">
            </label>
            <label>
                Text:
                <textarea name="text_<?= $i ?>"><?= htmlspecialchars($item['text']) ?></textarea>
            </label>
        </fieldset>
    <?php endforeach; ?>
    <button type="submit">Save</button>
</form>