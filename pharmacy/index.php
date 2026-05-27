<?php
/**
 * pharmacy/index.php — Pharmacy Owner Dashboard
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/services/PharmacyService.php';
require_once '../includes/services/InventoryService.php';
require_once '../includes/pharmacy-auth.php';   // sets $current_pharmacy

$stats  = PharmacyService::getStats($current_pharmacy['pharmacy_id']);
$recent = InventoryService::getRecent($current_pharmacy['pharmacy_id']);

$page_title = 'Dashboard - ' . $current_pharmacy['pharmacy_name'];
$asset_path = '../';
$extra_css  = ['css/pharmacy.css'];
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy dashboard</p>
                <h1 class="page-title">Welcome back, <?= h($current_pharmacy['pharmacy_name']) ?></h1>
                <p class="page-subtitle">Update inventory, track stock, and reach more patients.</p>
                <div class="breadcrumb">
                    <a href="index.php">Pharmacy</a><span>/</span><span>Dashboard</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary"   href="inventory/add.php">Add medicine</a>
                <a class="btn btn-secondary" href="profile.php">Edit profile</a>
            </div>
        </div>
    </section>

    <?php render_flashes(); ?>

    <section class="section">
        <div class="container stat-grid">
            <div class="stat-card reveal">
                <h3><?= h($stats['total_meds']) ?></h3>
                <p>Medicines in inventory</p>
            </div>
            <div class="stat-card reveal delay-1">
                <h3><?= h($stats['low_stock']) ?></h3>
                <p>Low stock alerts</p>
            </div>
            <div class="stat-card reveal delay-2">
                <h3><?= h($stats['out_of_stock']) ?></h3>
                <p>Out of stock items</p>
            </div>
            <div class="stat-card reveal delay-3">
                <h3><?= h($stats['search_views']) ?>+</h3>
                <p>Platform searches (7 days)</p>
            </div>
        </div>
    </section>

    <section class="section section-accent">
        <div class="container page-layout">
            <div class="content-area">
                <div class="panel">
                    <h3 class="panel-title">Recent inventory updates</h3>
                    <?php if (empty($recent)): ?>
                        <p class="text-muted">No inventory yet. <a href="inventory/add.php">Add your first medicine.</a></p>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Status</th>
                                        <th>Quantity</th>
                                        <th>Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent as $item): ?>
                                        <tr>
                                            <td><?= h($item['medicine_name']) ?></td>
                                            <td><span class="badge <?= badge_class($item['status']) ?>">
                                                <?= status_label($item['status']) ?>
                                            </span></td>
                                            <td><?= (int)$item['quantity'] ?></td>
                                            <td><?= time_ago($item['updated_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="sidebar">
                <h3 class="panel-title">Quick actions</h3>
                <div class="form-footer">
                    <a class="btn btn-primary"   href="inventory/index.html">Manage inventory</a>
                    <a class="btn btn-secondary" href="profile.php">Update profile</a>
                    <a class="btn btn-secondary" href="logout.php">Log out</a>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
