<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/MedicineService.php';
require_once '../../includes/admin-auth.php';

$id = (int)($_GET['id'] ?? 0);
$m  = MedicineService::getById($id);

if (!$m) {
    flash('error', 'Medicine not found.');
    redirect('admin/medicines/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    // Check if it's in use
    $count = MedicineService::getInventoryCount($id);
    if ($count > 0) {
        flash('error', "Cannot delete medicine '{$m['medicine_name']}' because it's listed in {$count} pharmacies' inventories. Please archive it instead or remove it from inventories first.");
        redirect('admin/medicines/index.php');
    }

    if (MedicineService::delete($id)) {
        flash('success', 'Medicine deleted successfully.');
    } else {
        flash('error', 'Failed to delete medicine.');
    }
    redirect('admin/medicines/index.php');
}

$page_title = 'Delete Medicine - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container container-narrow">
            <div class="form-card" style="text-align:center;max-width:500px;margin:100px auto">
                <div class="auth-header">
                    <p class="eyebrow" style="color:var(--danger)">Danger zone</p>
                    <h2>Delete <?= h($m['medicine_name']) ?>?</h2>
                </div>
                <div class="alert alert-warning" style="margin-bottom:2rem">
                    This action is permanent and cannot be undone. 
                    If this medicine is already in use by pharmacies, the deletion might fail.
                </div>
                <form action="delete.php?id=<?= $id ?>" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-footer" style="justify-content:center">
                        <button type="submit" class="btn btn-danger">Yes, delete medicine</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
