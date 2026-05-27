<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/NeighborhoodService.php';
require_once '../../includes/admin-auth.php';

$neighborhoods = NeighborhoodService::getWithCounts();

$page_title = 'Neighborhoods - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Neighborhood management</p>
                <h1 class="page-title">Neighborhoods</h1>
                <p class="page-subtitle">Manage the areas used in search filters and pharmacy locations.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <span>Neighborhoods</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="add.php">Add neighborhood</a>
                <a class="btn btn-secondary" href="../index.php">Back to dashboard</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>
            <div class="results-toolbar">
                <div class="search-inline">
                    <div class="form-group">
                        <label for="neighborhood-search">Search neighborhoods</label>
                        <input type="text" id="neighborhood-search" placeholder="Search by name">
                    </div>
                </div>
                <span class="pill"><?= count($neighborhoods) ?> neighborhoods</span>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Zone</th>
                            <th>Pharmacies</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($neighborhoods)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;padding:2rem">No neighborhoods found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($neighborhoods as $n): ?>
                                <tr>
                                    <td><?= h($n['name']) ?></td>
                                    <td><?= h($n['zone'] ?? '—') ?></td>
                                    <td><?= (int)$n['pharmacy_count'] ?></td>
                                    <td class="table-actions">
                                        <a class="btn btn-secondary" href="edit.php?id=<?= (int)$n['neighborhood_id'] ?>">Edit</a>
                                        <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete this neighborhood?')">
                                            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= (int)$n['neighborhood_id'] ?>">
                                            <button class="btn btn-link" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
