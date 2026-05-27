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
    $data = [
        'name'        => sanitize($_POST['name'] ?? ''),
        'zone'        => sanitize($_POST['zone'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
    ];

    if ($data['name'] === '') {
        flash('error', 'Neighborhood name is required.');
    } else {
        NeighborhoodService::update($id, $data);
        flash('success', 'Neighborhood updated successfully.');
        redirect('admin/neighborhoods/index.php');
    }
}

$page_title = 'Edit Neighborhood - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Neighborhoods</p>
                <h1 class="page-title">Edit neighborhood</h1>
                <p class="page-subtitle">Update neighborhood labels used for pharmacy locations.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <a href="index.php">Neighborhoods</a>
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
                <h3>Neighborhood details</h3>
                <form action="edit.php?id=<?= $id ?>" method="post">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="neighborhood-name">Name</label>
                            <input type="text" id="neighborhood-name" name="name" value="<?= h($n['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="neighborhood-zone">Zone</label>
                            <select id="neighborhood-zone" name="zone">
                                <option value="">Select zone</option>
                                <option value="north" <?= $n['zone'] === 'north' ? 'selected' : '' ?>>North</option>
                                <option value="south" <?= $n['zone'] === 'south' ? 'selected' : '' ?>>South</option>
                                <option value="east" <?= $n['zone'] === 'east' ? 'selected' : '' ?>>East</option>
                                <option value="west" <?= $n['zone'] === 'west' ? 'selected' : '' ?>>West</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="neighborhood-description">Description</label>
                            <textarea id="neighborhood-description" name="description"><?= h($n['description'] ?? '') ?></textarea>
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
