<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/customer-auth.php'; // Auth guard for search as requested
require_once 'includes/services/SearchService.php';
require_once 'includes/services/NeighborhoodService.php';
require_once 'includes/services/CustomerService.php';
require_once 'includes/services/ReviewService.php';

$q               = sanitize($_GET['q'] ?? '');
$neighborhood_id = (int)($_GET['neighborhood'] ?? 0);
$status          = sanitize($_GET['status'] ?? '');
$sort            = sanitize($_GET['sort'] ?? 'updated');
$page            = max(1, (int)($_GET['page'] ?? 1));

$results = SearchService::search($q, $neighborhood_id, $status, $sort, $page);
$rows    = $results['rows'];
$pag     = $results['pagination'];
$neighborhoods = NeighborhoodService::getAll();

$page_title = 'Search Results - MedFinder Ethiopia';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Search results</p>
                <h1 class="page-title">
                    <?= $q ? '"' . h($q) . '"' : 'All medicines' ?>
                </h1>
                <p class="page-subtitle">Found <?= (int)$pag['total'] ?> pharmacies with matching stock.</p>
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <span>Search results</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">New search</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container page-layout">
            <!-- Filter sidebar -->
            <aside class="sidebar">
                <h3 class="panel-title">Filter results</h3>
                <form class="filter-form" action="search-results.php" method="get">
                    <div class="form-group">
                        <label for="filter-medicine">Medicine</label>
                        <input type="text" id="filter-medicine" name="q"
                               value="<?= h($q) ?>" placeholder="Insulin" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="filter-neighborhood">Neighborhood</label>
                        <select id="filter-neighborhood" name="neighborhood">
                            <option value="">All neighborhoods</option>
                            <?php foreach ($neighborhoods as $nbhd): ?>
                                <option value="<?= h($nbhd['neighborhood_id']) ?>"
                                    <?= $neighborhood_id === (int)$nbhd['neighborhood_id'] ? 'selected' : '' ?>>
                                    <?= h($nbhd['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock status</label>
                        <div class="radio-list">
                            <label><input type="radio" name="status" value=""          <?= $status === ''            ? 'checked' : '' ?>> Any</label>
                            <label><input type="radio" name="status" value="in_stock"  <?= $status === 'in_stock'    ? 'checked' : '' ?>> In stock</label>
                            <label><input type="radio" name="status" value="limited"   <?= $status === 'limited'     ? 'checked' : '' ?>> Limited</label>
                            <label><input type="radio" name="status" value="out_of_stock" <?= $status === 'out_of_stock' ? 'checked' : '' ?>> Out of stock</label>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-primary" type="submit">Apply filters</button>
                        <a class="btn btn-link" href="search-results.php<?= $q ? '?q=' . urlencode($q) : '' ?>">Reset filters</a>
                    </div>
                </form>
            </aside>

            <!-- Results -->
            <div class="content-area">
                <div class="results-toolbar">
                    <div class="search-inline">
                        <div class="form-group">
                            <label for="sort-results">Sort by</label>
                            <select id="sort-results" onchange="
                                const u = new URL(window.location);
                                u.searchParams.set('sort', this.value);
                                window.location = u.toString();">
                                <option value="updated"    <?= $sort === 'updated'    ? 'selected' : '' ?>>Recently updated</option>
                                <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Lowest price</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Highest price</option>
                            </select>
                        </div>
                    </div>
                    <span class="pill pill-success"><?= count($rows) ?> pharmacies shown</span>
                </div>

                <?php if (empty($rows)): ?>
                    <div class="card empty-state">
                        <h3>No pharmacies found</h3>
                        <p>Try adjusting filters or searching by a different brand name.</p>
                        <a class="btn btn-secondary" href="index.php">Start a new search</a>
                    </div>
                <?php else: ?>
                    <div class="result-list">
                        <?php foreach ($rows as $row): ?>
                            <?php 
                            $is_favorited = CustomerService::isFavorited($_SESSION['customer_id'], $row['pharmacy_id']);
                            $avg_rating = ReviewService::getAverageRating($row['pharmacy_id']);
                            $rev_count = ReviewService::getReviewCount($row['pharmacy_id']);
                            ?>
                            <article class="card result-card">
                                <div class="card-header">
                                    <div class="title-with-rating">
                                        <h3><?= h($row['pharmacy_name']) ?></h3>
                                        <?php if ($avg_rating > 0): ?>
                                            <div class="rating-mini" style="font-size: 12px; color: #f39c12;">
                                                <?= str_repeat('★', floor($avg_rating)) ?> (<?= $avg_rating ?>)
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="header-tools">
                                        <button class="favorite-btn" 
                                                data-pharmacy-id="<?= h($row['pharmacy_id']) ?>"
                                                data-is-favorited="<?= $is_favorited ? '1' : '0' ?>"
                                                title="<?= $is_favorited ? 'Remove from favorites' : 'Add to favorites' ?>">
                                            <span class="heart-icon"><?= $is_favorited ? '❤️' : '🤍' ?></span>
                                        </button>
                                        <span class="badge badge-<?= $row['stock_status'] === 'in_stock' ? 'success' : ($row['stock_status'] === 'limited' ? 'warning' : 'danger') ?>">
                                            <?= str_replace('_', ' ', ucfirst($row['stock_status'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <p class="card-meta"><?= h($row['neighborhood_name']) ?>, <?= h($row['address']) ?></p>
                                <div class="card-details">
                                    <span>Price: <?= number_format($row['price'], 2) ?> ETB</span>
                                    <span>Updated <?= time_ago($row['updated_at']) ?></span>
                                </div>
                                <div class="card-actions">
                                    <a class="btn btn-secondary" href="pharmacy-detail.php?id=<?= h($row['pharmacy_id']) ?>">View details</a>
                                    <?php if ($row['phone']): ?>
                                        <a class="btn btn-link" href="tel:<?= h($row['phone']) ?>">Call</a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pag['total'] > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $pag['total']; $i++): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                   class="page-link <?= $i === $pag['current'] ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
