<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/services/CustomerService.php';

// If already logged in, redirect to home
if (!empty($_SESSION['customer_id'])) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_request('customer_login');
    
    verify_csrf();

    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        flash('error', 'Email and password are required');
    } else {
        $customer = CustomerService::verifyPassword($email, $password);

        if ($customer) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['customer_id']    = $customer['customer_id'];
            $_SESSION['customer_email'] = $customer['email'];

            flash('success', 'Welcome back, ' . h($customer['first_name']) . '!');
            redirect('index.php');
        } else {
            flash('error', 'Invalid email or password');
        }
    }
}

// Start session for CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}

$asset_path = '../';
$page_title = 'Customer Login - MedFinder Ethiopia';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Sign in to your account</p>
                <h1 class="page-title">Customer Login</h1>
                <p class="page-subtitle">Access your saved favorites and search history</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 500px;">
            <?php render_flashes(); ?>

            <div class="card">
                <form action="login.php" method="post" class="form">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               autocomplete="email"
                               value="<?= h($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required 
                               autocomplete="current-password">
                    </div>

                    <button class="btn btn-primary btn-block" type="submit">Log In</button>

                    <p style="text-align: center; margin-top: 16px; color: #666;">
                        Don't have an account? <a href="register.php" style="color: #10b981; font-weight: 600;">Register here</a>
                    </p>
                </form>
            </div>

            <div class="card" style="margin-top: 24px; background: #f0fdf4; border: 1px solid #bbf7d0;">
                <h3 style="color: #15803d; margin-bottom: 12px;">Demo Credentials</h3>
                <p style="font-size: 14px; margin-bottom: 8px;">
                    <strong>Email:</strong> customer@test.et<br>
                    <strong>Password:</strong> password123
                </p>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
