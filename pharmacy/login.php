<?php
/**
 * Pharmacy Login Page + POST Handler
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

start_session();
debug_request('pharmacy/login.php');


if (!empty($_SESSION['pharmacy_id'])) {
    redirect('pharmacy/index.php');
}


$notice = $_GET['notice'] ?? '';
$notice_messages = [
    'pending'   => 'Your account is awaiting approval.',
    'suspended' => 'Your account has been suspended.',
];

if ($notice !== '' && isset($notice_messages[$notice])) {
    flash('info', $notice_messages[$notice]);
}


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if ($email    === '') $errors[] = 'Email is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (empty($errors)) {
        $pharmacy = db_query(
            'SELECT pharmacy_id, pharmacy_name, email, password, status
               FROM pharmacies
              WHERE email = :email
              LIMIT 1',
            [':email' => $email]
        )->fetch();

        if (!$pharmacy || !password_verify($password, $pharmacy['password'])) {
            $errors[] = 'Invalid email or password.';
        } elseif ($pharmacy['status'] === 'pending') {
            $errors[] = $notice_messages['pending'];
        } elseif ($pharmacy['status'] === 'suspended') {
            $errors[] = $notice_messages['suspended'];
        } else {
            // Valid active pharmacy
            session_regenerate_id(true);

            $_SESSION['pharmacy_id']   = $pharmacy['pharmacy_id'];
            $_SESSION['pharmacy_name'] = $pharmacy['pharmacy_name'];

            flash('success', 'Welcome back, ' . $pharmacy['pharmacy_name'] . '!');
            redirect('pharmacy/index.php');
        }
    }

    if (!empty($errors)) {
        flash('error', implode('<br>', $errors));
    }
}

 
$page_title = 'Pharmacy Login - MedFinder Ethiopia';
$asset_path = '../';
$extra_css  = ['css/pharmacy.css'];
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <?php render_flashes(); ?>
                <div class="auth-header">
                    <p class="eyebrow">Pharmacy access</p>
                    <h2>Sign in to your dashboard</h2>
                </div>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="pharmacy-email">Email address <span class="required">*</span></label>
                        <input type="email" id="pharmacy-email" name="email"
                               value="<?= h($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pharmacy-password">Password <span class="required">*</span></label>
                        <input type="password" id="pharmacy-password" name="password" required>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Log in</button>
                        <a class="btn-link" href="register.php">Create an account</a>
                    </div>
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                </form>

                <div class="auth-footer">
                    <span>New pharmacy? </span>
                    <a class="link" href="register.php">Register here</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
