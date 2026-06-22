<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/admin-auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id = $_POST['id'] ?? null;
    if ($id) {
        try {
            $db = get_db_connection();
            $stmt = $db->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            set_flash('error', 'Error deleting comment.');
        }
    } else {
        set_flash('error', 'Invalid request.');
    }
}

redirect('admin/comments/index.php');
