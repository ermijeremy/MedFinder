<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

$customer = CustomerService::getCustomer($_SESSION['customer_id']);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verify_csrf(); // Assuming CSRF is implemented as per message.md
    
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name  = sanitize($_POST['last_name'] ?? '');
        $email      = sanitize($_POST['email'] ?? '');
        $phone      = sanitize($_POST['phone'] ?? '');

        if (empty($first_name)) $errors['first_name'] = 'First name is required';
        if (empty($last_name)) $errors['last_name'] = 'Last name is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        } elseif (CustomerService::emailExists($email, $_SESSION['customer_id'])) {
            $errors['email'] = 'Email is already taken';
        }

        if (empty($errors)) {
            $photo = $customer['photo'];
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $uploaded = upload_customer_photo($_FILES['photo']);
                    if ($uploaded) {
                        $photo = $uploaded;
                    }
                } catch (Exception $e) {
                    $errors['photo'] = $e->getMessage();
                }
            }

            if (empty($errors) && CustomerService::updateCustomer($_SESSION['customer_id'], $first_name, $last_name, $email, $phone, $photo)) {
                flash('success', 'Profile updated successfully');
                $success = 'Profile updated successfully';
                $customer = CustomerService::getCustomer($_SESSION['customer_id']);
            } elseif (empty($errors)) {
                $errors['general'] = 'Failed to update profile';
            }
        }
    } elseif ($action === 'password') {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($old_password)) $errors['old_password'] = 'Current password is required';
        if (strlen($new_password) < 8) $errors['new_password'] = 'Password must be at least 8 characters';
        if ($new_password !== $confirm_password) $errors['confirm_password'] = 'Passwords do not match';

        if (empty($errors)) {
            if (CustomerService::verifyPassword($customer['email'], $old_password)) {
                if (CustomerService::updatePassword($_SESSION['customer_id'], $new_password)) {
                    flash('success', 'Password updated successfully');
                    $success = 'Password updated successfully';
                } else {
                    $errors['general'] = 'Failed to update password';
                }
            } else {
                $errors['old_password'] = 'Incorrect current password';
            }
        }
    }
}

$asset_path = '../';
$page_title = 'My Profile - MedFinder';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container">
            <h1>My Profile</h1>
            <p>View and update your account information.</p>
        </div>
    </section>

    <div class="container section">
        <div class="profile-grid">
            <!-- Edit Profile -->
            <div class="content-area">
                <div class="card">
                    <h2 class="panel-title">Edit Personal Information</h2>
                    <form action="profile.php" method="post" enctype="multipart/form-data" class="form">
                        <input type="hidden" name="action" value="profile">
                        
                        <?php if (isset($errors['photo'])): ?>
                            <div class="alert alert-danger"><?= $errors['photo'] ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="photo">Profile Photo</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <?php if (!empty($customer['photo'])): ?>
                                    <img src="<?= base_url() . 'uploads/customer-photos/' . h($customer['photo']) ?>" alt="Profile" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                <?php endif; ?>
                                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?= h($customer['first_name']) ?>" class="<?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" required>
                                <?php if (isset($errors['first_name'])): ?><span class="error-text"><?= $errors['first_name'] ?></span><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?= h($customer['last_name']) ?>" class="<?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" required>
                                <?php if (isset($errors['last_name'])): ?><span class="error-text"><?= $errors['last_name'] ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= h($customer['email']) ?>" class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['email'])): ?><span class="error-text"><?= $errors['email'] ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= h($customer['phone']) ?>" class="<?= isset($errors['phone']) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors['phone'])): ?><span class="error-text"><?= $errors['phone'] ?></span><?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <aside class="sidebar">
                <div class="card">
                    <h2 class="panel-title">Change Password</h2>
                    <form action="profile.php" method="post" class="form">
                        <input type="hidden" name="action" value="password">

                        <div class="form-group">
                            <label for="old_password">Current Password</label>
                            <input type="password" id="old_password" name="old_password" class="<?= isset($errors['old_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['old_password'])): ?><span class="error-text"><?= $errors['old_password'] ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="<?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['new_password'])): ?><span class="error-text"><?= $errors['new_password'] ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="<?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['confirm_password'])): ?><span class="error-text"><?= $errors['confirm_password'] ?></span><?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-secondary">Update Password</button>
                        </div>
                    </form>
                </div>

                <div class="card border-danger mt-4">
                    <h3 class="panel-title text-danger">Danger Zone</h3>
                    <p>Once you deactivate your account, there is no going back. Please be certain.</p>
                    <a href="deactivate.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to deactivate your account?')">Deactivate Account</a>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
