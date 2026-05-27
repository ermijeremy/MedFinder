<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/AdminService.php';
require_once '../../includes/admin-auth.php';

debug_request('admin/pharmacies/delete.php');

$id       = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pharmacy = $id > 0 ? AdminService::getPharmacy($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (!$pharmacy) {
        flash('error', 'Pharmacy not found.');
        redirect('admin/pharmacies/index.php');
    }

    AdminService::deletePharmacy($id);
    flash('success', h($pharmacy['pharmacy_name']) . ' was deleted.');
    redirect('admin/pharmacies/index.php');
}

if (!$pharmacy) {
    flash('error', 'Pharmacy not found.');
    redirect('admin/pharmacies/index.php');
}

$page_title = 'Delete Pharmacy - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <div class="auth-header">
                    <p class="eyebrow">Delete pharmacy</p>
                    <h2>Delete <?= h($pharmacy['pharmacy_name']) ?>?</h2>
                </div>
                <div class="alert alert-warning">This action permanently removes the pharmacy and all inventory.</div>
                <div class="form-footer">
                    <form action="delete.php?id=<?= (int)$id ?>" method="post">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= (int)$id ?>">
                        <button class="btn btn-primary" type="submit">Confirm delete</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
