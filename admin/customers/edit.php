<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin-auth.php';
require_once '../../includes/services/CustomerService.php';

$id = (int)($_GET['id'] ?? 0);
$customer = CustomerService::getCustomer($id);

if (!$customer) {
    http_response_code(404);
    flash('error', 'Customer not found');
    redirect('customers/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_request('edit_customer', ['id' => $id]);
    
    verify_csrf();

    $action = sanitize($_POST['action'] ?? 'profile');

    if ($action === 'profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name  = sanitize($_POST['last_name'] ?? '');
        $email      = sanitize($_POST['email'] ?? '');
        $phone      = sanitize($_POST['phone'] ?? '');

        // Validation
        if (empty($first_name)) {
            $errors['first_name'] = 'First name is required';
        }
        if (empty($last_name)) {
            $errors['last_name'] = 'Last name is required';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        } elseif (CustomerService::emailExists($email, $id)) {
            $errors['email'] = 'Email already exists';
        }

        if (empty($errors)) {
            if (CustomerService::updateCustomer($id, $first_name, $last_name, $email, $phone)) {
                flash('success', 'Customer updated successfully');
                $customer = CustomerService::getCustomer($id);
            } else {
                flash('error', 'Failed to update customer');
            }
        }
    } elseif ($action === 'password') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm      = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || strlen($new_password) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters';
        }
        if ($new_password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        if (empty($errors)) {
            if (CustomerService::updatePassword($id, $new_password)) {
                flash('success', 'Password updated successfully');
            } else {
                flash('error', 'Failed to update password');
            }
        }
    } elseif ($action === 'toggle_active') {
        CustomerService::toggleActive($id);
        $customer = CustomerService::getCustomer($id);
        flash('success', 'Customer status updated');
    }
}

$asset_path = '../../';
$page_title = 'Edit Customer - Admin Panel';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Admin panel</p>
                <h1 class="page-title">Edit Customer</h1>
                <p class="page-subtitle"><?= h($customer['first_name'] . ' ' . $customer['last_name']) ?></p>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">Back to customers</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 600px;">
            <?php render_flashes(); ?>

            <!-- Profile form -->
            <div class="card" style="margin-bottom: 24px;">
                <h2 style="margin-bottom: 16px;">Profile Information</h2>
                <form action="edit.php?id=<?= h($id) ?>" method="post" class="form">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="profile">

                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= h($customer['first_name']) ?>"
                               <?php if (isset($errors['first_name'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['first_name'])): ?>
                            <span class="error-text"><?= h($errors['first_name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?= h($customer['last_name']) ?>"
                               <?php if (isset($errors['last_name'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['last_name'])): ?>
                            <span class="error-text"><?= h($errors['last_name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= h($customer['email']) ?>"
                               <?php if (isset($errors['email'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-text"><?= h($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= h($customer['phone'] ?? '') ?>"
                               placeholder="+251 912 345 678">
                    </div>

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Password form -->
            <div class="card" style="margin-bottom: 24px;">
                <h2 style="margin-bottom: 16px;">Change Password</h2>
                <form action="edit.php?id=<?= h($id) ?>" method="post" class="form">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="password">

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" required 
                               <?php if (isset($errors['new_password'])): ?>aria-invalid="true"<?php endif; ?>>
                        <?php if (isset($errors['new_password'])): ?>
                            <span class="error-text"><?= h($errors['new_password']) ?></span>
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

                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                    </div>
                </form>
            </div>

            <!-- Account status -->
            <div class="card">
                <h2 style="margin-bottom: 16px;">Account Status</h2>
                <p style="margin-bottom: 12px;">
                    Current status: 
                    <span class="badge <?= $customer['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                        <?= $customer['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </p>
                <form action="edit.php?id=<?= h($id) ?>" method="post" style="display: inline;">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="toggle_active">
                    <button class="btn btn-<?= $customer['is_active'] ? 'danger' : 'success' ?>" type="submit">
                        <?= $customer['is_active'] ? 'Deactivate' : 'Activate' ?> Account
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
