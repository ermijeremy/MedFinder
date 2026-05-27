<?php
/**
 * pharmacy/inventory/add.php — Add medicine to inventory
 */
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/InventoryService.php';
require_once '../../includes/services/MedicineService.php';
require_once '../../includes/pharmacy-auth.php';

debug_request('pharmacy/inventory/add.php');

$pid      = $current_pharmacy['pharmacy_id'];
$medicines = MedicineService::getDropdownList();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'medicine_id'  => (int)($_POST['medicine_id'] ?? 0),
        'quantity'     => (int)($_POST['quantity']    ?? 0),
        'price'        => (float)($_POST['price']     ?? 0),
        'status'       => sanitize($_POST['status']   ?? 'in_stock'),
        'expiry_date'  => sanitize($_POST['expiry_date']  ?? ''),
        'restock_note' => sanitize($_POST['restock_note'] ?? ''),
        'notes'        => sanitize($_POST['notes']        ?? ''),
    ];

    $errors = [];
    if ($data['medicine_id'] === 0)  $errors[] = 'Please select a medicine.';
    if ($data['quantity'] < 0)       $errors[] = 'Quantity cannot be negative.';
    if ($data['price'] < 0)          $errors[] = 'Price cannot be negative.';
    if (!in_array($data['status'], ['in_stock','limited','out_of_stock'], true))
        $errors[] = 'Invalid status value.';

    if (empty($errors)) {
        InventoryService::add($pid, $data);
        flash('success', 'Medicine added to inventory.');
        redirect('pharmacy/inventory/index.html');
    } else {
        flash('error', implode('<br>', $errors));
    }
}

$page_title = 'Add Inventory Item - Pharmacy';
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
                <h1 class="page-title">Add medicine</h1>
                <div class="breadcrumb">
                    <a href="../index.html">Pharmacy</a>
                    <span>/</span>
                    <a href="index.html">Inventory</a>
                    <span>/</span>
                    <span>Add</span>
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
                <h3>Inventory details</h3>
                <form action="add.php" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="inventory-medicine">Medicine</label>
                            <select id="inventory-medicine" name="medicine_id" required>
                                <option value="">Select medicine</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?= (int)$med['medicine_id'] ?>">
                                        <?= h($med['medicine_name']) ?>
                                        <?= $med['generic_name'] ? '(' . h($med['generic_name']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inventory-quantity">Quantity</label>
                            <input type="number" id="inventory-quantity" name="quantity" min="0" step="1" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-price">Price (ETB)</label>
                            <input type="number" id="inventory-price" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-status">Status</label>
                            <select id="inventory-status" name="status" required>
                                <option value="in_stock">In stock</option>
                                <option value="limited">Limited</option>
                                <option value="out_of_stock">Out of stock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inventory-expiry">Expiry date (optional)</label>
                            <input type="date" id="inventory-expiry" name="expiry_date">
                        </div>
                        <div class="form-group">
                            <label for="restock-note">Restock note (optional)</label>
                            <input type="text" id="restock-note" name="restock_note"
                                   placeholder="e.g. Restocking in 2 days">
                        </div>
                        <div class="form-group form-group-full">
                            <label for="inventory-notes">Notes</label>
                            <textarea id="inventory-notes" name="notes" placeholder="Additional notes"></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save item</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </div>
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                </form>
            </div>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
