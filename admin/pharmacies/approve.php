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
                    <a href="../index.html">Admin</a>
                    <span>/</span>
                    <a href="index.html">Pharmacies</a>
                    <span>/</span>
                    <span>Approve</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.html">Back to list</a>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container page-layout">
            <div class="content-area">
                <div class="panel">
                    <h3 class="panel-title">Pharmacy details</h3>
                    <div class="info-list">
                        <?php foreach ([
                            'Pharmacy name' => 'pharmacy_name',
                            'Owner'         => 'owner_name',
                            'Email'         => 'email',
                            'Phone'         => 'phone',
                            'License'       => 'license_number',
                            'Neighborhood'  => 'neighborhood_name',
                            'Address'       => 'address',
                            'Hours'         => 'operating_hours',
                            'Status'        => 'status',
                        ] as $label => $key): ?>
                            <div class="info-row">
                                <span><?= $label ?></span>
                                <span><?= h($pharmacy[$key] ?? '—') ?></span>
                            </div>
                            <div class="info-row">
                                <span>Owner</span>
                                <span>Tsegaye Bekele</span>
                            </div>
                            <div class="info-row">
                                <span>Phone</span>
                                <span>+251 91 123 4455</span>
                            </div>
                            <div class="info-row">
                                <span>License number</span>
                                <span>ET-00921</span>
                            </div>
                            <div class="info-row">
                                <span>Neighborhood</span>
                                <span>Yeka</span>
                            </div>
                            <div class="info-row">
                                <span>Submitted</span>
                                <span>Yesterday</span>
                            </div>
                        <?php endforeach; ?>
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
