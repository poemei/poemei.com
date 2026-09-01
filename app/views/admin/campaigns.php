<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container">
    <h1>Campaigns</h1>

    <?php if ($data['view_mode'] === 'create') : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns">&larr; Back</a></p>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=create">
            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" required><br><br>

            <label for="slug">Slug</label><br>
            <input type="text" id="slug" name="slug" required><br><br>

            <label for="description">Description</label><br>
            <textarea id="description" name="description" rows="5"></textarea><br><br>

            <label for="start_date">Start Date</label><br>
            <input type="date" id="start_date" name="start_date"><br><br>

            <label for="status">Status</label><br>
            <select id="status" name="status">
                <option value="draft">Draft</option>
                <option value="paused">Paused</option>
                <option value="ended">Ended</option>
                <option value="active">Active</option>
            </select><br><br>

            <button type="submit">Create Campaign</button>
        </form>

    <?php elseif ($data['view_mode'] === 'edit' && !empty($data['campaign'])) : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns">&larr; Back</a></p>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=edit&id=<?= (int) $data['campaign']['id'] ?>">
            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars((string) $data['campaign']['title']) ?>" required><br><br>

            <label for="slug">Slug</label><br>
            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars((string) $data['campaign']['slug']) ?>" required><br><br>

            <label for="description">Description</label><br>
            <textarea id="description" name="description" rows="5"><?= htmlspecialchars((string) ($data['campaign']['description'] ?? '')) ?></textarea><br><br>

            <label for="start_date">Start Date</label><br>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars((string) ($data['campaign']['start_date'] ?? '')) ?>"><br><br>

            <label for="status">Status</label><br>
            <select id="status" name="status">
                <option value="draft" <?= (($data['campaign']['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                <option value="paused" <?= (($data['campaign']['status'] ?? '') === 'paused') ? 'selected' : '' ?>>Paused</option>
                <option value="ended" <?= (($data['campaign']['status'] ?? '') === 'ended') ? 'selected' : '' ?>>Ended</option>
                <option value="active" <?= (($data['campaign']['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            </select><br><br>

            <button type="submit">Save Campaign</button>
        </form>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=delete&id=<?= (int) $data['campaign']['id'] ?>" style="margin-top: 12px;">
            <button type="submit">Delete Campaign</button>
        </form>

    <?php elseif ($data['view_mode'] === 'entries' && !empty($data['campaign'])) : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns">&larr; Back</a></p>

        <h2><?= htmlspecialchars((string) $data['campaign']['title']) ?></h2>

        <p>
            <a href="<?= URLROOT ?>/admin/campaigns?action=create_entry&id=<?= (int) $data['campaign_id'] ?>">+ New Entry</a>
            |
            <a href="<?= URLROOT ?>/admin/campaigns?action=edit&id=<?= (int) $data['campaign_id'] ?>">Edit Campaign</a>
        </p>

        <hr>

        <?php if (empty($data['entries'])) : ?>
            <p>No entries yet.</p>
        <?php else : ?>
            <?php foreach ($data['entries'] as $entry) : ?>
                <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
                    <strong>Day <?= (int) $entry['day_index'] ?></strong><br>

                    <?php if (!empty($entry['title'])) : ?>
                        <em><?= htmlspecialchars((string) $entry['title']) ?></em><br>
                    <?php endif; ?>

                    <p><?= nl2br(htmlspecialchars((string) $entry['content'])) ?></p>

                    <small>Status: <?= !empty($entry['is_published']) ? 'Published' : 'Hidden' ?></small>

                    <div style="margin-top: 8px;">
                        <a href="<?= URLROOT ?>/admin/campaigns?action=edit_entry&id=<?= (int) $data['campaign_id'] ?>&entry_id=<?= (int) $entry['id'] ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php elseif ($data['view_mode'] === 'create_entry' && !empty($data['campaign'])) : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns?action=entries&id=<?= (int) $data['campaign_id'] ?>">&larr; Back</a></p>

        <h2>New Entry for <?= htmlspecialchars((string) $data['campaign']['title']) ?></h2>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=create_entry&id=<?= (int) $data['campaign_id'] ?>">
            <label for="day_index">Day (1&ndash;28)</label><br>
            <input type="number" id="day_index" name="day_index" min="1" max="28" required><br><br>

            <label for="title">Title</label><br>
            <input type="text" id="title" name="title"><br><br>

            <label for="content">Content</label><br>
            <textarea id="content" name="content" rows="8" required></textarea><br><br>

            <label>
                <input type="checkbox" name="is_published" value="1" checked>
                Published
            </label><br><br>

            <button type="submit">Save Entry</button>
        </form>

    <?php elseif ($data['view_mode'] === 'edit_entry' && !empty($data['campaign']) && !empty($data['entry'])) : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns?action=entries&id=<?= (int) $data['campaign_id'] ?>">&larr; Back</a></p>

        <h2>Edit Entry</h2>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=edit_entry&id=<?= (int) $data['campaign_id'] ?>&entry_id=<?= (int) $data['entry']['id'] ?>">
            <label for="day_index">Day (1&ndash;28)</label><br>
            <input type="number" id="day_index" name="day_index" min="1" max="28" value="<?= (int) $data['entry']['day_index'] ?>" required><br><br>

            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars((string) ($data['entry']['title'] ?? '')) ?>"><br><br>

            <label for="content">Content</label><br>
            <textarea id="content" name="content" rows="8" required><?= htmlspecialchars((string) $data['entry']['content']) ?></textarea><br><br>

            <label>
                <input type="checkbox" name="is_published" value="1" <?= !empty($data['entry']['is_published']) ? 'checked' : '' ?>>
                Published
            </label><br><br>

            <button type="submit">Save Entry</button>
        </form>

        <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=delete_entry&id=<?= (int) $data['campaign_id'] ?>&entry_id=<?= (int) $data['entry']['id'] ?>" style="margin-top: 12px;">
            <button type="submit">Delete Entry</button>
        </form>

    <?php else : ?>

        <p><a href="<?= URLROOT ?>/admin/campaigns?action=create">+ New Campaign</a></p>

        <?php if (empty($data['campaigns'])) : ?>
            <p>No campaigns yet.</p>
        <?php else : ?>
            <?php foreach ($data['campaigns'] as $campaign) : ?>
                <div style="border: 1px solid #ccc; padding: 12px; margin-bottom: 12px;">
                    <strong><?= htmlspecialchars((string) $campaign['title']) ?></strong><br>
                    <small>Status: <?= htmlspecialchars((string) $campaign['status']) ?></small><br>

                    <?php if (!empty($campaign['start_date'])) : ?>
                        <small>Start: <?= htmlspecialchars((string) $campaign['start_date']) ?></small><br>
                    <?php endif; ?>

                    <div style="margin-top: 8px;">
                        <a href="<?= URLROOT ?>/admin/campaigns?action=entries&id=<?= (int) $campaign['id'] ?>">Entries</a>
                        |
                        <a href="<?= URLROOT ?>/admin/campaigns?action=edit&id=<?= (int) $campaign['id'] ?>">Edit</a>
                        |

                        <?php if (($campaign['status'] ?? '') !== 'active') : ?>

                            <form method="post" action="<?= URLROOT ?>/admin/campaigns?action=activate&id=<?= (int) $campaign['id'] ?>" style="display: inline;">
                                <button type="submit">Activate</button>
                            </form>

                        <?php else : ?>

                            <span style="color: green; font-weight: bold;">● ACTIVE</span>

                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
