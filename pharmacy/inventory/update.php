<?php
$page_title = 'Update Inventory - Pharmacy';
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
                <h1 class="page-title">Update stock</h1>
                <p class="page-subtitle">Adjust quantity, price, and status for a medicine.</p>
                <div class="breadcrumb">
                    <a href="../index.html">Pharmacy</a>
                    <span>/</span>
                    <a href="index.html">Inventory</a>
                    <span>/</span>
                    <span>Update</span>
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
                <h3>Update inventory</h3>
                <form action="index.html" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="inventory-medicine">Medicine</label>
                            <input type="text" id="inventory-medicine" value="Metformin 500mg" disabled>
                        </div>
                        <div class="form-group">
                            <label for="inventory-quantity">Quantity</label>
                            <input type="number" id="inventory-quantity" name="quantity" min="0" step="1" value="6" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-price">Price (ETB)</label>
                            <input type="number" id="inventory-price" name="price" min="0" step="0.01" value="180" required>
                        </div>
                        <div class="form-group">
                            <label for="inventory-status">Status</label>
                            <select id="inventory-status" name="status" required>
                                <option value="in_stock">In stock</option>
                                <option value="limited" selected>Limited</option>
                                <option value="out_of_stock">Out of stock</option>
                            </select>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="inventory-notes">Notes</label>
                            <textarea id="inventory-notes" name="notes">Low stock alert active.</textarea>
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
