<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/InventoryService.php';
require_once '../../includes/pharmacy-auth.php';

debug_request('pharmacy/inventory/delete.php');

$pid  = $current_pharmacy['pharmacy_id'];
$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$item = $id > 0 ? InventoryService::getOne($id, $pid) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (!$item) {
        flash('error', 'Inventory item not found.');
        redirect('pharmacy/inventory/index.html');
    }

    InventoryService::delete($id, $pid);
    flash('success', h($item['medicine_name']) . ' was removed.');
    redirect('pharmacy/inventory/index.html');
}

if (!$item) {
    flash('error', 'Inventory item not found.');
    redirect('pharmacy/inventory/index.html');
}

$page_title = 'Remove Inventory Item - Pharmacy';
$asset_path = '../../';
$extra_css = array('css/pharmacy.css');
$body_class = 'pharmacy-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <div class="auth-header">
                    <p class="eyebrow">Remove item</p>
                    <h2>Remove <?= h($item['medicine_name']) ?>?</h2>
                </div>
                <div class="alert alert-warning">This medicine will no longer appear in search results.</div>
                <div class="form-footer">
                    <form action="delete.php?id=<?= (int)$id ?>" method="post">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= (int)$id ?>">
                        <button class="btn btn-primary" type="submit">Confirm removal</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
