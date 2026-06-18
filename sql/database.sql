-- Database Schema + Seed Data

CREATE DATABASE IF NOT EXISTS medfinder
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE medfinder;

-- TABLE1: admins
-- Stores the super-admin accounts that manage the platform.
CREATE TABLE IF NOT EXISTS admins (
    admin_id    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username    VARCHAR(50)     NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    email       VARCHAR(100)    NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (admin_id),
    UNIQUE KEY uq_admin_username (username),
    UNIQUE KEY uq_admin_email    (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE2: neighborhoods
-- Represents geographic sub-areas (Bole, Kirkos, etc.).
CREATE TABLE IF NOT EXISTS neighborhoods (
    neighborhood_id INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name            VARCHAR(100)  NOT NULL,
    description     TEXT,
    zone            VARCHAR(50)   DEFAULT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (neighborhood_id),
    UNIQUE KEY uq_neighborhood_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE3: pharmacies
-- Registered pharmacy accounts.
-- status flow: pending → active | suspended
CREATE TABLE IF NOT EXISTS pharmacies (
    pharmacy_id      INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    pharmacy_name    VARCHAR(200)   NOT NULL,
    owner_name       VARCHAR(100)   NOT NULL,
    email            VARCHAR(100)   NOT NULL,
    phone            VARCHAR(20)    NOT NULL,
    password         VARCHAR(255)   NOT NULL,
    address          TEXT           NOT NULL,
    neighborhood_id  INT UNSIGNED   DEFAULT NULL,
    license_number   VARCHAR(50)    NOT NULL,
    logo             VARCHAR(255)   DEFAULT NULL,
    operating_hours  VARCHAR(200)   DEFAULT NULL,
    latitude         DECIMAL(10,7)  DEFAULT NULL,
    longitude        DECIMAL(10,7)  DEFAULT NULL,
    rejection_reason TEXT           DEFAULT NULL,
    status           ENUM('pending','active','suspended') NOT NULL DEFAULT 'pending',
    created_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (pharmacy_id),
    UNIQUE KEY uq_pharmacy_email   (email),
    UNIQUE KEY uq_pharmacy_license (license_number),
    KEY idx_pharmacy_neighborhood  (neighborhood_id),
    KEY idx_pharmacy_status        (status),
    CONSTRAINT fk_pharmacy_neighborhood
        FOREIGN KEY (neighborhood_id)
        REFERENCES neighborhoods (neighborhood_id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE4: medicines
-- Master catalog of medicines managed by admins.
-- is_active = 0 soft-deletes a medicine (keeps history).
-- FULLTEXT index enables fast LIKE-style search on name/generic.
CREATE TABLE IF NOT EXISTS medicines (
    medicine_id   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    medicine_name VARCHAR(200)  NOT NULL,
    generic_name  VARCHAR(200)  DEFAULT NULL,
    category      VARCHAR(100)  DEFAULT NULL,
    description   TEXT          DEFAULT NULL,
    is_active     TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (medicine_id),
    KEY idx_medicine_active   (is_active),
    KEY idx_medicine_category (category),
    FULLTEXT KEY ft_medicine_name (medicine_name, generic_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE5: inventory
-- Junction table: which pharmacy stocks which medicine.
CREATE TABLE IF NOT EXISTS inventory (
    inventory_id  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    pharmacy_id   INT UNSIGNED    NOT NULL,
    medicine_id   INT UNSIGNED    NOT NULL,
    quantity      INT             NOT NULL DEFAULT 0,
    price         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    status        ENUM('in_stock','limited','out_of_stock') NOT NULL DEFAULT 'in_stock',
    expiry_date   DATE            DEFAULT NULL,
    restock_note  VARCHAR(255)   DEFAULT NULL,
    notes         TEXT           DEFAULT NULL,
    updated_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (inventory_id),
    UNIQUE KEY uq_pharmacy_medicine (pharmacy_id, medicine_id),
    KEY idx_inventory_status     (status),
    KEY idx_inventory_updated    (updated_at),
    CONSTRAINT fk_inventory_pharmacy
        FOREIGN KEY (pharmacy_id)
        REFERENCES pharmacies (pharmacy_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_inventory_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines (medicine_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TABLE6: search_logs
-- Stores every search for analytics.
CREATE TABLE IF NOT EXISTS search_logs (
    log_id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    customer_id     INT UNSIGNED  DEFAULT NULL,
    search_query    VARCHAR(200)  NOT NULL,
    neighborhood_id INT UNSIGNED  DEFAULT NULL,
    status_filter   VARCHAR(20)   DEFAULT NULL,
    results_count   INT           NOT NULL DEFAULT 0,
    ip_hash         VARCHAR(64)   DEFAULT NULL,
    search_date     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id),
    KEY idx_log_date         (search_date),
    KEY idx_log_neighborhood (neighborhood_id),
    KEY idx_log_query        (search_query(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  SEED DATA
-- Admin account
INSERT INTO admins (username, email, password) VALUES
('admin', 'admin@medfinder.et',
 'PLACEHOLDER_ADMIN_HASH')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- Neighborhoods (5 main Addis Ababa sub-cities)
INSERT INTO neighborhoods (name, description, zone) VALUES
('Bole',    'Diplomatic and commercial hub in eastern Addis', 'East'),
('Kirkos',  'Central business district near Meskel Square',   'Central'),
('Arada',   'Historic city centre, Piazza area',              'Central'),
('Yeka',    'Residential area in the northeast',              'East'),
('Lideta',  'Western residential and commercial zone',        'West')
ON DUPLICATE KEY UPDATE description = VALUES(description), zone = VALUES(zone);


-- Medicine catalog (20 common medicines)
-- Use INSERT IGNORE for medicines to avoid duplicating the catalog
INSERT IGNORE INTO medicines (medicine_name, generic_name, category, description) VALUES
('Insulin Rapid',        'Insulin (short-acting)',          'Diabetes',      'Fast-acting insulin for blood sugar control.'),
('Insulin NPH',          'Insulin (intermediate-acting)',   'Diabetes',      'Intermediate-acting insulin injection.'),
('Metformin 500mg',      'Metformin HCl',                  'Diabetes',      'First-line oral medication for type 2 diabetes.'),
('Amoxicillin 500mg',    'Amoxicillin',                    'Antibiotic',    'Broad-spectrum antibiotic for bacterial infections.'),
('Amoxicillin 250mg',    'Amoxicillin',                    'Antibiotic',    'Lower-dose amoxicillin, suitable for children.'),
('Paracetamol 500mg',    'Acetaminophen',                  'Painkiller',    'Common analgesic and antipyretic.'),
('Ibuprofen 400mg',      'Ibuprofen',                      'Painkiller',    'NSAID for pain and inflammation.'),
('Ciprofloxacin 500mg',  'Ciprofloxacin',                  'Antibiotic',    'Fluoroquinolone antibiotic for serious infections.'),
('Omeprazole 20mg',      'Omeprazole',                     'Gastrointestinal', 'Proton pump inhibitor for acid reflux.'),
('Atenolol 50mg',        'Atenolol',                       'Cardiovascular','Beta-blocker for hypertension and angina.'),
('Amlodipine 5mg',       'Amlodipine',                     'Cardiovascular','Calcium channel blocker for high blood pressure.'),
('Lisinopril 10mg',      'Lisinopril',                     'Cardiovascular','ACE inhibitor for hypertension and heart failure.'),
('Diazepam 5mg',         'Diazepam',                       'Neurological',  'Benzodiazepine for anxiety and muscle spasms.'),
('Cetirizine 10mg',      'Cetirizine HCl',                 'Allergy',       'Non-drowsy antihistamine for allergic reactions.'),
('Salbutamol Inhaler',   'Salbutamol (Albuterol)',          'Respiratory',   'Bronchodilator inhaler for asthma relief.'),
('Dexamethasone 4mg',    'Dexamethasone',                  'Steroid',       'Corticosteroid for inflammation and allergies.'),
('ORS Sachets',          'Oral Rehydration Salts',         'Gastrointestinal', 'Electrolyte powder for dehydration treatment.'),
('Vitamin C 1000mg',     'Ascorbic Acid',                  'Supplement',    'High-dose vitamin C supplement.'),
('Folic Acid 5mg',       'Folic Acid',                     'Supplement',    'B-vitamin for pregnancy support.'),
('Cotrimoxazole 480mg',  'Trimethoprim/Sulfamethoxazole',  'Antibiotic',    'Combination antibiotic for UTI and respiratory infections.');


-- Sample pharmacies (passwords: pharmacy123)
INSERT INTO pharmacies
    (pharmacy_name, owner_name, email, phone, password, address, neighborhood_id,
     license_number, operating_hours, status)
VALUES
(
    'Unity Pharmacy',
    'Hana Mulu',
    'unity@pharmacy.et',
    '+251912345678',
    'PLACEHOLDER_UNITY_HASH',
    'Atlas area, near Bole Atlas Hotel, Bole Sub-city',
    1,
    'ETH-PH-001',
    '8:00 AM - 9:00 PM',
    'active'
),
(
    'EthioCare Pharmacy',
    'Tsegaye Bekele',
    'ethiocare@pharmacy.et',
    '+251911223344',
    'PLACEHOLDER_ETHIOCARE_HASH',
    'Meskel Square area, Kirkos Sub-city',
    2,
    'ETH-PH-002',
    '8:00 AM - 8:00 PM',
    'active'
),
(
    'BlueCross Pharmacy',
    'Selamawit Girma',
    'bluecross@pharmacy.et',
    '+251900112233',
    'PLACEHOLDER_BLUECROSS_HASH',
    'Megenagna area, Yeka Sub-city',
    4,
    'ETH-PH-003',
    '24 hours',
    'active'
),
(
    'GreenMed Pharmacy',
    'Dereje Alemu',
    'greenmed@pharmacy.et',
    '+251922334455',
    'PLACEHOLDER_GREENMED_HASH',
    'Piazza area, Arada Sub-city',
    3,
    'ETH-PH-004',
    '8:00 AM - 7:00 PM',
    'pending'
)
ON DUPLICATE KEY UPDATE password = VALUES(password);


-- Sample inventory (Unity Pharmacy — pharmacy_id = 1)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(1, 1,  45, 250.00, 'in_stock'),    -- Insulin Rapid
(1, 3,   6, 180.00, 'limited'),     -- Metformin
(1, 4,  80, 120.00, 'in_stock'),    -- Amoxicillin 500
(1, 6, 200,  25.00, 'in_stock'),    -- Paracetamol
(1, 7,  30,  55.00, 'in_stock'),    -- Ibuprofen
(1, 9,  20,  95.00, 'in_stock'),    -- Omeprazole
(1,14,  50,  40.00, 'in_stock'),    -- Cetirizine
(1,15,   0, 320.00, 'out_of_stock');-- Salbutamol Inhaler

-- Sample inventory (EthioCare Pharmacy — pharmacy_id = 2)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(2, 1,  12, 260.00, 'limited'),     -- Insulin Rapid
(2, 2,  25, 310.00, 'in_stock'),    -- Insulin NPH
(2, 4,  60, 115.00, 'in_stock'),    -- Amoxicillin 500
(2, 6, 150,  22.00, 'in_stock'),    -- Paracetamol
(2,10,  35, 120.00, 'in_stock'),    -- Atenolol
(2,11,  28, 140.00, 'in_stock'),    -- Amlodipine
(2,17, 100,  18.00, 'in_stock');    -- ORS Sachets

-- Sample inventory (BlueCross Pharmacy — pharmacy_id = 3)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status, restock_note) VALUES
(3, 1,   0, 255.00, 'out_of_stock', 'Restocking tomorrow'),
(3, 3,  40, 175.00, 'in_stock',     NULL),
(3, 8,  22, 210.00, 'in_stock',     NULL),    -- Ciprofloxacin
(3,12,  18, 165.00, 'in_stock',     NULL),    -- Lisinopril
(3,15,  15, 330.00, 'in_stock',     NULL),    -- Salbutamol Inhaler
(3,18, 300,  35.00, 'in_stock',     NULL),    -- Vitamin C
(3,19, 200,  28.00, 'in_stock',     NULL);    -- Folic Acid


-- TABLE7: customers
-- Registered customer accounts.
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email       VARCHAR(100) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    phone       VARCHAR(20)  DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (customer_id),
    UNIQUE KEY uq_customer_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE8: customer_favorites
-- Junction table: which customer favorited which pharmacy.
CREATE TABLE IF NOT EXISTS customer_favorites (
    favorite_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_id INT UNSIGNED NOT NULL,
    pharmacy_id INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (favorite_id),
    UNIQUE KEY uq_customer_pharmacy (customer_id, pharmacy_id),
    CONSTRAINT fk_favorite_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers (customer_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_favorite_pharmacy
        FOREIGN KEY (pharmacy_id)
        REFERENCES pharmacies (pharmacy_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE9: pharmacy_reviews
-- Customer ratings and comments for pharmacies.
CREATE TABLE IF NOT EXISTS pharmacy_reviews (
    review_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    pharmacy_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL,
    comment     TEXT,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (review_id),
    UNIQUE KEY uq_customer_pharmacy_review (customer_id, pharmacy_id),
    CONSTRAINT fk_review_pharmacy FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id) ON DELETE CASCADE,
    CONSTRAINT fk_review_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE10: notifications
-- System alerts for customers.
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_id     INT UNSIGNED NOT NULL,
    title           VARCHAR(200) NOT NULL,
    message         TEXT NOT NULL,
    type            ENUM('stock_alert', 'new_medicine', 'pharmacy_update', 'general') DEFAULT 'general',
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (notification_id),
    CONSTRAINT fk_notification_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample customer (password: password123)
-- Hash will be updated by setup.php
INSERT INTO customers (first_name, last_name, email, password, phone) VALUES
('Demo', 'Customer', 'customer@test.et', 'PLACEHOLDER_CUSTOMER_HASH', '+251900000000')
ON DUPLICATE KEY UPDATE first_name = VALUES(first_name);

-- Sample reviews
INSERT INTO pharmacy_reviews (pharmacy_id, customer_id, rating, comment) VALUES
(1, 1, 5, 'Excellent service and they always have what I need!'),
(2, 1, 4, 'Friendly staff, but sometimes there is a wait.')
ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment);

-- Sample notifications
INSERT INTO notifications (customer_id, title, message, type) VALUES
(1, 'Stock Update', 'Insulin Rapid is now back in stock at Unity Pharmacy!', 'stock_alert'),
(1, 'Welcome', 'Welcome to MedFinder Ethiopia! Start searching for medicines nearby.', 'general')
ON DUPLICATE KEY UPDATE title = VALUES(title), message = VALUES(message);
