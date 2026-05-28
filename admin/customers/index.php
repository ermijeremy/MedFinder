<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin-auth.php';
require_once '../../includes/services/CustomerService.php';

$search = sanitize($_GET['search'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));

$result = CustomerService::getAllCustomers($search, $page);
$customers = $result['rows'];
$pagination = $result['pagination'];

$asset_path = '../../';
$page_title = 'Manage Customers - Admin Panel';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Admin panel</p>
                <h1 class="page-title">Manage Customers</h1>
                <p class="page-subtitle">View and manage customer accounts</p>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="add.php">Add customer</a>
                <a class="btn btn-secondary" href="../index.html">Back to dashboard</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>

            <!-- Search and filters -->
            <div class="card" style="margin-bottom: 24px;">
                <form class="filter-form" action="index.php" method="get" style="display: flex; gap: 12px; align-items: flex-end;">
                    <div class="form-group" style="flex: 1;">
                        <label for="search-customers">Search</label>
                        <input type="text" id="search-customers" name="search" 
                               placeholder="Search by name or email..." 
                               value="<?= h($search) ?>" autocomplete="off">
                    </div>
                    <button class="btn btn-primary" type="submit">Search</button>
                    <?php if ($search): ?>
                        <a class="btn btn-secondary" href="index.php">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Results table -->
            <div class="card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Favorites</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 32px; color: #666;">
                                        No customers found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><strong><?= h($customer['customer_id']) ?></strong></td>
                                        <td><?= h($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                                        <td><?= h($customer['email']) ?></td>
                                        <td><?= h($customer['phone'] ?? '-') ?></td>
                                        <td><?= (int)($customer['favorite_count'] ?? 0) ?></td>
                                        <td>
                                            <span class="badge <?= $customer['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                                                <?= $customer['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <a class="btn btn-sm btn-secondary" href="edit.php?id=<?= h($customer['customer_id']) ?>">Edit</a>
                                                <a class="btn btn-sm btn-danger" href="delete.php?id=<?= h($customer['customer_id']) ?>" 
                                                   onclick="return confirm('Delete this customer? This action cannot be undone.');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total'] > 1): ?>
                <div class="pagination" style="margin-top: 24px; text-align: center;">
                    <?php if ($pagination['current'] > 1): ?>
                        <a class="btn btn-secondary" href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>">First</a>
                        <a class="btn btn-secondary" href="?page=<?= $pagination['current'] - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Previous</a>
                    <?php endif; ?>

                    <span style="margin: 0 12px; color: #666;">
                        Page <?= $pagination['current'] ?> of <?= $pagination['total'] ?>
                    </span>

                    <?php if ($pagination['current'] < $pagination['total']): ?>
                        <a class="btn btn-secondary" href="?page=<?= $pagination['current'] + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Next</a>
                        <a class="btn btn-secondary" href="?page=<?= $pagination['total'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Last</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../../includes/footer.php'; ?>
