<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/PharmacyService.php';
require_once 'includes/services/InventoryService.php';

$id       = (int)($_GET['id'] ?? 0);
$pharmacy = PharmacyService::getById($id);

if (!$pharmacy || $pharmacy['status'] !== 'active') {
    flash('error', 'Pharmacy not found or not yet approved.');
    redirect('index.php');
}

$inventory = InventoryService::getByPharmacy($id);

$page_title = h($pharmacy['pharmacy_name']) . ' - MedFinder';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy profile</p>
                <h1 class="page-title"><?= h($pharmacy['pharmacy_name']) ?></h1>
                <p class="page-subtitle">
                    <?= h($pharmacy['neighborhood_name'] ?? '') ?> — <?= h($pharmacy['address']) ?>
                    | License: <?= h($pharmacy['license_number']) ?>
                </p>
                <div class="status-row">
                    <?php
                        // Determine the best stock status across all inventory
                        $hasIn = false; $hasLim = false;
                        foreach ($inventory as $inv) {
                            if ($inv['status'] === 'in_stock') { $hasIn = true; break; }
                            if ($inv['status'] === 'limited')  { $hasLim = true; }
                        }
                        $overallStatus = $hasIn ? 'in_stock' : ($hasLim ? 'limited' : 'out_of_stock');
                    ?>
                    <span class="badge badge-success">Active</span>
                    <span class="pill <?= $overallStatus === 'in_stock' ? 'pill-success' : '' ?>">
                        <?= status_label($overallStatus) ?>
                    </span>
                    <?php if (!empty($inventory)): ?>
                        <span class="pill">Updated <?= time_ago($inventory[0]['updated_at']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="tel:<?= h($pharmacy['phone']) ?>">Call pharmacy</a>
                <a class="btn btn-secondary" href="search-results.php">Back to results</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container profile-grid">
            <div class="profile-main">
                <article class="panel">
                    <h3 class="panel-title">Overview</h3>
                    <p><?= h($pharmacy['pharmacy_name']) ?> serves the <?= h($pharmacy['neighborhood_name'] ?? 'local') ?>
                       area. Verified by MedFinder with regularly updated inventory.</p>
                </article>

                <article class="panel">
                    <h3 class="panel-title">Available medicines
                        <span class="pill"><?= count($inventory) ?></span>
                    </h3>
                    <?php if (empty($inventory)): ?>
                        <p class="text-muted">No medicines listed yet.</p>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inventory as $inv): ?>
                                        <tr>
                                            <td>
                                                <?= h($inv['medicine_name']) ?>
                                                <?php if ($inv['generic_name']): ?>
                                                    <small class="text-muted"><?= h($inv['generic_name']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $inv['price'] > 0 ? format_price((float)$inv['price']) : '—' ?></td>
                                            <td><span class="badge <?= badge_class($inv['status']) ?>">
                                                <?= status_label($inv['status']) ?>
                                            </span></td>
                                            <td><?= time_ago($inv['updated_at']) ?></td>
                                        </tr>
                                        <?php if ($inv['restock_note']): ?>
                                            <tr>
                                                <td colspan="4" class="text-muted" style="font-size:.85em;padding-top:0">
                                                    ℹ <?= h($inv['restock_note']) ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </article>

                <?php if ($pharmacy['operating_hours']): ?>
                <article class="panel">
                    <h3 class="panel-title">Operating hours</h3>
                    <div class="info-list">
                        <div class="info-row">
                            <span>Hours</span>
                            <span><?= h($pharmacy['operating_hours']) ?></span>
                        </div>
                    </div>
                </article>
                <?php endif; ?>
            </div>

            <aside class="profile-sidebar">
                <div class="panel">
                    <h3 class="panel-title">Contact details</h3>
                    <div class="info-list">
                        <div class="info-row">
                            <span>Phone</span>
                            <a href="tel:<?= h($pharmacy['phone']) ?>"><?= h($pharmacy['phone']) ?></a>
                        </div>
                        <div class="info-row">
                            <span>Email</span>
                            <span><?= h($pharmacy['email']) ?></span>
                        </div>
                        <div class="info-row">
                            <span>Neighborhood</span>
                            <span><?= h($pharmacy['neighborhood_name'] ?? '—') ?></span>
                        </div>
                        <div class="info-row">
                            <span>Owner</span>
                            <span><?= h($pharmacy['owner_name']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <h3 class="panel-title">Location map</h3>
                    <div class="empty-state">
                        <p>Map preview coming soon.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
