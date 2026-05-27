<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/services/AdminService.php';
require_once '../includes/admin-auth.php';

$stats = AdminService::getDashboardStats();
$pending = AdminService::getPharmacies('pending', '', 1)['rows'];

$page_title = 'Admin Dashboard - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Admin dashboard</p>
                <h1 class="page-title">System overview</h1>
                <p class="page-subtitle">Monitor pharmacy onboarding, medicine catalog, and search activity.</p>
                <div class="breadcrumb">
                    <a href="index.php">Admin</a>
                    <span>/</span>
                    <span>Dashboard</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="medicines/add.php">Add medicine</a>
                <a class="btn btn-secondary" href="pharmacies/index.php">Manage pharmacies</a>
            </div>
        </div>
    </section>

    <div class="container">
        <?php render_flashes(); ?>
    </div>

    <section class="section">
        <div class="container stat-grid">
            <div class="stat-card reveal">
                <h3><?= h($stats['total_pharmacies']) ?></h3>
                <p>Total pharmacies</p>
            </div>
            <div class="stat-card reveal delay-1">
                <h3><?= h($stats['pending']) ?></h3>
                <p>Pending approvals</p>
            </div>
            <div class="stat-card reveal delay-2">
                <h3><?= h($stats['total_medicines']) ?></h3>
                <p>Medicines in catalog</p>
            </div>
            <div class="stat-card reveal delay-3">
                <h3><?= h($stats['searches_today']) ?></h3>
                <p>Searches today</p>
            </div>
        </div>
    </section>

    <section class="section section-accent">
        <div class="container page-layout">
            <div class="content-area">
                <div class="panel">
                    <h3 class="panel-title">Pending pharmacy approvals</h3>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pharmacy</th>
                                    <th>Owner</th>
                                    <th>Neighborhood</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pending)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;padding:2rem">No pending approvals.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pending as $ph): ?>
                                        <tr>
                                            <td><?= h($ph['pharmacy_name']) ?></td>
                                            <td><?= h($ph['owner_name']) ?></td>
                                            <td><?= h($ph['neighborhood_name'] ?? '—') ?></td>
                                            <td><span class="pill pill-warning">Pending</span></td>
                                            <td class="table-actions">
                                                <a class="btn btn-secondary" href="pharmacies/approve.php?id=<?= (int)$ph['pharmacy_id'] ?>">Review</a>
                                                <a class="btn btn-link" href="pharmacies/view.php?id=<?= (int)$ph['pharmacy_id'] ?>">Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="sidebar">
                <h3 class="panel-title">Quick links</h3>
                <div class="info-list">
                    <div class="info-row">
                        <span>Active pharmacies</span>
                        <span><?= h($stats['active']) ?></span>
                    </div>
                    <div class="info-row">
                        <span>Suspended</span>
                        <span><?= h($stats['suspended']) ?></span>
                    </div>
                    <div class="info-row">
                        <span>Medicines in catalog</span>
                        <span><?= h($stats['total_medicines']) ?></span>
                    </div>
                </div>
                <div class="form-footer">
                    <a class="btn btn-primary" href="medicines/index.php">Review catalog</a>
                    <a class="btn btn-secondary" href="neighborhoods/index.php">Manage neighborhoods</a>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include '../includes/footer.php'; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>Copyright 2026 MedFinder Ethiopia</span>
            <span>Built for local pharmacies.</span>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
