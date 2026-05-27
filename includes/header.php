<?php
if (!isset($page_title) || $page_title === '') {
    $page_title = 'MedFinder Ethiopia';
}
if (!isset($asset_path)) {
    $asset_path = '';
}
if (!isset($extra_css) || !is_array($extra_css)) {
    $extra_css = array();
}
if (!isset($body_class)) {
    $body_class = '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>css/style.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>css/responsive.css">
    <?php foreach ($extra_css as $css_file) { ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_path . $css_file, ENT_QUOTES, 'UTF-8'); ?>">
    <?php } ?>
</head>
<body<?php echo $body_class !== '' ? ' class="' . htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>index.php">MedFinder</a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">Menu</button>
        <nav class="site-nav" aria-label="Primary">
            <ul class="nav-list" id="primary-nav">
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>index.php">Home</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>search-results.php">Search</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>about.php">About</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/login.php">Pharmacy Login</a>
            <a class="btn btn-primary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/register.php">Register</a>
        </div>
    </div>
</header>
