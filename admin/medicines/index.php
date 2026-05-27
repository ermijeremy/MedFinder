<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/MedicineService.php';
require_once '../../includes/admin-auth.php';

$search   = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));

$result = MedicineService::getAll($search, $category, $page);
$rows   = $result['rows'];
$pag    = $result['pagination'];

// Get unique categories for filter
$categories = db_query('SELECT DISTINCT category FROM medicines WHERE category IS NOT NULL ORDER BY category ASC')->fetchAll(PDO::FETCH_COLUMN);

$page_title = 'Medicine Catalog - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Medicine catalog</p>
                <h1 class="page-title">Manage medicines</h1>
                <p class="page-subtitle">Add, edit, or archive medicines listed across partner pharmacies.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <span>Medicines</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="add.php">Add new medicine</a>
                <a class="btn btn-secondary" href="../index.php">Back to dashboard</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>
            <div class="results-toolbar">
                <form action="index.php" method="get" class="search-inline">
                    <div class="form-group">
                        <label for="medicine-search">Search medicines</label>
                        <input type="text" id="medicine-search" name="search" placeholder="Search by name or generic" value="<?= h($search) ?>">
                    </div>
                    <div class="form-group">
                        <label for="medicine-category">Category</label>
                        <select id="medicine-category" name="category">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="index.php" class="btn btn-link">Reset</a>
                </form>
                <span class="pill"><?= (int)$pag['total'] ?> items</span>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Generic name</th>
                            <th>Category</th>
                            <th>Pharmacies</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:2rem">No medicines found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $m): ?>
                                <tr>
                                    <td><?= h($m['medicine_name']) ?></td>
                                    <td><?= h($m['generic_name'] ?? '—') ?></td>
                                    <td><?= h($m['category'] ?? 'Uncategorized') ?></td>
                                    <td><?= (int)$m['pharmacy_count'] ?></td>
                                    <td class="table-actions">
                                        <a class="btn btn-secondary" href="edit.php?id=<?= (int)$m['medicine_id'] ?>">Edit</a>
                                        <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Archive this medicine?')">
                                            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= (int)$m['medicine_id'] ?>">
                                            <button class="btn btn-link" type="submit">Archive</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pag['total_pages'] > 1): ?>
                <nav class="pagination" style="margin-top:2rem">
                    <?php for ($i = 1; $i <= $pag['total_pages']; $i++): ?>
                        <a class="btn <?= $i === $pag['current'] ? 'btn-primary' : 'btn-secondary' ?>" 
                           href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
