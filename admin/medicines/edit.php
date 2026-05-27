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
    $data = [
        'medicine_name' => sanitize($_POST['medicine_name'] ??''),
        'generic_name'  => sanitize($_POST['generic_name'] ??''),
        'description'   => sanitize($_POST['description'] ??''),
        'category'      => sanitize($_POST['category'] ??''),
    ];

    if ($data['medicine_name'] === '') {
        flash('error', 'Medicine name is required.');
    } else {
        MedicineService::update($id, $data);
        flash('success', 'Medicine updated successfully.');
        redirect('admin/medicines/index.php');
    }
}

$page_title = 'Edit Medicine - Admin';
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
                <h1 class="page-title">Edit medicine</h1>
                <p class="page-subtitle">Update the catalog details shown to pharmacies.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <a href="index.php">Medicines</a>
                    <span>/</span>
                    <span>Edit</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">Back to list</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>
            <div class="form-card">
                <h3>Medicine details</h3>
                <form action="edit.php?id=<?= $id ?>" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="medicine-name">Medicine name <span class="required">*</span></label>
                            <input type="text" id="medicine-name" name="medicine_name" value="<?= h($m['medicine_name']) ?>" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="generic-name">Generic name</label>
                            <input type="text" id="generic-name" name="generic_name" value="<?= h($m['generic_name'] ?? '') ?>" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="medicine-category">Category <span class="required">*</span></label>
                            <select id="medicine-category" name="category" required>
                                <option value="">Select category</option>
                                <?php $cats = ['antibiotic', 'painkiller', 'diabetes', 'respiratory']; 
                                foreach ($cats as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $m['category'] === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="medicine-description">Description</label>
                            <textarea id="medicine-description" name="description"><?= h($m['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save changes</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
