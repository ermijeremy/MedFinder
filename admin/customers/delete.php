<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin-auth.php';
require_once '../../includes/services/CustomerService.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    http_response_code(404);
    if (wants_json()) {
        json_response(['status' => 'error', 'message' => 'Customer not found'], 404);
    }
    flash('error', 'Customer not found');
    redirect('customers/index.php');
}

$customer = CustomerService::getCustomer($id);

if (!$customer) {
    http_response_code(404);
    if (wants_json()) {
        json_response(['status' => 'error', 'message' => 'Customer not found'], 404);
    }
    flash('error', 'Customer not found');
    redirect('customers/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_request('delete_customer', ['id' => $id]);
    
    verify_csrf();

    if (CustomerService::deleteCustomer($id)) {
        if (wants_json()) {
            json_response(['status' => 'ok']);
        }
        flash('success', "Customer '" . h($customer['first_name'] . ' ' . $customer['last_name']) . "' deleted successfully");
        redirect('customers/index.php');
    } else {
        http_response_code(422);
        if (wants_json()) {
            json_response(['status' => 'error', 'message' => 'Failed to delete customer'], 422);
        }
        flash('error', 'Failed to delete customer');
    }
}

// Display confirmation page
$asset_path = '../../';
$page_title = 'Delete Customer - Admin Panel';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Admin panel</p>
                <h1 class="page-title">Delete Customer</h1>
                <p class="page-subtitle">Confirm deletion</p>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">Back to customers</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 500px;">
            <div class="card" style="border: 2px solid #dc2626; background: #fef2f2;">
                <h2 style="color: #dc2626; margin-bottom: 16px;">Confirm Deletion</h2>
                <p style="margin-bottom: 16px;">
                    Are you sure you want to delete <strong><?= h($customer['first_name'] . ' ' . $customer['last_name']) ?></strong> (<?= h($customer['email']) ?>)?
                </p>
                <p style="margin-bottom: 24px; color: #666;">
                    This action cannot be undone. All customer data and favorite records will be permanently deleted.
                </p>

                <form action="delete.php?id=<?= h($id) ?>" method="post" style="display: flex; gap: 12px;">
                    <input type="hidden" name="_csrf_token" value="<?= h(csrf_token()) ?>">
                    <button class="btn btn-danger" type="submit">Yes, Delete Customer</button>
                    <a class="btn btn-secondary" href="index.php">Cancel</a>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
