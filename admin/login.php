<?php
/**
 * admin/login.php — Admin Login Page + POST Handler
 *
 * FLOW:
 *  GET  → render the login form
 *  POST → validate CSRF → lookup user → verify password
 *         → regenerate session → redirect to dashboard
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

start_session();
debug_request('admin/login.php');

// If already logged in, skip the form
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.html');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CSRF check
    verify_csrf();

    // 2. Collect + sanitize inputs
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 3. Basic presence validation
    if ($username === '') $errors[] = 'Username is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (empty($errors)) {
        // 4. Lookup admin by username
        $admin = db_query(
            'SELECT admin_id, username, password FROM admins WHERE username = :u LIMIT 1',
            [':u' => $username]
        )->fetch();

        // 5. Verify password (intentionally vague error message)
        if (!$admin || !password_verify($password, $admin['password'])) {
            $errors[] = 'Invalid username or password.';
        } else {
            // 6. Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // 7. Store identity in session
            $_SESSION['admin_id']       = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];

            // 8. Redirect to dashboard
            flash('success', 'Welcome back, ' . $admin['username'] . '!');
            redirect('admin/index.html');
        }
    }

    // Store errors in session so they survive the page re-render
    if (!empty($errors)) {
        flash('error', implode(' ', $errors));
    }
}
$page_title = 'Admin Login - MedFinder Ethiopia';
$asset_path = '../';
$extra_css = array('css/admin.css');
$body_class = 'admin-body';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container auth-layout">
            <div class="auth-card">
                <div class="auth-header">
                    <p class="eyebrow">Admin access</p>
                    <h2>Sign in to the admin panel</h2>
                </div>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="admin-username">Username</label>
                        <input type="text" id="admin-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="admin-password">Password</label>
                        <input type="password" id="admin-password" name="password" required>
                    </div>
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-footer">
                        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                    </div>
                </form>
                <div class="auth-footer">
                    <a class="link" href="../index.html">Back to website</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
