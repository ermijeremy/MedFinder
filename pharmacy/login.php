<?php
$page_title = 'Pharmacy Login - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/pharmacy.css');
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <div class="auth-header">
                    <p class="eyebrow">Pharmacy access</p>
                    <h2>Sign in to your dashboard</h2>
                </div>
                <form action="index.html" method="post">
                    <div class="form-group">
                        <label for="pharmacy-email">Email address</label>
                        <input type="email" id="pharmacy-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="pharmacy-password">Password</label>
                        <input type="password" id="pharmacy-password" name="password" required>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                        <a class="btn btn-link" href="#">Forgot password?</a>
                    </div>
                </form>
                <div class="auth-footer">
                    <span>New pharmacy? </span><a class="link" href="register.php">Register here</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
