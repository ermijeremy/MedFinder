<?php
require_once __DIR__ . '/config.php';

//  SESSION MANAGEMENT
//  Start the application session exactly once.
function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),  
            'httponly' => true,                       // No JS access to cookie
            'samesite' => 'Lax',                     // CSRF mitigation
        ]);
        session_start();
    }
}

//  CSRF PROTECTION
//  Generate (or retrieve) the session CSRF token.
function csrf_token(): string
{
    start_session();
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify the CSRF token submitted with a POST form.
// Kills the request immediately if the token is missing or wrong.
function verify_csrf(): void
{
    start_session();
    $submitted = $_POST[CSRF_TOKEN_NAME] ?? '';
    $expected  = $_SESSION[CSRF_TOKEN_NAME] ?? '';


    if (!hash_equals($expected, $submitted)) {
        http_response_code(403);
        echo "Submitted: $submitted, Expected: $expected";
        die('Invalid request. Please go back and try again.');
    }
}

//  FLASH MESSAGES
//  Store a one-time flash message in the session.
function flash(string $key, string $msg): void
{
    start_session();
    $_SESSION['_flash'][$key] = $msg;
}

//  Retrieve and clear a flash message.
//  Returns null if the key doesn't exist.
function get_flash(string $key): ?string
{
    start_session();
    if (isset($_SESSION['_flash'][$key])) {
        $msg = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
    return null;
}

// Render all flash messages as HTML alert divs.
function render_flashes(): void
{
    start_session();
    if (empty($_SESSION['_flash'])) return;

    $map = [
        'success' => 'alert-success',
        'error'   => 'alert-error',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
    ];

    foreach ($_SESSION['_flash'] as $key => $msg) {
        $cls = $map[$key] ?? 'alert-info';
        echo '<div class="alert ' . $cls . '">' . h($msg) . '</div>';
    }

    unset($_SESSION['_flash']);
}


// OUTPUT SANITIZATION
// Short alias for htmlspecialchars().
function h($val): string
{
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize(string $str): string
{
    return trim(strip_tags($str));
}

// Detect JSON requests (Postman or AJAX) for API-friendly responses.
function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xhr    = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains($accept, 'application/json') || $xhr === 'XMLHttpRequest';
}

// Emit a JSON response and stop execution.
function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

// Compute base URL for redirects, with safe fallback for the PHP built-in server.
function base_url(): string
{
    if (PHP_SAPI !== 'cli-server' && defined('BASE_URL') && BASE_URL !== '') {
        return rtrim(BASE_URL, '/') . '/';
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $dir    = preg_replace('#/(admin|pharmacy|customer)(/.*)?$#', '', $dir);
    $dir    = $dir === '/' ? '' : $dir;

    return $scheme . '://' . $host . $dir . '/';
}

// REQUEST DEBUG LOGGER
// Logs request context for tracing handler-level issues.
function debug_request(string $handler, array $extra = []): void
{
    $files = [];
    foreach ($_FILES as $field => $meta) {
        if (is_array($meta) && isset($meta['name'])) {
            $files[$field] = [
                'name'  => $meta['name'],
                'size'  => $meta['size'] ?? null,
                'error' => $meta['error'] ?? null,
            ];
        }
    }

    $payload = [
        'time'      => date('c'),
        'handler'   => $handler,
        'method'    => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
        'uri'       => $_SERVER['REQUEST_URI'] ?? '',
        'query'     => $_GET,
        'post'      => $_POST,
        'files'     => $files,
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
        'admin_id'  => $_SESSION['admin_id'] ?? null,
        'pharmacy_id' => $_SESSION['pharmacy_id'] ?? null,
        'extra'     => $extra,
    ];

    error_log('[REQUEST_DEBUG] ' . json_encode($payload, JSON_UNESCAPED_SLASHES));
}

//  REDIRECTS
//  Redirect to a URL and stop execution.
function redirect(string $path): void
{
    // If it's already a full URL, use it directly
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . base_url() . ltrim($path, '/'));
    }
    exit;
}


//  FORMATTING HELPERS
//  Returns a human-friendly "time ago" string.
function time_ago(string $datetime): string
{
    $now  = new DateTimeImmutable();
    $then = new DateTimeImmutable($datetime);
    $diff = $now->diff($then);

    if ($diff->days === 0) {
        if ($diff->h === 0) {
            $m = $diff->i;
            return $m <= 1 ? 'Just now' : "$m minutes ago";
        }
        $h = $diff->h;
        return $h === 1 ? '1 hour ago' : "$h hours ago";
    }
    if ($diff->days === 1) return 'Yesterday';
    if ($diff->days < 7)  return $diff->days . ' days ago';

    return $then->format('d M Y');
}

//  Maps an inventory status string to its CSS badge class.
function badge_class(string $status): string
{
    return match ($status) {
        'in_stock'     => 'badge-success',
        'limited'      => 'badge-warning',
        'out_of_stock' => 'badge-danger',
        default        => '',
    };
}

// Maps an inventory status to a human label.
function status_label(string $status): string
{
    return match ($status) {
        'in_stock'     => 'In stock',
        'limited'      => 'Limited',
        'out_of_stock' => 'Out of stock',
        default        => ucfirst(str_replace('_', ' ', $status)),
    };
}

// Format a price in ETB.
function format_price(float $amount): string
{
    return number_format($amount, 2) . ' ETB';
}


//  PAGINATION
// Build a pagination array used by templates to render page links.
function paginate(int $total, int $per_page = ITEMS_PER_PAGE, int $page = 1): array
{
    $total_pages = max(1, (int)ceil($total / $per_page));
    $current     = max(1, min($page, $total_pages));
    $offset      = ($current - 1) * $per_page;

    return [
        'total'       => $total,
        'total_pages' => $total_pages,
        'current'     => $current,
        'per_page'    => $per_page,
        'offset'      => $offset,
    ];
}

//  INPUT VALIDATION HELPERS
//  Check if a string looks like a valid Ethiopian phone number.
function is_valid_ethiopian_phone(string $phone): bool
{
    $phone = preg_replace('/\s+/', '', $phone);
    return (bool)preg_match('/^(\+2519\d{8}|09\d{8}|9\d{8})$/', $phone);
}

//  Check password strength: at least 8 characters.
function is_strong_password(string $password): bool
{
    return strlen($password) >= 8;
}

//  FILE UPLOAD HELPER
//  Handle a pharmacy logo file upload.
function upload_logo(array $file): ?string
{
    // No file chosen — this field is optional
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed with error code: ' . $file['error']);
    }

    // Size check
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('Logo file is too large. Maximum size is 2 MB.');
    }

    // MIME type check (use finfo, not $_FILES['type'] which can be spoofed)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
        throw new RuntimeException('Invalid file type. Only JPEG, PNG, and WebP are allowed.');
    }

    // Build a safe filename: UUID + original extension
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);

    // Ensure upload directory exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $dest = UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Could not save the uploaded file. Check server permissions.');
    }

    return $filename;
}
