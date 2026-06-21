<?php
// Database
define('DB_HOST', 'localhost');   
define('DB_NAME', 'medfinder');   
define('DB_USER', 'root');        
define('DB_PASS', 'Jeremy1997');           
define('DB_CHARSET', 'utf8mb4'); 

// Application base url
define('BASE_URL', 'http://localhost:8000/');


define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/pharmacy-logos/');


define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);

// Allowed MIME types for logo uploads
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Session
define('SESSION_NAME', 'mf_session');   
define('SESSION_LIFETIME', 7200);       

// Security
define('CSRF_TOKEN_NAME', '_csrf_token');

// Pagination 
define('ITEMS_PER_PAGE', 15);        

// Low-stock threshold 
define('LOW_STOCK_THRESHOLD', 10);     
