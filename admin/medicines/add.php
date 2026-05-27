<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/MedicineService.php';
require_once '../../includes/admin-auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'medicine_name' => sanitize($_POST['medicine_name'] ?? ''),
        'generic_name'  => sanitize($_POST['generic_name'] ?? ''),
        'description'   => sanitize($_POST['description'] ?? ''),
        'category'      => sanitize($_POST['category'] ?? ''),
    ];

    if ($data['medicine_name'] === '') {
        flash('error', 'Medicine name is required.');
    } else {
        MedicineService::add($data);
        flash('success', 'Medicine added successfully.');
        redirect('admin/medicines/index.php');
    }
}

$page_title = 'Add Medicine - Admin';
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
                <h1 class="page-title">Add a new medicine</h1>
                <p class="page-subtitle">Create a catalog entry for pharmacies to use in their inventory.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <a href="index.php">Medicines</a>
                    <span>/</span>
                    <span>Add</span>
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
                <form action="add.php" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="medicine-name">Medicine name <span class="required">*</span></label>
                            <input type="text" id="medicine-name" name="medicine_name" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="generic-name">Generic name</label>
                            <input type="text" id="generic-name" name="generic_name" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="medicine-category">Category <span class="required">*</span></label>
                            <select id="medicine-category" name="category" required>
                                <option value="">Select category</option>
                                <option value="antibiotic">Antibiotic</option>
                                <option value="painkiller">Painkiller</option>
                                <option value="diabetes">Diabetes</option>
                                <option value="respiratory">Respiratory</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="medicine-description">Description</label>
                            <textarea id="medicine-description" name="description" placeholder="Short usage notes"></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save medicine</button>
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
