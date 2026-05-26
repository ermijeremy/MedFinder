<?php
$page_title = 'Pharmacy Registration - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/pharmacy.css');
$extra_js = array('js/validation.js');
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
                <h3>Registration form</h3>
                <form action="login.php" method="post" enctype="multipart/form-data">
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
                                <option value="bole">Bole</option>
                                <option value="kirkos">Kirkos</option>
                                <option value="arada">Arada</option>
                                <option value="yeka">Yeka</option>
                                <option value="lideta">Lideta</option>
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
                            <input type="text" id="register-hours" name="operating_hours" placeholder="8:00 AM - 9:00 PM">
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
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
