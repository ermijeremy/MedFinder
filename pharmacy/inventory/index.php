<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/pharmacy-auth.php';
require_once '../../includes/services/InventoryService.php';

$id = $_SESSION['pharmacy_id'];
$inventory = InventoryService::getByPharmacy($id);

$asset_path = '../../';
$page_title = 'Manage Inventory - MedFinder';
$body_class = 'pharmacy-body';
$extra_css = ['css/pharmacy.css'];
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Inventory</p>
                <h1 class="page-title">Manage inventory</h1>
                <div class="breadcrumb">
                    <a href="../index.php">Pharmacy</a>
                    <span>/</span>
                    <span>Inventory</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="add.php">Add medicine</a>
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
                        <label for="inventory-search">Search inventory</label>
                        <input type="text" id="inventory-search" placeholder="Search by medicine name">
                    </div>
                    <div class="form-group">
                        <label for="inventory-status">Status</label>
                        <select id="inventory-status">
                            <option value="">All statuses</option>
                            <option value="in_stock">In stock</option>
                            <option value="limited">Limited</option>
                            <option value="out_of_stock">Out of stock</option>
                        </select>
                    </div>
                </div>
                <span class="pill" data-count="inventory-list"><?= count($inventory) ?> items</span>
            </div>

            <div class="table-wrap">
                <table class="table" data-table="inventory-list">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventory)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No medicines in your inventory yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventory as $inv): ?>
                                <tr data-search-text="<?= h(strtolower($inv['medicine_name'] . ' ' . $inv['status'])) ?>" data-status="<?= h($inv['status']) ?>">
                                    <td>
                                        <strong><?= h($inv['medicine_name']) ?></strong>
                                        <?php if ($inv['generic_name']): ?>
                                            <br><small class="text-muted"><?= h($inv['generic_name']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int)$inv['quantity'] ?></td>
                                    <td><?= format_price($inv['price']) ?></td>
                                    <td><span class="badge <?= badge_class($inv['status']) ?>"><?= status_label($inv['status']) ?></span></td>
                                    <td><?= time_ago($inv['updated_at']) ?></td>
                                    <td class="table-actions">
                                        <a class="btn btn-secondary btn-sm" href="update.php?id=<?= (int)$inv['inventory_id'] ?>">Update</a>
                                        <a class="btn btn-link btn-sm" href="delete.php?id=<?= (int)$inv['inventory_id'] ?>" onclick="return confirm('Remove this medicine from inventory?')">Remove</a>
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
