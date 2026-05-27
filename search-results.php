<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/SearchService.php';
require_once 'includes/services/NeighborhoodService.php';

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
                <a class="btn btn-primary" href="pharmacy/register.php">List your pharmacy</a>
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
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-secondary" type="button" data-use-location>Use my location</button>
                        </div>
                    </div>
                    <span class="pill pill-success">3 pharmacies</span>
                </div>

                <div class="card" data-location-banner>
                    <h3 class="panel-title">Nearby recommendation</h3>
                    <p data-location-message>Allow location to rank pharmacies by proximity.</p>
                </div>

                <div class="result-list">
                    <article class="card result-card" data-name="Unity Pharmacy" data-neighborhood="bole" data-status="in_stock" data-lat="8.995" data-lng="38.785" data-price="250" data-updated-minutes="15">
                        <div class="card-header">
                            <h3>Unity Pharmacy</h3>
                            <span class="badge badge-success">In stock</span>
                        </div>
                        <p class="card-meta">Bole, Atlas Area</p>
                        <div class="card-details">
                            <span>Price range: 230 - 260 ETB</span>
                            <span>Updated 15 minutes ago</span>
                            <span>Open until 9:00 PM</span>
                        </div>
                        <div class="card-actions">
                            <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                            <a class="btn btn-link" href="tel:+251912345678">Call</a>
                        </div>
                    </article>

                    <article class="card result-card" data-name="EthioCare Pharmacy" data-neighborhood="kirkos" data-status="limited" data-lat="8.984" data-lng="38.764" data-price="255" data-updated-minutes="45">
                        <div class="card-header">
                            <h3>EthioCare Pharmacy</h3>
                            <span class="badge badge-warning">Limited</span>
                        </div>
                        <p class="card-meta">Kirkos, Meskel Square</p>
                        <div class="card-details">
                            <span>Price range: 240 - 275 ETB</span>
                            <span>Updated 45 minutes ago</span>
                            <span>Open until 8:00 PM</span>
                        </div>
                        <div class="card-actions">
                            <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                            <a class="btn btn-link" href="tel:+251911223344">Call</a>
                        </div>
                    </article>

                    <article class="card result-card" data-name="BlueCross Pharmacy" data-neighborhood="yeka" data-status="out_of_stock" data-lat="9.03" data-lng="38.81" data-price="0" data-updated-minutes="120">
                        <div class="card-header">
                            <h3>BlueCross Pharmacy</h3>
                            <span class="badge badge-danger">Out of stock</span>
                        </div>
                        <p class="card-meta">Yeka, Megenagna</p>
                        <div class="card-details">
                            <span>Expected restock: Tomorrow</span>
                            <span>Updated 2 hours ago</span>
                            <span>Open until 10:00 PM</span>
                        </div>
                        <div class="card-actions">
                            <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                            <a class="btn btn-link" href="tel:+251900112233">Call</a>
                        </div>
                    </article>
                </div>

                <div class="card empty-state" data-empty-state hidden>
                    <h3>No pharmacies found</h3>
                    <p>Try adjusting filters or searching by a different brand name.</p>
                    <a class="btn btn-secondary" href="index.php">Start a new search</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
