<?php
$page_title = 'Add Inventory Item - Pharmacy';
$asset_path = '../../';
$extra_css = array('css/pharmacy.css');
$body_class = 'pharmacy-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Inventory</p>
                <h1 class="page-title">Add medicine</h1>
                <p class="page-subtitle">Select a medicine from the catalog and set stock details.</p>
                <div class="breadcrumb">
                    <a href="../index.html">Pharmacy</a>
                    <span>/</span>
                    <a href="index.html">Inventory</a>
                    <span>/</span>
                    <span>Add</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.html">Back to inventory</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-card">
                <h3>Inventory details</h3>
                <form action="index.html" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="inventory-medicine">Medicine</label>
                            <select id="inventory-medicine" name="medicine_id" required>
                                <option value="">Select medicine</option>
                                <option value="1">Insulin (Rapid)</option>
                                <option value="2">Metformin 500mg</option>
                                <option value="3">Amoxicillin 500mg</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inventory-quantity">Quantity</label>
                            <input type="number" id="inventory-quantity" name="quantity" min="0" step="1" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-price">Price (ETB)</label>
                            <input type="number" id="inventory-price" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-status">Status</label>
                            <select id="inventory-status" name="status" required>
                                <option value="in_stock">In stock</option>
                                <option value="limited">Limited</option>
                                <option value="out_of_stock">Out of stock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inventory-expiry">Expiry date (optional)</label>
                            <input type="date" id="inventory-expiry" name="expiry_date">
                        </div>
                        <div class="form-group form-group-full">
                            <label for="inventory-notes">Notes</label>
                            <textarea id="inventory-notes" name="notes" placeholder="Additional notes"></textarea>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button class="btn btn-primary" type="submit">Save item</button>
                        <a class="btn btn-secondary" href="index.html">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
