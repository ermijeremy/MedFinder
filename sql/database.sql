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
('Bole',             'Diplomatic and commercial hub in eastern Addis',         'East'),
('Kirkos',           'Central business district near Meskel Square',           'Central'),
('Arada',            'Historic city centre, Piazza area',                      'Central'),
('Yeka',             'Residential area in the northeast',                      'East'),
('Lideta',           'Western residential and commercial zone',                'West'),
('Addis Ketema',     'Known for the Merkato market area',                      'Central'),
('Akaky Kaliti',     'Industrial hub in the southern periphery',               'South'),
('Gullele',          'Northern residential area with hilly terrain',           'North'),
('Kolfe Keranio',    'Large residential zone in the west',                     'West'),
('Nifas Silk-Lafto', 'Southern residential and expanding business area',       'South'),
('Lemi-Kura',        'Newly established district spanning Ayat and CMC areas', 'East')
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
     license_number, logo, operating_hours, status, latitude, longitude)
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
    'uploads/pharmacy-logos/unity_pharmacy.png',
    '8:00 AM - 9:00 PM',
    'active',
    8.9950, 38.7885
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
    'uploads/pharmacy-logos/ethiocare_pharmacy.png',
    '8:00 AM - 8:00 PM',
    'active',
    9.0105, 38.7610
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
    'uploads/pharmacy-logos/bluecross_pharmacy.png',
    '24 hours',
    'active',
    9.0185, 38.8020
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
    'uploads/pharmacy-logos/greenmed_pharmacy.png',
    '8:00 AM - 7:00 PM',
    'pending',
    9.0305, 38.7525
),
(
    'Milo Pharmacy',
    'Fitsum Abebe',
    'contact@milopharmacy.et',
    '+251928222228',
    'PLACEHOLDER_MILO_HASH',
    'Summit area, Yeka Sub-city',
    4,
    'ETH-PH-005',
    'uploads/pharmacy-logos/milo_pharmacy.png',
    '8:30 AM - 10:00 PM',
    'active',
    9.0250, 38.8350
),
(
    'Gishen Pharmacy',
    'Aster Kebede',
    'info@gishen.et',
    '+251911765544',
    'PLACEHOLDER_GISHEN_HASH',
    'Arada Sub-city, Piazza',
    3,
    'ETH-PH-006',
    'uploads/pharmacy-logos/gishen_pharmacy.png',
    '8:00 AM - 9:00 PM',
    'active',
    9.0320, 38.7500
),
(
    'Droga Pharmacy',
    'Yosef Tadesse',
    'service@drogapharmacy.com',
    '+251971719898',
    'PLACEHOLDER_DROGA_HASH',
    'Bole Sub-city, near Olympia',
    1,
    'ETH-PH-007',
    'uploads/pharmacy-logos/droga_pharmacy.png',
    '24 hours',
    'active',
    9.0020, 38.7750
),
(
    'Canaan Pharmacy',
    'Senait Worku',
    'info@canaanpharmacy.com.et',
    '+251909196651',
    'PLACEHOLDER_CANAAN_HASH',
    'Summit Safari, Yeka Sub-city',
    4,
    'ETH-PH-008',
    'uploads/pharmacy-logos/canaan_pharmacy.png',
    '8:00 AM - 10:00 PM',
    'active',
    9.0300, 38.8400
),
(
    'Sunshine Pharmacy',
    'Dawit Solomon',
    'sunshine@pharmacy.et',
    '+251911554433',
    'PLACEHOLDER_SUNSHINE_HASH',
    'Gotera, Nifas Silk-Lafto Sub-city',
    10,
    'ETH-PH-009',
    'uploads/pharmacy-logos/sunshine_pharmacy.png',
    '8:00 AM - 8:00 PM',
    'active',
    8.9800, 38.7650
),
(
    'Merkato Health Pharmacy',
    'Hagos Tekle',
    'merkato@health.et',
    '+251914332211',
    'PLACEHOLDER_MERKATO_HASH',
    'Addis Ketema Sub-city',
    6,
    'ETH-PH-010',
    'uploads/pharmacy-logos/merkato_health_pharmacy.png',
    '8:00 AM - 7:00 PM',
    'active',
    9.0350, 38.7400
),
(
    'Akaky Community Pharmacy',
    'Fikirte Zenebe',
    'akaky@pharmacy.et',
    '+251912998877',
    'PLACEHOLDER_AKAKY_HASH',
    'Akaky Kaliti Sub-city',
    7,
    'ETH-PH-011',
    'uploads/pharmacy-logos/akakyCommunity_pharmacy.png',
    '8:00 AM - 8:00 PM',
    'active',
    8.8800, 38.7900
),
(
    'Ayat City Pharmacy',
    'Samuel Negash',
    'ayat@citypharm.et',
    '+251911889900',
    'PLACEHOLDER_AYAT_HASH',
    'Ayat 2, near Condominiums, Lemi-Kura Sub-city',
    11,
    'ETH-PH-012',
    'uploads/pharmacy-logos/ayatCity.png',
    '8:00 AM - 10:00 PM',
    'active',
    9.0450, 38.8650
),
(
    'CMC Care Pharmacy',
    'Betelhem Tesfaye',
    'info@cmccare.et',
    '+251933445566',
    'PLACEHOLDER_CMC_HASH',
    'CMC Road, near St. Michael Church, Lemi-Kura Sub-city',
    11,
    'ETH-PH-013',
    'uploads/pharmacy-logos/cmcCare.png',
    '24 hours',
    'active',
    9.0380, 38.8520
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
(1,15,   0, 320.00, 'out_of_stock')-- Salbutamol Inhaler
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (EthioCare Pharmacy — pharmacy_id = 2)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(2, 1,  12, 260.00, 'limited'),     -- Insulin Rapid
(2, 2,  25, 310.00, 'in_stock'),    -- Insulin NPH
(2, 4,  60, 115.00, 'in_stock'),    -- Amoxicillin 500
(2, 6, 150,  22.00, 'in_stock'),    -- Paracetamol
(2,10,  35, 120.00, 'in_stock'),    -- Atenolol
(2,11,  28, 140.00, 'in_stock'),    -- Amlodipine
(2,17, 100,  18.00, 'in_stock')    -- ORS Sachets
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (BlueCross Pharmacy — pharmacy_id = 3)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status, restock_note) VALUES
(3, 1,   0, 255.00, 'out_of_stock', 'Restocking tomorrow'),
(3, 3,  40, 175.00, 'in_stock',     NULL),
(3, 8,  22, 210.00, 'in_stock',     NULL),    -- Ciprofloxacin
(3,12,  18, 165.00, 'in_stock',     NULL),    -- Lisinopril
(3,15,  15, 330.00, 'in_stock',     NULL),    -- Salbutamol Inhaler
(3,18, 300,  35.00, 'in_stock',     NULL),    -- Vitamin C
(3,19, 200,  28.00, 'in_stock',     NULL)    -- Folic Acid
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (GreenMed Pharmacy — pharmacy_id = 4)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(4, 5,  40, 95.00,  'in_stock'),    -- Amoxicillin 250
(4, 6, 250, 20.00,  'in_stock'),    -- Paracetamol
(4, 9,  30, 90.00,  'in_stock'),    -- Omeprazole
(4, 16, 15, 450.00, 'limited'),     -- Dexamethasone
(4, 20, 20, 280.00, 'in_stock')    -- Cotrimoxazole
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Milo Pharmacy — pharmacy_id = 5)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(5, 7,  50, 50.00,  'in_stock'),    -- Ibuprofen
(5, 14, 60, 42.00,  'in_stock'),    -- Cetirizine
(5, 18, 150, 35.00, 'in_stock'),    -- Vitamin C
(5, 19, 120, 30.00, 'in_stock')    -- Folic Acid
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Gishen Pharmacy — pharmacy_id = 6)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(6, 6, 500, 22.00,  'in_stock'),    -- Paracetamol
(6, 7, 100, 55.00,  'in_stock'),    -- Ibuprofen
(6, 17, 200, 15.00, 'in_stock')    -- ORS Sachets
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Droga Pharmacy — pharmacy_id = 7)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(7, 1, 20, 265.00, 'in_stock'),     -- Insulin Rapid
(7, 3, 30, 185.00, 'in_stock'),     -- Metformin
(7, 15, 8, 335.00, 'limited')      -- Salbutamol Inhaler
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Canaan Pharmacy — pharmacy_id = 8)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(8, 8, 25, 220.00, 'in_stock'),     -- Ciprofloxacin
(8, 12, 40, 170.00, 'in_stock'),    -- Lisinopril
(8, 20, 35, 290.00, 'in_stock')    -- Cotrimoxazole
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Sunshine Pharmacy — pharmacy_id = 9)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(9, 4, 100, 125.00, 'in_stock'),    -- Amoxicillin 500
(9, 13, 20, 480.00, 'limited'),     -- Diazepam
(9, 14, 80, 45.00,  'in_stock')    -- Cetirizine
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);

-- Sample inventory (Merkato Health Pharmacy — pharmacy_id = 10)
INSERT INTO inventory (pharmacy_id, medicine_id, quantity, price, status) VALUES
(10, 6, 400, 20.00, 'in_stock'),    -- Paracetamol
(10, 7, 200, 50.00, 'in_stock'),    -- Ibuprofen
(10, 17, 300, 12.00, 'in_stock')   -- ORS Sachets
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), status = VALUES(status);


-- TABLE7: customers
-- Registered customer accounts.
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email       VARCHAR(100) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    phone       VARCHAR(20)  DEFAULT NULL,
    photo       VARCHAR(255) DEFAULT NULL,
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
('Demo', 'Customer', 'customer@test.et', 'PLACEHOLDER_CUSTOMER_HASH', '+251900000000'),
('Alemu', 'Tesfaye', 'alemu@test.et', 'PLACEHOLDER_CUSTOMER_HASH', '+251900000001')
ON DUPLICATE KEY UPDATE first_name = VALUES(first_name);

-- Sample reviews
INSERT INTO pharmacy_reviews (pharmacy_id, customer_id, rating, comment) VALUES
(1, 1, 5, 'Excellent service and they always have what I need!'),
(2, 1, 4, 'Friendly staff, but sometimes there is a wait.'),
(3, 2, 3, 'Good prices but the pharmacy is a bit far from me.'),
(1, 2, 4, 'Great location and helpful pharmacists.'),
(4, 2, 2, 'Had an issue with a prescription but they resolved it.')
ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment);

-- Sample notifications
INSERT INTO notifications (customer_id, title, message, type) VALUES
(1, 'Stock Update', 'Insulin Rapid is now back in stock at Unity Pharmacy!', 'stock_alert'),
(1, 'Welcome', 'Welcome to MedFinder Ethiopia! Start searching for medicines nearby.', 'general')
ON DUPLICATE KEY UPDATE title = VALUES(title), message = VALUES(message);
