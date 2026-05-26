<?php
$page_title = 'Add Medicine - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Medicine catalog</p>
                <h1 class="page-title">Add a new medicine</h1>
                <p class="page-subtitle">Create a catalog entry for pharmacies to use in their inventory.</p>
                <div class="breadcrumb">
                    <a href="../index.html">Admin</a>
                    <span>/</span>
                    <a href="index.html">Medicines</a>
                    <span>/</span>
                    <span>Add</span>
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
                <h3>Medicine details</h3>
                <form action="index.html" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="medicine-name">Medicine name</label>
                            <input type="text" id="medicine-name" name="medicine_name" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="generic-name">Generic name</label>
                            <input type="text" id="generic-name" name="generic_name" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="medicine-category">Category</label>
                            <select id="medicine-category" name="category" required>
                                <option value="">Select category</option>
                                <option value="antibiotic">Antibiotic</option>
                                <option value="painkiller">Painkiller</option>
                                <option value="diabetes">Diabetes</option>
                                <option value="respiratory">Respiratory</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="medicine-description">Description</label>
                            <textarea id="medicine-description" name="description" placeholder="Short usage notes"></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save medicine</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
