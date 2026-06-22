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
            db_query(
                'DELETE FROM comments WHERE comment_id = :comment_id',
                [':comment_id' => $id]
            );
            flash('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            flash('error', 'Error deleting comment.');
        }
    } else {
        flash('error', 'Invalid request.');
    }
}

redirect('admin/comments/index.php');
