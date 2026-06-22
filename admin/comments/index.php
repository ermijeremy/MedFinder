<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin-auth.php';

$db = get_db_connection();
$stmt = $db->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $stmt->fetchAll();

$page_title = 'Comments - Admin';
$asset_path = '../../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Contact management</p>
                <h1 class="page-title">User Comments</h1>
                <p class="page-subtitle">View and manage messages sent through the contact form.</p>
                <div class="breadcrumb">
                    <a href="../index.php">Admin</a>
                    <span>/</span>
                    <span>Comments</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="../index.php">Back to dashboard</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php render_flashes(); ?>
            <div class="results-toolbar">
                <span class="pill"><?= count($comments) ?> comments</span>
            </div>

            <div class="table-wrap" style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Topic</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($comments)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;padding:2rem">No comments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($comments as $c): ?>
                                <tr>
                                    <td style="white-space: nowrap;"><?= h(date('M j, Y H:i', strtotime($c['created_at']))) ?></td>
                                    <td><?= h($c['name']) ?></td>
                                    <td><a href="mailto:<?= h($c['email']) ?>"><?= h($c['email']) ?></a></td>
                                    <td><?= h($c['phone'] ?? '—') ?></td>
                                    <td><?= h(ucfirst($c['topic'] ?? '—')) ?></td>
                                    <td style="max-width: 300px;"><?= nl2br(h($c['message'])) ?></td>
                                    <td class="table-actions">
                                        <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete this comment?')">
                                            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= (int)$c['comment_id'] ?>">
                                            <button class="btn btn-link" style="color: #dc3545;" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<?php include '../../includes/footer.php'; ?>
