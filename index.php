<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/AdminService.php';
require_once 'includes/services/NeighborhoodService.php';

$stats = AdminService::getDashboardStats();
$neighborhoods = NeighborhoodService::getAll();

$page_title = 'MedFinder Ethiopia - Find medicine fast';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="hero">
        <div class="container hero-inner">
            <div class="hero-content reveal">
                <p class="eyebrow">Medicine search made simple</p>
                <h1>Find real-time medicine availability across your neighborhood.</h1>
                <p class="lead">Search by medicine, compare stock status, and call verified pharmacies in minutes.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="search-results.php">Start search</a>
                    <a class="btn btn-secondary" href="pharmacy/register.php">List your pharmacy</a>
                </div>
                <div class="hero-highlights">
                    <div class="highlight-card">
                        <p class="highlight-number"><?= h($stats['pharmacy_count']) ?>+</p>
                        <p class="highlight-label">Partner pharmacies</p>
                    </div>
                    <div class="highlight-card">
                        <p class="highlight-number"><?= h($stats['total_medicines']) ?>+</p>
                        <p class="highlight-label">Medicines tracked</p>
                    </div>
                    <div class="highlight-card">
                        <p class="highlight-number"><?= count($neighborhoods) ?></p>
                        <p class="highlight-label">Neighborhoods covered</p>
                    </div>
                </div>
            </div>

            <div class="hero-search reveal delay-1">
                <form class="search-form" action="search-results.php" method="get">
                    <div class="form-group">
                        <label for="search-medicine">Medicine name</label>
                        <input type="text" id="search-medicine" name="q"
                               placeholder="Search Insulin, Amoxicillin, Paracetamol" required
                               autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="search-neighborhood">Neighborhood</label>
                        <select id="search-neighborhood" name="neighborhood">
                            <option value="">All neighborhoods</option>
                            <?php foreach ($neighborhoods as $nbhd): ?>
                                <option value="<?= h($nbhd['neighborhood_id']) ?>">
                                    <?= h($nbhd['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="search-status">Stock status</label>
                        <select id="search-status" name="status">
                            <option value="">Any status</option>
                            <option value="in_stock">In stock</option>
                            <option value="limited">Limited</option>
                            <option value="out_of_stock">Out of stock</option>
                        </select>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Search pharmacies</button>
                    <p class="form-note">Use the results page to filter by neighborhood, status, and nearby location.</p>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="eyebrow">Featured pharmacies</p>
                    <h2>Verified local partners</h2>
                </div>
                <a class="link" href="search-results.php">View all pharmacies</a>
            </div>
            <div class="card-grid">
                <article class="card pharmacy-card reveal">
                    <div class="card-header">
                        <h3>Unity Pharmacy</h3>
                        <span class="badge badge-success">In stock</span>
                    </div>
                    <p class="card-meta">Bole, Atlas Area</p>
                    <div class="card-details">
                        <span>24 medicines listed</span>
                        <span>Open until 9:00 PM</span>
                    </div>
                    <div class="card-actions">
                        <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                        <a class="btn btn-link" href="tel:+251912345678">Call</a>
                    </div>
                </article>

                <article class="card pharmacy-card reveal delay-1">
                    <div class="card-header">
                        <h3>EthioCare Pharmacy</h3>
                        <span class="badge badge-warning">Limited</span>
                    </div>
                    <p class="card-meta">Kirkos, Meskel Square</p>
                    <div class="card-details">
                        <span>19 medicines listed</span>
                        <span>Open until 8:00 PM</span>
                    </div>
                    <div class="card-actions">
                        <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                        <a class="btn btn-link" href="tel:+251911223344">Call</a>
                    </div>
                </article>

                <article class="card pharmacy-card reveal delay-2">
                    <div class="card-header">
                        <h3>BlueCross Pharmacy</h3>
                        <span class="badge badge-danger">Out of stock</span>
                    </div>
                    <p class="card-meta">Yeka, Megenagna</p>
                    <div class="card-details">
                        <span>31 medicines listed</span>
                        <span>Opens at 8:00 AM</span>
                    </div>
                    <div class="card-actions">
                        <a class="btn btn-secondary" href="pharmacy-detail.php">View details</a>
                        <a class="btn btn-link" href="tel:+251900112233">Call</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section section-accent">
        <div class="container steps-grid">
            <div class="steps-header">
                <p class="eyebrow">How it works</p>
                <h2>Search, compare, and connect.</h2>
                <p class="lead">Every result is tied to real pharmacy inventory updates.</p>
            </div>
            <div class="steps-list">
                <div class="step-card reveal">
                    <span class="step-number">1</span>
                    <h3>Search a medicine</h3>
                    <p>Type a brand or generic name and choose your neighborhood.</p>
                </div>
                <div class="step-card reveal delay-1">
                    <span class="step-number">2</span>
                    <h3>Compare nearby pharmacies</h3>
                    <p>Sort by proximity, stock, and recent updates to find the best option.</p>
                </div>
                <div class="step-card reveal delay-2">
                    <span class="step-number">3</span>
                    <h3>Call and confirm</h3>
                    <p>Use the contact number to verify stock before you travel.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
