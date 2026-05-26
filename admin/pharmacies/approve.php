<?php
$page_title = 'Approve Pharmacy - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Pharmacy approvals</p>
                <h1 class="page-title">Review registration</h1>
                <p class="page-subtitle">Confirm details before approving the pharmacy account.</p>
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
                <div class="panel-stack">
                    <div class="panel">
                        <h3 class="panel-title">Registration details</h3>
                        <div class="info-list">
                            <div class="info-row">
                                <span>Pharmacy</span>
                                <span>BlueCross Pharmacy</span>
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
                        </div>
                    </div>

                    <div class="panel">
                        <h3 class="panel-title">Approval actions</h3>
                        <form action="index.html" method="post">
                            <div class="form-group">
                                <label for="approval-note">Approval note (optional)</label>
                                <textarea id="approval-note" name="note" placeholder="Add a note for the pharmacy"></textarea>
                            </div>
                            <div class="form-footer">
                                <button class="btn btn-primary" type="submit">Approve pharmacy</button>
                                <button class="btn btn-secondary" type="submit">Reject application</button>
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
