<?php

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/services/PharmacyService.php';
require_once '../includes/services/NeighborhoodService.php';
require_once '../includes/pharmacy-auth.php';

debug_request('pharmacy/profile.php');

$pid           = $current_pharmacy['pharmacy_id'];
$neighborhoods = NeighborhoodService::getAll();
$old           = $current_pharmacy;   // pre-fill form with current values

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? 'profile';
    $wantsJson = wants_json();

    if ($action === 'profile') {
        $data = [
            'pharmacy_name'   => sanitize($_POST['pharmacy_name']   ?? ''),
            'owner_name'      => sanitize($_POST['owner_name']       ?? ''),
            'phone'           => sanitize($_POST['phone']            ?? ''),
            'address'         => sanitize($_POST['address']          ?? ''),
            'neighborhood_id' => (int)($_POST['neighborhood_id']     ?? 0),
            'operating_hours' => sanitize($_POST['operating_hours']  ?? ''),
        ];

        $errors = [];
        if ($data['pharmacy_name'] === '') $errors[] = 'Pharmacy name is required.';
        if (!is_valid_ethiopian_phone($data['phone'])) $errors[] = 'Invalid phone number.';
        if ($data['neighborhood_id'] === 0) $errors[] = 'Please select a neighborhood.';
        if ($data['neighborhood_id'] > 0) {
            $exists = db_query(
                'SELECT 1 FROM neighborhoods WHERE neighborhood_id = :id LIMIT 1',
                [':id' => $data['neighborhood_id']]
            )->fetchColumn();
            if (!$exists) $errors[] = 'Selected neighborhood does not exist.';
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            try {
                $data['logo'] = upload_logo($_FILES['logo']);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (empty($errors)) {
            try {
                PharmacyService::updateProfile($pid, $data);
                if ($wantsJson) {
                    json_response(['status' => 'ok', 'message' => 'Profile updated successfully.']);
                }
                flash('success', 'Profile updated successfully.');
                redirect('pharmacy/profile.php');
            } catch (PDOException $e) {
                $msg = $e->getCode() === '23000'
                    ? 'Invalid neighborhood selection.'
                    : 'Unable to update profile right now. Please try again.';
                if ($wantsJson) {
                    json_response(['status' => 'error', 'message' => $msg], 422);
                }
                $errors[] = $msg;
            }
        }

        if (!empty($errors)) {
            if ($wantsJson) {
                json_response(['status' => 'error', 'errors' => $errors], 422);
            }
            flash('error', implode('<br>', $errors));
            $old = array_merge($old, $data);
        }
    }

    if ($action === 'password') {
        // ── Password change ────────────────────────────────
        $currentPass = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password']      ?? '';
        $confirmPass = $_POST['confirm_password']  ?? '';

        $errors = [];
        $dbRow  = db_query(
            'SELECT password FROM pharmacies WHERE pharmacy_id = :id LIMIT 1',
            [':id' => $pid]
        )->fetch();

        if (!password_verify($currentPass, $dbRow['password']))
            $errors[] = 'Current password is incorrect.';
        if (!is_strong_password($newPass))
            $errors[] = 'New password must be at least 8 characters.';
        if ($newPass !== $confirmPass)
            $errors[] = 'New passwords do not match.';

        if (empty($errors)) {
            PharmacyService::updatePassword($pid, $newPass);
            if ($wantsJson) {
                json_response(['status' => 'ok', 'message' => 'Password changed successfully.']);
            }
            flash('success', 'Password changed successfully.');
            redirect('pharmacy/profile.php');
        } else {
            if ($wantsJson) {
                json_response(['status' => 'error', 'errors' => $errors], 422);
            }
            flash('error', implode('<br>', $errors));
        }
    }
}

$page_title = 'Edit Profile - ' . h($current_pharmacy['pharmacy_name']);
$asset_path = '../';
$extra_css  = ['css/pharmacy.css'];
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>
<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy settings</p>
                <h1 class="page-title">Edit profile</h1>
                <div class="breadcrumb">
                    <a href="index.html">Pharmacy</a>
                    <span>/</span>
                    <span>Profile</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.html">Back to dashboard</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-card">
                <?php render_flashes(); ?>
                <h3>Profile details</h3>
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="profile-name">Pharmacy name</label>
                            <input type="text" id="profile-name" name="pharmacy_name" value="<?= h($old['pharmacy_name'] ?? '') ?>" autocomplete="organization" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-owner">Owner name</label>
                            <input type="text" id="profile-owner" name="owner_name" value="<?= h($old['owner_name'] ?? '') ?>" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-email">Email address</label>
                            <input type="email" id="profile-email" name="email" value="<?= h($old['email'] ?? '') ?>" autocomplete="email" readonly>
                        </div>
                        <div class="form-group">
                            <label for="profile-phone">Phone number</label>
                            <input type="tel" id="profile-phone" name="phone" value="<?= h($old['phone'] ?? '') ?>" autocomplete="tel" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-neighborhood">Neighborhood</label>
                            <select id="profile-neighborhood" name="neighborhood_id" required>
                                <option value="">Select neighborhood</option>
                                <?php foreach ($neighborhoods as $nbhd): ?>
                                    <option value="<?= (int)$nbhd['neighborhood_id'] ?>"
                                        <?= ((int)($old['neighborhood_id'] ?? 0) === (int)$nbhd['neighborhood_id']) ? 'selected' : '' ?>>
                                        <?= h($nbhd['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="profile-hours">Operating hours</label>
                            <input type="text" id="profile-hours" name="operating_hours" value="<?= h($old['operating_hours'] ?? '') ?>">
                        </div>
                        <div class="form-group form-group-full">
                            <label for="profile-address">Address</label>
                            <textarea id="profile-address" name="address" autocomplete="street-address"><?= h($old['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="profile-logo">Update logo</label>
                            <input type="file" id="profile-logo" name="logo" accept="image/*">
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save changes</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </div>
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                </form>
            </div>
        </div>
    </section>
</main>
<?php include '../includes/footer.php'; ?>
