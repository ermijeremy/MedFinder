<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/NeighborhoodService.php';
require_once '../../includes/admin-auth.php';

$id = (int)($_GET['id'] ?? 0);
$n  = NeighborhoodService::getById($id);

if (!$n) {
    flash('error', 'Neighborhood not found.');
    redirect('admin/neighborhoods/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    

    $count = NeighborhoodService::getPharmacyCount($id);
    if ($count > 0) {
        flash('error', "Cannot delete neighborhood '{$n['name']}' because it is assigned to {$count} pharmacies.");
        redirect('admin/neighborhoods/index.php');
    }

    if (NeighborhoodService::delete($id)) {
        flash('success', 'Neighborhood deleted successfully.');
    } else {
        flash('error', 'Failed to delete neighborhood.');
    }
    redirect('admin/neighborhoods/index.php');
}

$page_title = 'Delete Neighborhood - Admin';
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
                    <h2>Delete <?= h($n['name']) ?>?</h2>
                </div>
                <div class="alert alert-warning" style="margin-bottom:2rem">
                    This action is permanent and cannot be undone. 
                    It will fail if any pharmacy is currently assigned to this neighborhood.
                </div>
                <form action="delete.php?id=<?= $id ?>" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-footer" style="justify-content:center">
                        <button type="submit" class="btn btn-danger">Yes, delete neighborhood</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
