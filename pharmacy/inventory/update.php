<?php
/**
 * pharmacy/inventory/update.php — Edit existing inventory row
 */
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/InventoryService.php';
require_once '../../includes/pharmacy-auth.php';

debug_request('pharmacy/inventory/update.php');

$pid = $current_pharmacy['pharmacy_id'];
$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$item = $id > 0 ? InventoryService::getOne($id, $pid) : null;

if (!$item) {
    flash('error', 'Inventory item not found.');
    redirect('pharmacy/inventory/index.html');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'quantity'     => (int)($_POST['quantity']    ?? 0),
        'price'        => (float)($_POST['price']     ?? 0),
        'status'       => sanitize($_POST['status']   ?? 'in_stock'),
        'expiry_date'  => sanitize($_POST['expiry_date']  ?? ''),
        'restock_note' => sanitize($_POST['restock_note'] ?? ''),
        'notes'        => sanitize($_POST['notes']        ?? ''),
    ];

    $errors = [];
    if ($data['quantity'] < 0) $errors[] = 'Quantity cannot be negative.';
    if ($data['price'] < 0)    $errors[] = 'Price cannot be negative.';

    if (empty($errors)) {
        InventoryService::update($id, $pid, $data);
        flash('success', 'Inventory updated.');
        redirect('pharmacy/inventory/index.html');
    } else {
        flash('error', implode('<br>', $errors));
    }
}

$page_title = 'Update Inventory - Pharmacy';
$asset_path = '../../';
$extra_css  = ['css/pharmacy.css'];
$body_class = 'pharmacy-body';
include '../../includes/header.php';
?>
<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Inventory</p>
                <h1 class="page-title">Update: <?= h($item['medicine_name']) ?></h1>
                <div class="breadcrumb">
                    <a href="../index.html">Pharmacy</a>
                    <span>/</span>
                    <a href="index.html">Inventory</a>
                    <span>/</span>
                    <span>Update</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.html">Back to inventory</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>
            <div class="form-card">
                <h3>Update inventory</h3>
                <form action="update.php?id=<?= (int)$id ?>" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" min="0"
                                   value="<?= (int)$item['quantity'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Price (ETB)</label>
                            <input type="number" name="price" min="0" step="0.01"
                                   value="<?= number_format((float)$item['price'], 2) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="in_stock" <?= ($item['status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In stock</option>
                                <option value="limited" <?= ($item['status'] ?? '') === 'limited' ? 'selected' : '' ?>>Limited</option>
                                <option value="out_of_stock" <?= ($item['status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Expiry date</label>
                            <input type="date" name="expiry_date"
                                   value="<?= h($item['expiry_date'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Restock note</label>
                            <input type="text" name="restock_note"
                                   value="<?= h($item['restock_note'] ?? '') ?>"
                                   placeholder="e.g. Restocking in 2 days">
                        </div>
                        <div class="form-group form-group-full">
                            <label>Notes</label>
                            <textarea name="notes"><?= h($item['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save changes</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </div>
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= (int)$id ?>">
                </form>
            </div>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
