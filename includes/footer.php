<?php
if (!isset($extra_js) || !is_array($extra_js)) {
    $extra_js = array();
}
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>index.html">MedFinder</a>
            <p>Find nearby pharmacies and live medicine availability across Addis Ababa.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>search-results.html">Search results</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/register.php">Register pharmacy</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>about.html">About MedFinder</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Support</h4>
            <ul>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>contact.html">Contact support</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/login.php">Pharmacy login</a></li>
                <li><a href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>admin/login.php">Admin access</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Contact</h4>
            <p>Call: <a href="tel:+251912345678">+251 91 234 5678</a></p>
            <p>Email: <a href="mailto:hello@medfinder.et">hello@medfinder.et</a></p>
            <p>Hours: 7:00 AM - 10:00 PM</p>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>Copyright <?php echo date('Y'); ?> MedFinder Ethiopia</span>
            <span>Built for local pharmacies.</span>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
window.MedFinderConfig = {
    assetPath: '<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>'
};
</script>
<script src="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>js/main.js"></script>
<?php foreach ($extra_js as $js_file) { ?>
    <script src="<?php echo htmlspecialchars($asset_path . $js_file, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php } ?>
</body>
</html>
