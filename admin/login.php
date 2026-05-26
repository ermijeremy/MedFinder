<?php
$page_title = 'Admin Login - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <div class="auth-header">
                    <p class="eyebrow">Admin access</p>
                    <h2>Sign in to the admin panel</h2>
                </div>
                <form action="index.html" method="post">
                    <div class="form-group">
                        <label for="admin-username">Username</label>
                        <input type="text" id="admin-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin-password">Password</label>
                        <input type="password" id="admin-password" name="password" required>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                    </div>
                </form>
                <div class="auth-footer">
                    <a class="link" href="../index.html">Back to website</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
