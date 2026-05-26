<?php
$page_title = 'Delete Neighborhood - Admin';
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
                    <p class="eyebrow">Delete neighborhood</p>
                    <h2>Delete Bole?</h2>
                </div>
                <div class="alert alert-warning">This action may remove the neighborhood from search filters.</div>
                <div class="form-footer">
                    <a class="btn btn-primary" href="index.html">Confirm delete</a>
                    <a class="btn btn-secondary" href="index.html">Cancel</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
