<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/AdminService.php';
require_once '../../includes/admin-auth.php';

$id = (int)($_GET['id'] ?? 0);
$pharmacy = db_query(
    'SELECT p.*, n.name as neighborhood_name 
       FROM pharmacies p 
       LEFT JOIN neighborhoods n ON p.neighborhood_id = n.neighborhood_id 
      WHERE p.pharmacy_id = :id 
      LIMIT 1',
    [':id' => $id]
)->fetch();

if (!$pharmacy) {
    flash('error', 'Pharmacy not found.');
    redirect('admin/pharmacies/index.php');
}

// Get inventory snapshot
$inventory = db_query(
    'SELECT i.*, m.medicine_name, m.generic_name 
       FROM inventory i 
       JOIN medicines m ON i.medicine_id = m.medicine_id 
      WHERE i.pharmacy_id = :id 
   ORDER BY i.updated_at DESC 
      LIMIT 10',
    [':id' => $id]
)->fetchAll();

$page_title = 'Pharmacy Details - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy profile</p>
                <h1 class="page-title"><?= h($pharmacy['pharmacy_name']) ?></h1>
                <p class="page-subtitle"><?= h($pharmacy['address'] ?? 'No address') ?> | Neighborhood: <?= h($pharmacy['neighborhood_name'] ?? '—') ?> | Status: <?= ucfirst(h($pharmacy['status'])) ?></p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <a href="index.php">Pharmacies</a>
                    <span>/</span>
                    <span>View</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">Back to list</a>
                <a class="btn btn-primary" href="approve.php?id=<?= $id ?>">Update status</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container profile-grid">
            <div class="profile-main">
                <div class="panel">
                    <h3 class="panel-title">Pharmacy details</h3>
                    <div class="info-list">
                        <div class="info-row">
                            <span>Owner</span>
                            <span><?= h($pharmacy['owner_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span>Phone</span>
                            <span><?= h($pharmacy['phone']) ?></span>
                        </div>
                        <div class="info-row">
                            <span>Email</span>
                            <span><?= h($pharmacy['email']) ?></span>
                        </div>
                        <div class="info-row">
                            <span>License</span>
                            <span><?= h($pharmacy['license_number']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h3 class="panel-title">Inventory snapshot</h3>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Status</th>
                                    <th>Quantity</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($inventory)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center;padding:2rem">No inventory records.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($inventory as $item): ?>
                                        <tr>
                                            <td><?= h($item['medicine_name']) ?></td>
                                            <td>
                                                <span class="badge <?= $item['status'] === 'in-stock' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= h($item['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= (int)$item['quantity'] ?></td>
                                            <td><?= date('M d, H:i', strtotime($item['updated_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="profile-sidebar">
                <div class="panel">
                    <h3 class="panel-title">Status history</h3>
                    <div class="info-list">
                        <div class="info-row">
                            <span>Created</span>
                            <span><?= date('M d, Y', strtotime($pharmacy['created_at'])) ?></span>
                        </div>
                        <div class="info-row">
                            <span>Status</span>
                            <span><?= ucfirst(h($pharmacy['status'])) ?></span>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <h3 class="panel-title">Admin actions</h3>
                    <div class="form-footer">
                        <a class="btn btn-primary" href="approve.php?id=<?= $id ?>">Change status</a>
                        <form action="delete.php" method="post" onsubmit="return confirm('Suspend this pharmacy?')">
                            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button class="btn btn-secondary btn-block" type="submit">Suspend pharmacy</button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>