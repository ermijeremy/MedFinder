<?php
/**
 * pharmacy/register.php — Pharmacy Registration Page + POST Handler
 *
 * FLOW:
 *  GET  → render form, populate neighborhood dropdown from DB
 *  POST → validate CSRF → server-side validation → uniqueness check
 *         → upload logo → hash password → INSERT → flash success → redirect
 *
 * SECURITY:
 *  - All inputs sanitized before use
 *  - Prepared statements for all DB queries
 *  - File upload validated by MIME type (not extension)
 *  - Password hashed with bcrypt cost 12
 *  - CSRF token on every POST
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

start_session();
debug_request('pharmacy/register.php');

// Load neighborhoods for the dropdown (also used on GET)
$neighborhoods = db_query(
    'SELECT neighborhood_id, name FROM neighborhoods ORDER BY name ASC'
)->fetchAll();

$errors   = [];
$old      = [];   // Repopulate form on validation failure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // ── Collect inputs ─────────────────────────────────────
    $old = [
        'pharmacy_name'   => sanitize($_POST['pharmacy_name']   ?? ''),
        'owner_name'      => sanitize($_POST['owner_name']       ?? ''),
        'email'           => sanitize($_POST['email']            ?? ''),
        'phone'           => sanitize($_POST['phone']            ?? ''),
        'neighborhood_id' => (int)($_POST['neighborhood']        ?? 0),
        'license_number'  => sanitize($_POST['license_number']   ?? ''),
        'address'         => sanitize($_POST['address']          ?? ''),
        'operating_hours' => sanitize($_POST['operating_hours']  ?? ''),
    ];
    $password         = $_POST['password']         ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // ── Server-side validation ─────────────────────────────
    if ($old['pharmacy_name'] === '')
        $errors[] = 'Pharmacy name is required.';
    if ($old['owner_name'] === '')
        $errors[] = 'Owner name is required.';
    if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email address is required.';
    if (!is_valid_ethiopian_phone($old['phone']))
        $errors[] = 'Phone must be a valid Ethiopian number (e.g. +251912345678 or 0912345678).';
    if (!is_strong_password($password))
        $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm_password)
        $errors[] = 'Passwords do not match.';
    if ($old['neighborhood_id'] === 0)
        $errors[] = 'Please select a neighborhood.';
    if ($old['license_number'] === '')
        $errors[] = 'License number is required.';
    if ($old['address'] === '')
        $errors[] = 'Address is required.';

    // ── Uniqueness checks ──────────────────────────────────
    if (empty($errors)) {
        $emailExists = db_query(
            'SELECT pharmacy_id FROM pharmacies WHERE email = :e LIMIT 1',
            [':e' => $old['email']]
        )->fetch();
        if ($emailExists) $errors[] = 'That email address is already registered.';

        $licenseExists = db_query(
            'SELECT pharmacy_id FROM pharmacies WHERE license_number = :l LIMIT 1',
            [':l' => $old['license_number']]
        )->fetch();
        if ($licenseExists) $errors[] = 'That license number is already registered.';
    }

    // ── File upload ────────────────────────────────────────
    $logoFilename = null;
    if (empty($errors) && !empty($_FILES['logo']['name'])) {
        try {
            $logoFilename = upload_logo($_FILES['logo']);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    // ── Insert if clean ────────────────────────────────────
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        db_query(
            'INSERT INTO pharmacies
                (pharmacy_name, owner_name, email, phone, password,
                 address, neighborhood_id, license_number,
                 logo, operating_hours, status)
             VALUES
                (:name, :owner, :email, :phone, :pass,
                 :addr, :nbhd, :lic,
                 :logo, :hours, \'pending\')',
            [
                ':name'  => $old['pharmacy_name'],
                ':owner' => $old['owner_name'],
                ':email' => $old['email'],
                ':phone' => $old['phone'],
                ':pass'  => $hashedPassword,
                ':addr'  => $old['address'],
                ':nbhd'  => $old['neighborhood_id'],
                ':lic'   => $old['license_number'],
                ':logo'  => $logoFilename,
                ':hours' => $old['operating_hours'],
            ]
        );

        flash('success',
            'Registration submitted! Your pharmacy is under review. ' .
            'You will be notified once approved.'
        );
        redirect('pharmacy/login.php');
    }

    // Keep errors for display
    if (!empty($errors)) {
        flash('error', implode('<br>', $errors));
    }
}

// ── View ───────────────────────────────────────────────────
$page_title = 'Pharmacy Registration - MedFinder Ethiopia';
$asset_path = '../';
$extra_css  = ['css/pharmacy.css'];
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy registration</p>
                <h1 class="page-title">Join MedFinder</h1>
                <p class="page-subtitle">Submit your pharmacy details for approval and start publishing inventory.</p>
                <div class="breadcrumb">
                    <a href="../index.html">Home</a>
                    <span>/</span>
                    <span>Register pharmacy</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="login.php">Already registered? Log in</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-card">
                <?php render_flashes(); ?>
                <h3>Registration form</h3>
                <form action="register.php" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pharmacy-name">Pharmacy name</label>
                            <input type="text" id="pharmacy-name" name="pharmacy_name" autocomplete="organization" required>
                        </div>
                        <div class="form-group">
                            <label for="owner-name">Owner name</label>
                            <input type="text" id="owner-name" name="owner_name" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Email address</label>
                            <input type="email" id="register-email" name="email" autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <label for="register-phone">Phone number</label>
                            <input type="tel" id="register-phone" name="phone" placeholder="+251 9x xxx xxxx" autocomplete="tel" required>
                        </div>
                        <div class="form-group">
                            <label for="register-password">Password</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" required>
                        </div>
                        <div class="form-group">
                            <label for="register-confirm">Confirm password</label>
                            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                            <span class="form-note" id="password-help">Passwords must match.</span>
                        </div>
                        <div class="form-group">
                            <label for="register-neighborhood">Neighborhood</label>
                            <select id="register-neighborhood" name="neighborhood" required>
                                <option value="">Select neighborhood</option>
                                <?php foreach ($neighborhoods as $nbhd): ?>
                                    <option value="<?= h($nbhd['neighborhood_id']) ?>"
                                        <?= (($old['neighborhood_id'] ?? 0) == $nbhd['neighborhood_id']) ? 'selected' : '' ?>>
                                        <?= h($nbhd['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="register-license">License number</label>
                            <input type="text" id="register-license" name="license_number" autocomplete="off" required>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="register-address">Address</label>
                            <textarea id="register-address" name="address" autocomplete="street-address" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="register-hours">Operating hours</label>
                            <input type="text" id="register-hours" name="operating_hours"
                                   value="<?= h($old['operating_hours'] ?? '') ?>"
                                   placeholder="8:00 AM - 9:00 PM">
                        </div>
                        <div class="form-group">
                            <label for="register-logo">Upload logo (optional)</label>
                            <input type="file" id="register-logo" name="logo" accept="image/*">
                        </div>
                    </div>

                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Submit registration</button>
                        <span class="form-note">Approval required before account activation.</span>
                    </div>
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
