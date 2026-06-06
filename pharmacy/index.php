<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/pharmacy-auth.php';
require_once '../includes/services/PharmacyService.php';
require_once '../includes/services/InventoryService.php';

$id = $_SESSION['pharmacy_id'];
$pharmacy = $current_pharmacy; // From pharmacy-auth.php
$inventory = InventoryService::getByPharmacy($id);

// Simple stats
$total_medicines = count($inventory);
$low_stock = 0;
foreach ($inventory as $inv) {
    if ($inv['status'] === 'limited') $low_stock++;
}

$asset_path = '../';
$page_title = 'Pharmacy Dashboard - MedFinder Ethiopia';
$body_class = 'pharmacy-body';
$extra_css = ['css/pharmacy.css'];
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy dashboard</p>
                <h1 class="page-title">Welcome back, <?= h($pharmacy['pharmacy_name']) ?></h1>
                <p class="page-subtitle">Update inventory, track stock, and reach more patients.</p>
                <div class="breadcrumb">
                    <a href="index.php">Pharmacy</a>
                    <span>/</span>
                    <span>Dashboard</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="inventory/add.php">Add medicine</a>
                <a class="btn btn-secondary" href="profile.php">Edit profile</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container stat-grid">
            <div class="stat-card reveal">
                <h3><?= $total_medicines ?></h3>
                <p>Medicines in stock</p>
            </div>
            <div class="stat-card reveal delay-1">
                <h3><?= $low_stock ?></h3>
                <p>Low stock alerts</p>
            </div>
            <div class="stat-card reveal delay-2">
                <h3>120</h3> <!-- Static for now as search_logs not fully integrated for dashboard -->
                <p>Search views this week</p>
            </div>
            <div class="stat-card reveal delay-3">
                <h3>4.8</h3> <!-- Static for now -->
                <p>Average rating</p>
            </div>
        </div>
    </section>

    <section class="section section-accent">
        <div class="container page-layout">
            <div class="content-area">
                <div class="panel">
                    <h3 class="panel-title">Recent inventory updates</h3>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inventory)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No medicines added yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $recent = array_slice($inventory, 0, 5);
                                    foreach ($recent as $inv): 
                                    ?>
                                        <tr>
                                            <td><?= h($inv['medicine_name']) ?></td>
                                            <td><span class="badge <?= badge_class($inv['status']) ?>"><?= status_label($inv['status']) ?></span></td>
                                            <td><?= format_price($inv['price']) ?></td>
                                            <td><?= time_ago($inv['updated_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="sidebar">
                <h3 class="panel-title">Quick actions</h3>
                <div class="form-footer" style="flex-direction: column; gap: 10px; display: flex;">
                    <a class="btn btn-primary btn-block" href="inventory/index.php">Manage inventory</a>
                    <a class="btn btn-secondary btn-block" href="profile.php">Update profile</a>
                    <a class="btn btn-secondary btn-block" href="../logout.php">Log out</a>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
