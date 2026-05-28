<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/services/CustomerService.php';

// If already logged in, redirect to home
if (!empty($_SESSION['customer_id'])) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_request('customer_register');
    
    verify_csrf();

    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name  = sanitize($_POST['last_name'] ?? '');
    $email      = sanitize($_POST['email'] ?? '');
    $phone      = sanitize($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    } elseif (CustomerService::emailExists($email)) {
        $errors['email'] = 'Email already registered';
    }
    if (empty($password) || strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    if ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $customer_id = CustomerService::createCustomer($first_name, $last_name, $email, $password, $phone);
        
        if ($customer_id) {
            // Auto-login after registration
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_email'] = $email;
            
            flash('success', 'Welcome! Your account has been created successfully.');
            redirect('index.php');
        } else {
            flash('error', 'Registration failed. Please try again.');
        }
    }
}

// Start session for CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'mf_session');
    session_start();
}

$page_title = 'Register - MedFinder Ethiopia';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Create an account</p>
                <h1 class="page-title">Customer Registration</h1>
                <p class="page-subtitle">Join MedFinder to save favorite pharmacies and track medicines</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 500px;">
            <?php render_flashes(); ?>

            <div class="card">
                <form action="customer/register.php" method="post" class="form">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">

                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= h($_POST['first_name'] ?? '') ?>"
                               <?php if (isset($errors['first_name'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['first_name'])): ?>
                            <span class="error-text"><?= h($errors['first_name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?= h($_POST['last_name'] ?? '') ?>"
                               <?php if (isset($errors['last_name'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['last_name'])): ?>
                            <span class="error-text"><?= h($errors['last_name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= h($_POST['email'] ?? '') ?>"
                               <?php if (isset($errors['email'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-text"><?= h($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= h($_POST['phone'] ?? '') ?>"
                               placeholder="+251 912 345 678">
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="At least 8 characters"
                               <?php if (isset($errors['password'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['password'])): ?>
                            <span class="error-text"><?= h($errors['password']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               <?php if (isset($errors['confirm_password'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span class="error-text"><?= h($errors['confirm_password']) ?></span>
                        <?php endif; ?>
                    </div>

                    <button class="btn btn-primary btn-block" type="submit">Create Account</button>

                    <p style="text-align: center; margin-top: 16px; color: #666;">
                        Already have an account? <a href="customer/login.php" style="color: #10b981; font-weight: 600;">Log in here</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
