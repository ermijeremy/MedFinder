<?php
$page_title = 'Edit Neighborhood - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Neighborhoods</p>
                <h1 class="page-title">Edit neighborhood</h1>
                <p class="page-subtitle">Update neighborhood labels used for pharmacy locations.</p>
                <div class="breadcrumb">
                    <a href="../index.html">Admin</a>
                    <span>/</span>
                    <a href="index.html">Neighborhoods</a>
                    <span>/</span>
                    <span>Edit</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.html">Back to list</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-card">
                <h3>Neighborhood details</h3>
                <form action="index.html" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="neighborhood-name">Name</label>
                            <input type="text" id="neighborhood-name" name="name" value="Bole" required>
                        </div>
                        <div class="form-group">
                            <label for="neighborhood-zone">Zone</label>
                            <select id="neighborhood-zone" name="zone">
                                <option value="">Select zone</option>
                                <option value="north">North</option>
                                <option value="south">South</option>
                                <option value="east" selected>East</option>
                                <option value="west">West</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="neighborhood-description">Description</label>
                            <textarea id="neighborhood-description" name="description">Airport corridor and commercial zone.</textarea>
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

<?php include '../../includes/footer.php'; ?>
