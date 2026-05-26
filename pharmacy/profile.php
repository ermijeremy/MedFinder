<?php
$page_title = 'Edit Pharmacy Profile - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/pharmacy.css');
$body_class = 'pharmacy-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy profile</p>
                <h1 class="page-title">Update your profile</h1>
                <p class="page-subtitle">Keep contact details and operating hours accurate for patients.</p>
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
                <h3>Profile details</h3>
                <form action="index.html" method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="profile-name">Pharmacy name</label>
                            <input type="text" id="profile-name" name="pharmacy_name" value="Unity Pharmacy" autocomplete="organization" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-owner">Owner name</label>
                            <input type="text" id="profile-owner" name="owner_name" value="Hana Mulu" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-email">Email address</label>
                            <input type="email" id="profile-email" name="email" value="unity@pharmacy.et" autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-phone">Phone number</label>
                            <input type="tel" id="profile-phone" name="phone" value="+251912345678" autocomplete="tel" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-neighborhood">Neighborhood</label>
                            <select id="profile-neighborhood" name="neighborhood" required>
                                <option value="bole" selected>Bole</option>
                                <option value="kirkos">Kirkos</option>
                                <option value="arada">Arada</option>
                                <option value="yeka">Yeka</option>
                                <option value="lideta">Lideta</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="profile-hours">Operating hours</label>
                            <input type="text" id="profile-hours" name="operating_hours" value="8:00 AM - 9:00 PM">
                        </div>
                        <div class="form-group form-group-full">
                            <label for="profile-address">Address</label>
                            <textarea id="profile-address" name="address" autocomplete="street-address">Atlas area, Addis Ababa</textarea>
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
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
