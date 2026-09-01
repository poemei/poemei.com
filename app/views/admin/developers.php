<?php

/**
 * Developer Certification Administration
 *
 * Displays certification lifecycle queues and individual
 * certification management.
 *
 * @package ChaosMVC
 * @category Developers
 */

require APPROOT . '/views/inc/head.php';

/* [AI:GPT-5.6 Sol | 2026-08-18 18:52:00 UTC] */

$mode = $data['mode'] ?? 'list';

$certifications = $data['certifications'] ?? [];
$certification = $data['certification'] ?? [];

$filter = $data['filter'] ?? 'all';

$counts = $data['counts'] ?? [
    'all' => 0,
    'pending' => 0,
    'active' => 0,
    'suspended' => 0,
    'revoked' => 0,
    'expired' => 0,
];
?>

<style>
    .developers-admin {
        max-width: 1100px;
        margin: 0 auto;
    }

    .developers-admin-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 1.5rem 0;
    }

    .developers-admin-filters a {
        display: inline-block;
        padding: 0.55rem 0.75rem;
        border: 1px solid #bbb;
        border-radius: 5px;
        text-decoration: none;
    }

    .developers-admin-filters a.active {
        font-weight: bold;
        border-color: #333;
    }

    .developers-admin table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }

    .developers-admin th,
    .developers-admin td {
        border: 1px solid #ddd;
        padding: 0.7rem;
        text-align: left;
        vertical-align: top;
    }

    .developers-admin th {
        font-weight: bold;
    }

    .certification-detail {
        max-width: 850px;
    }

    .certification-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .certification-row {
        margin-bottom: 1rem;
    }

    .certification-form {
        margin-top: 2rem;
    }

    .certification-field {
        margin-bottom: 1.5rem;
    }

    .certification-field label {
        display: block;
        font-weight: bold;
        margin-bottom: 0.4rem;
    }

    .certification-field select {
        width: 100%;
        box-sizing: border-box;
        padding: 0.7rem;
    }

    .certification-error {
        border: 1px solid #a00;
        padding: 1rem;
        margin: 1rem 0;
    }

    .certification-terminal {
        border: 1px solid #aaa;
        border-radius: 8px;
        padding: 1.25rem;
        margin-top: 2rem;
    }

    .certification-empty {
        padding: 2rem;
        text-align: center;
    }

    .certification-actions {
        margin-top: 1.5rem;
    }

    .certification-actions a {
        margin-right: 1rem;
    }

    @media (max-width: 800px) {
        .developers-admin table,
        .developers-admin thead,
        .developers-admin tbody,
        .developers-admin th,
        .developers-admin td,
        .developers-admin tr {
            display: block;
        }

        .developers-admin thead {
            display: none;
        }

        .developers-admin tr {
            margin-bottom: 1rem;
            border: 1px solid #ddd;
        }

        .developers-admin td {
            border: 0;
            border-bottom: 1px solid #eee;
        }
    }
</style>

<div class="developers-admin">

<?php if ($mode === 'certification'): ?>

    <?php
    $currentStatus = (string) ($certification['status'] ?? '');

    $terminal = $currentStatus === 'revoked';

    $statusOptions = [];

    switch ($currentStatus) {
        case 'pending':
            $statusOptions = [
                'active' => 'Active',
                'revoked' => 'Revoked',
            ];
            break;

        case 'active':
            $statusOptions = [
                'suspended' => 'Suspended',
                'revoked' => 'Revoked',
                'expired' => 'Expired',
            ];
            break;

        case 'suspended':
            $statusOptions = [
                'active' => 'Active',
                'revoked' => 'Revoked',
            ];
            break;

        case 'expired':
            $statusOptions = [
                'active' => 'Active',
                'revoked' => 'Revoked',
            ];
            break;
    }
    ?>

    <div class="certification-detail">

        <h1>Manage Certification</h1>

        <p>
            <a href="<?= URLROOT; ?>/developers/admin">
                Back to Certification Admin
            </a>
        </p>

        <?php if (!empty($data['error'])): ?>

            <div class="certification-error">
                <?= htmlspecialchars((string) $data['error']); ?>
            </div>

        <?php endif; ?>

        <div class="certification-card">

            <h2>
                <?= htmlspecialchars(
                    (string) ($certification['public_name'] ?? '')
                ); ?>
            </h2>

            <div class="certification-row">
                <strong>Certification ID:</strong>
                <?= (int) ($certification['id'] ?? 0); ?>
            </div>

            <div class="certification-row">
                <strong>Developer:</strong>

                <a
                    href="<?= URLROOT; ?>/developers/profile/<?= (int) ($certification['developer_id'] ?? 0); ?>"
                >
                    <?= htmlspecialchars(
                        (string) ($certification['public_name'] ?? '')
                    ); ?>
                </a>
            </div>

            <div class="certification-row">
                <strong>Certification:</strong>

                <?php
                $certificationType =
                    (string) ($certification['certification_type'] ?? '');

                $certificationLabels = [
                    'theme' => 'Chaos Theme Developer',
                    'module' => 'Certified Module Developer',
                    'developer' => 'Chaos Certified Developer',
                ];
                ?>

                <?= htmlspecialchars(
                    $certificationLabels[$certificationType]
                    ?? ucfirst($certificationType)
                ); ?>
            </div>

            <div class="certification-row">
                <strong>Credential ID:</strong>

                <?= htmlspecialchars(
                    (string) ($certification['credential_id'] ?? '')
                ); ?>
            </div>

            <div class="certification-row">
                <strong>Status:</strong>

                <?= htmlspecialchars(
                    ucfirst($currentStatus)
                ); ?>
            </div>

            <div class="certification-row">
                <strong>Awarded:</strong>

                <?= !empty($certification['awarded_at'])
                    ? htmlspecialchars(
                        (string) $certification['awarded_at']
                    )
                    : '—'; ?>
            </div>

            <div class="certification-row">
                <strong>Expires:</strong>

                <?= !empty($certification['expires_at'])
                    ? htmlspecialchars(
                        (string) $certification['expires_at']
                    )
                    : '—'; ?>
            </div>

            <div class="certification-row">
                <strong>Created:</strong>

                <?= htmlspecialchars(
                    (string) ($certification['created_at'] ?? '')
                ); ?>
            </div>

            <?php if (!empty($certification['updated_at'])): ?>

                <div class="certification-row">
                    <strong>Last Updated:</strong>

                    <?= htmlspecialchars(
                        (string) $certification['updated_at']
                    ); ?>
                </div>

            <?php endif; ?>

        </div>

        <?php if ($terminal): ?>

            <section class="certification-terminal">

                <h3>Certification Revoked</h3>

                <p>
                    This certification has been revoked.
                </p>

                <p>
                    Revocation is final. This credential cannot be
                    restored through certification administration.
                </p>

            </section>

        <?php elseif (!empty($statusOptions)): ?>

            <form
                class="certification-form"
                action="<?= URLROOT; ?>/developers/update_certification/<?= (int) ($certification['id'] ?? 0); ?>"
                method="post"
            >

                <div class="certification-field">

                    <label for="status">
                        Change Certification Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >
                        <option value="">
                            Select new status
                        </option>

                        <?php foreach ($statusOptions as $value => $label): ?>

                            <option
                                value="<?= htmlspecialchars($value); ?>"
                            >
                                <?= htmlspecialchars($label); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button type="submit">
                    Update Certification
                </button>

            </form>

        <?php else: ?>

            <section class="certification-terminal">

                <p>
                    No administrative transition is available from the
                    current certification state.
                </p>

            </section>

        <?php endif; ?>

    </div>

<?php else: ?>

    <h1>Developer Certification Admin</h1>

    <p>
        Manage issued Chaos developer certifications and their lifecycle.
    </p>

    <?php
    $filters = [
        'all' => 'All',
        'pending' => 'Pending',
        'active' => 'Active',
        'suspended' => 'Suspended',
        'revoked' => 'Revoked',
        'expired' => 'Expired',
    ];
    ?>

    <nav class="developers-admin-filters">

        <?php foreach ($filters as $value => $label): ?>

            <a
                href="<?= URLROOT; ?>/developers/admin/<?= htmlspecialchars($value); ?>"
                class="<?= $filter === $value ? 'active' : ''; ?>"
            >
                <?= htmlspecialchars($label); ?>
                (<?= (int) ($counts[$value] ?? 0); ?>)
            </a>

        <?php endforeach; ?>

    </nav>

    <?php if (!empty($certifications)): ?>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Developer</th>
                    <th>Certification</th>
                    <th>Credential</th>
                    <th>Status</th>
                    <th>Awarded</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($certifications as $item): ?>

                <?php
                $type = (string) ($item['certification_type'] ?? '');

                $labels = [
                    'theme' => 'Chaos Theme Developer',
                    'module' => 'Certified Module Developer',
                    'developer' => 'Chaos Certified Developer',
                ];
                ?>

                <tr>

                    <td>
                        <?= (int) $item['id']; ?>
                    </td>

                    <td>
                        <a
                            href="<?= URLROOT; ?>/developers/profile/<?= (int) $item['developer_id']; ?>"
                        >
                            <?= htmlspecialchars(
                                (string) $item['public_name']
                            ); ?>
                        </a>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $labels[$type]
                            ?? ucfirst($type)
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            (string) $item['credential_id']
                        ); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            ucfirst(
                                (string) $item['status']
                            )
                        ); ?>
                    </td>

                    <td>
                        <?= !empty($item['awarded_at'])
                            ? htmlspecialchars(
                                (string) $item['awarded_at']
                            )
                            : '—'; ?>
                    </td>

                    <td>
                        <a
                            href="<?= URLROOT; ?>/developers/certification/<?= (int) $item['id']; ?>"
                        >
                            <?= $item['status'] === 'revoked'
                                ? 'View'
                                : 'Manage'; ?>
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    <?php else: ?>

        <div class="certification-empty">
            No certifications match this queue.
        </div>

    <?php endif; ?>

<?php endif; ?>

</div>

<!-- [End AI:GPT-5.6 Sol] -->

<?php require APPROOT . '/views/inc/foot.php'; ?>