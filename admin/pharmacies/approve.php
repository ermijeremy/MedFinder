<?php
/**
 * admin/pharmacies/approve.php — Review + approve/reject pharmacy
 */
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/services/AdminService.php';
require_once '../../includes/admin-auth.php';

debug_request('admin/pharmacies/approve.php');

$id       = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pharmacy = $id > 0 ? AdminService::getPharmacy($id) : null;

if (!$pharmacy) {
    flash('error', 'Pharmacy not found.');
    redirect('admin/pharmacies/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $reason = sanitize($_POST['reason'] ?? '');

    if ($action === 'approve') {
        AdminService::approvePharmacy($id);
        flash('success', h($pharmacy['pharmacy_name']) . ' has been approved.');
    } elseif ($action === 'reject') {
        AdminService::rejectPharmacy($id, $reason);
        flash('success', h($pharmacy['pharmacy_name']) . ' has been rejected/suspended.');
    }

    redirect('admin/pharmacies/index.php');
}

$page_title = 'Review Pharmacy - Admin';
$asset_path = '../../';
$extra_css  = ['css/admin.css'];
$body_class = 'admin-body';
include '../../includes/header.php';
?>
<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Admin</p>
                <h1 class="page-title">Review: <?= h($pharmacy['pharmacy_name']) ?></h1>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <a href="index.php">Pharmacies</a>
                    <span>/</span>
                    <span>Approve</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php">Back to list</a>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="panel">
                <h3 class="panel-title">Pharmacy details</h3>
                <div class="info-list">
                    <?php 
                    $fields = [
                        'Pharmacy name' => $pharmacy['pharmacy_name'],
                        'Owner'         => $pharmacy['owner_name'],
                        'Email'         => $pharmacy['email'],
                        'Phone'         => $pharmacy['phone'],
                        'License'       => $pharmacy['license_number'],
                        'Neighborhood'  => $pharmacy['neighborhood_name'] ?? '—',
                        'Address'       => $pharmacy['address'],
                        'Hours'         => $pharmacy['operating_hours'],
                        'Status'        => ucfirst($pharmacy['status']),
                    ];
                    foreach ($fields as $label => $val): ?>
                        <div class="info-row" style="display:flex;justify-content:space-between;border-bottom:1px solid #eee;padding:10px 0">
                            <strong><?= $label ?></strong>
                            <span><?= h($val) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-card" style="margin-top:2rem">
                    <h3>Decision</h3>
                    <form action="approve.php" method="post">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                        <div class="form-group">
                            <label for="reason">Reason (for rejection/suspension)</label>
                            <textarea id="reason" name="reason" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="form-footer">
                            <button class="btn btn-primary" type="submit" name="action" value="approve">Approve registration</button>
                            <button class="btn btn-danger" type="submit" name="action" value="reject">Reject / Suspend</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
                                <span>Submitted</span>
                                <span>Yesterday</span>
                            </div>
                      
                        </div>
                    </div>

                    <div class="panel">
                        <h3 class="panel-title">Approval actions</h3>
                        <form action="approve.php?id=<?= (int)$id ?>" method="post">
                            <div class="form-group">
                                <label for="approval-note">Approval note (optional)</label>
                                <textarea id="approval-note" name="reason" placeholder="Add a note for the pharmacy"></textarea>
                            </div>
                            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= csrf_token() ?>">
                            <input type="hidden" name="id" value="<?= (int)$id ?>">
                            <div class="form-footer">
                                <button class="btn btn-primary" type="submit" name="action" value="approve">Approve pharmacy</button>
                                <button class="btn btn-secondary" type="submit" name="action" value="reject">Reject application</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <aside class="sidebar">
                <h3 class="panel-title">Verification checklist</h3>
                <div class="check-list">
                    <label><input type="checkbox" name="checklist[]" value="license"> License number verified</label>
                    <label><input type="checkbox" name="checklist[]" value="phone"> Phone number confirmed</label>
                    <label><input type="checkbox" name="checklist[]" value="neighborhood"> Neighborhood matched</label>
                </div>
            </aside>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
