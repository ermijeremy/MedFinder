<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/PharmacyService.php';
require_once 'includes/services/InventoryService.php';
require_once 'includes/services/CustomerService.php';
require_once 'includes/services/ReviewService.php';

$id       = (int)($_GET['id'] ?? 0);
$pharmacy = PharmacyService::getById($id);

if (!$pharmacy || $pharmacy['status'] !== 'active') {
    flash('error', 'Pharmacy not found or not yet approved.');
    redirect('index.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    if (!isset($_SESSION['customer_id'])) {
        flash('error', 'Please log in to leave a review.');
    } else {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = sanitize($_POST['comment'] ?? '');
        
        if ($rating < 1 || $rating > 5) {
            $review_error = 'Please select a rating between 1 and 5.';
        } else {
            if (ReviewService::addReview($id, $_SESSION['customer_id'], $rating, $comment)) {
                flash('success', 'Thank you for your review!');
                redirect("pharmacy-detail.php?id=$id");
            } else {
                $review_error = 'Failed to submit review. Try again.';
            }
        }
    }
}

$inventory = InventoryService::getByPharmacy($id);
$reviews = ReviewService::getPharmacyReviews($id);
$avg_rating = ReviewService::getAverageRating($id);

$is_favorited = false;
if (isset($_SESSION['customer_id'])) {
    $is_favorited = CustomerService::isFavorited($_SESSION['customer_id'], $id);
}

$page_title = h($pharmacy['pharmacy_name']) . ' - MedFinder';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy profile</p>
                <div class="title-with-action">
                    <?php if (!empty($pharmacy['logo'])): ?>
                        <img src="<?= base_url() . 'uploads/pharmacy-logos/' . h($pharmacy['logo']) ?>" alt="<?= h($pharmacy['pharmacy_name']) ?> Logo" style="max-height: 60px; margin-right: 15px; border-radius: 8px; object-fit: contain;">
                    <?php endif; ?>
                    <h1 class="page-title"><?= h($pharmacy['pharmacy_name']) ?></h1>
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <button class="favorite-btn" 
                                data-pharmacy-id="<?= h($id) ?>"
                                data-is-favorited="<?= $is_favorited ? '1' : '0' ?>"
                                title="<?= $is_favorited ? 'Remove from favorites' : 'Add to favorites' ?>">
                            <span class="heart-icon"><?= $is_favorited ? '❤️' : '🤍' ?></span>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="rating-display" style="margin-bottom: 8px;">
                    <span style="color: #f39c12; font-size: 20px;">
                        <?= str_repeat('★', floor($avg_rating)) . str_repeat('☆', 5 - floor($avg_rating)) ?>
                    </span>
                    <span style="font-weight: 600;"><?= $avg_rating ?></span>
                    <span class="text-muted">(<?= count($reviews) ?> reviews)</span>
                </div>
                <p class="page-subtitle">
                    <?= h($pharmacy['neighborhood_name'] ?? '') ?> — <?= h($pharmacy['address']) ?>
                    | License: <?= h($pharmacy['license_number']) ?>
                </p>
                <div class="status-row">
                    <?php
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

                <article class="panel" id="reviews-section">
                    <h3 class="panel-title">Reviews & Feedback</h3>
                    
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <div class="card" style="margin-bottom: 24px; border: 1px dashed #ccc;">
                            <h4>Leave a Review</h4>
                            <form action="pharmacy-detail.php?id=<?= $id ?>#reviews-section" method="post" class="form">
                                <input type="hidden" name="action" value="add_review">
                                <div class="form-group">
                                    <label>Rating</label>
                                    <select name="rating" required>
                                        <option value="5">5 - Excellent</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="3">3 - Good</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="1">1 - Poor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Comment</label>
                                    <textarea id="comment" name="comment" placeholder="Share your experience..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Please <a href="customer/login.php">log in</a> to leave a review.
                        </div>
                    <?php endif; ?>

                    <div class="review-list">
                        <?php if (empty($reviews)): ?>
                            <p class="text-muted">No reviews yet. Be the first to leave one!</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="review-card" style="border-bottom: 1px solid #eee; padding: 16px 0;">
                                    <div class="card-header" style="justify-content: flex-start; gap: 12px; margin-bottom: 4px;">
                                        <span style="color: #f39c12;"><?= str_repeat('★', $rev['rating']) ?></span>
                                        <strong><?= h($rev['first_name'] . ' ' . $rev['last_name']) ?></strong>
                                    </div>
                                    <p style="font-size: 14px;"><?= h($rev['comment']) ?></p>
                                    <small class="text-muted"><?= time_ago($rev['created_at']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
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
