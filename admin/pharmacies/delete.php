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
        <div class="container container-narrow">
            <div class="auth-card" style="max-width:500px;margin:100px auto">
                <div class="auth-header">
                    <p class="eyebrow" style="color:var(--danger)">Danger zone</p>
                    <h2>Delete <?= h($pharmacy['pharmacy_name']) ?>?</h2>
                </div>
                <div class="alert alert-warning">This action permanently removes the pharmacy and all inventory.</div>
                <div class="form-footer" style="justify-content:center">
                    <form action="delete.php?id=<?= (int)$id ?>" method="post">
                        <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= (int)$id ?>">
                        <button class="btn btn-danger" type="submit">Confirm delete</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
