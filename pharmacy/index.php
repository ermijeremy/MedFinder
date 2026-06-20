<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/pharmacy-auth.php';
require_once '../includes/services/PharmacyService.php';
require_once '../includes/services/InventoryService.php';
require_once '../includes/services/ReviewService.php';

$id = $_SESSION['pharmacy_id'];
$pharmacy = $current_pharmacy; // From pharmacy-auth.php
$inventory = InventoryService::getByPharmacy($id);
$reviews = ReviewService::getPharmacyReviews($id);
$avg_rating = ReviewService::getAverageRating($id);

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
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
                    <?php if (!empty($pharmacy['logo'])): ?>
                        <img src="<?= base_url() . 'uploads/pharmacy-logos/' . h($pharmacy['logo']) ?>" alt="Logo" style="max-height: 50px; border-radius: 8px; object-fit: contain;">
                    <?php endif; ?>
                    <h1 class="page-title" style="margin-bottom: 0;">Welcome back, <?= h($pharmacy['pharmacy_name']) ?></h1>
                </div>
                <p class="page-subtitle">Update inventory, track stock, and reach more patients.</p>
                <div class="breadcrumb">
                    <a href="index.php">Pharmacy</a>
                    <span>/</span>
                    <span>Dashboard</span>
                </div>
            </div>
            <div class="page-actions">
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
                <h3><?= $avg_rating > 0 ? number_format($avg_rating, 1) : 'No ratings' ?></h3>
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

                <div class="panel" style="margin-top: 2rem;">
                    <h3 class="panel-title">Customer Reviews</h3>
                    <div class="review-list">
                        <?php if (empty($reviews)): ?>
                            <p class="text-muted">No reviews yet.</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="review-card" style="border-bottom: 1px solid #eee; padding: 16px 0;">
                                    <div class="card-header" style="justify-content: flex-start; gap: 12px; margin-bottom: 4px;">
                                        <span style="color: #f39c12;"><?= str_repeat('★', $rev['rating']) ?></span>
                                        <strong><?= h($rev['first_name'] . ' ' . $rev['last_name']) ?></strong>
                                    </div>
                                    <p style="font-size: 14px; margin-top: 8px;"><?= h($rev['comment']) ?></p>
                                    <small class="text-muted" style="display: block; margin-top: 8px;"><?= time_ago($rev['created_at']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
