<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

$favorites = CustomerService::getFavorites($_SESSION['customer_id']);

$asset_path = '../';
$page_title = 'My Favorites - MedFinder';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container">
            <h1>My Favorite Pharmacies</h1>
            <p>Your saved pharmacies for quick access.</p>
        </div>
    </section>

    <div class="container section">
        <div class="content-area">
            <?php if (empty($favorites)): ?>
                <div class="card empty-state">
                    <h3>No favorites yet</h3>
                    <p>Explore pharmacies and add them to your favorites for quick access.</p>
                    <a class="btn btn-primary" href="../search-results.php">Explore Pharmacies</a>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($favorites as $pharmacy): ?>
                        <article class="card pharmacy-card">
                            <div class="card-header">
                                <h3><?= h($pharmacy['pharmacy_name']) ?></h3>
                                <button class="favorite-btn" 
                                        data-pharmacy-id="<?= h($pharmacy['pharmacy_id']) ?>"
                                        data-is-favorited="1"
                                        title="Remove from favorites">
                                    <span class="heart-icon">❤️</span>
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="card-meta"><?= h($pharmacy['address']) ?></p>
                                <p class="card-meta"><?= h($pharmacy['phone']) ?></p>
                            </div>
                            <div class="card-actions">
                                <a class="btn btn-secondary btn-sm" href="../pharmacy-detail.php?id=<?= h($pharmacy['pharmacy_id']) ?>">View Details</a>
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
</main>

<?php include '../includes/footer.php'; ?>
