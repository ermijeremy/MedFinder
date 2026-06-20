<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';
require_once '../includes/services/SearchService.php';
require_once '../includes/services/NotificationService.php';

$customer = CustomerService::getCustomer($_SESSION['customer_id']);
$favorites = CustomerService::getFavorites($_SESSION['customer_id']);
$searches = SearchService::getRecentSearches($_SESSION['customer_id']);
$notifications = NotificationService::getUnread($_SESSION['customer_id']);

$asset_path = '../';
$page_title = 'Dashboard - MedFinder';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container">
            <h1>Welcome back, <?= h($customer['first_name']) ?></h1>
            <p>Manage your profile and favorite pharmacies.</p>
        </div>
    </section>

    <div class="container section">
        <div class="dashboard-grid">
            <!-- Profile Card Section -->
            <aside class="sidebar">
                <div class="card">
                    <h3 class="panel-title">Your Profile</h3>
                    <?php if (!empty($customer['photo'])): ?>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <img src="<?= base_url() . 'uploads/customer-photos/' . h($customer['photo']) ?>" alt="Profile Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?= h($customer['first_name'] . ' ' . $customer['last_name']) ?></p>
                        <p><strong>Email:</strong> <?= h($customer['email']) ?></p>
                        <p><strong>Phone:</strong> <?= h($customer['phone'] ?: 'Not provided') ?></p>
                        <p><strong>Member since:</strong> <?= date('M d, Y', strtotime($customer['created_at'])) ?></p>
                    </div>
                    <div class="card-actions">
                        <a class="btn btn-secondary btn-sm" href="profile.php">Edit Profile</a>
                    </div>
                </div>
            </aside>

            <!-- Favorites Section -->
            <div class="content-area">
                <!-- Notifications Section -->
                <?php if (!empty($notifications)): ?>
                    <div class="panel" style="margin-bottom: 24px; border-left: 4px solid #2a9d8f;">
                        <h3 class="panel-title">Notifications (<?= count($notifications) ?>)</h3>
                        <div class="notification-list">
                            <?php foreach ($notifications as $n): ?>
                                <div class="alert alert-info" style="margin-bottom: 10px;">
                                    <strong><?= h($n['title']) ?></strong>: <?= h($n['message']) ?>
                                    <br><small class="text-muted"><?= time_ago($n['created_at']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Search History Section -->
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header">
                        <h2 class="panel-title">Recent Searches</h2>
                        <?php if (!empty($searches)): ?>
                            <a href="clear-history.php" class="btn btn-link btn-sm" onclick="return confirm('Clear your search history?')">Clear All</a>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($searches)): ?>
                        <p>No recent searches.</p>
                    <?php else: ?>
                        <ul class="info-list">
                            <?php foreach ($searches as $s): ?>
                                <li class="info-row" style="border-bottom: 1px solid #eee; padding: 8px 0;">
                                    <a href="../search-results.php?q=<?= urlencode($s['search_query']) ?>&neighborhood=<?= $s['neighborhood_id'] ?>&status=<?= $s['status_filter'] ?>">
                                        "<?= h($s['search_query']) ?>" in <?= $s['neighborhood_id'] ? 'Selected Neighborhood' : 'All Areas' ?>
                                    </a>
                                    <span class="text-muted" style="font-size: 12px;"><?= time_ago($s['search_date']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2 class="panel-title">My Favorite Pharmacies (<?= count($favorites) ?>)</h2>
                    <?php if (empty($favorites)): ?>
                        <div class="empty-state">
                            <p>You haven't added any favorites yet.</p>
                            <a class="btn btn-primary" href="../search-results.php">Explore Pharmacies</a>
                        </div>
                    <?php else: ?>
                        <div class="card-grid">
                            <?php foreach ($favorites as $pharmacy): ?>
                                <article class="card pharmacy-card">
                                    <div class="card-header">
                                        <h3><?= h($pharmacy['pharmacy_name']) ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <p><?= h($pharmacy['address']) ?></p>
                                        <p><?= h($pharmacy['phone']) ?></p>
                                    </div>
                                    <div class="card-actions">
                                        <a class="btn btn-secondary btn-sm" href="../pharmacy-detail.php?id=<?= h($pharmacy['pharmacy_id']) ?>">View</a>
                                        <button class="btn btn-danger btn-sm remove-favorite" data-pharmacy-id="<?= h($pharmacy['pharmacy_id']) ?>">
                                            Remove
                                        </button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
