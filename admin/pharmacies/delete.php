<?php
$page_title = 'Suspend Pharmacy - Admin';
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
                    <p class="eyebrow">Suspend pharmacy</p>
                    <h2>Suspend Unity Pharmacy?</h2>
                </div>
                <div class="alert alert-warning">Suspended pharmacies will not appear in search results.</div>
                <div class="form-footer">
                    <a class="btn btn-primary" href="index.html">Confirm suspension</a>
                    <a class="btn btn-secondary" href="index.html">Cancel</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
