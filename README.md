
## Folder Structure

```
medfinder-ethiopia/
│
├── index.html                         # Landing/Home page with search
├── search-results.html                # Medicine search results page
├── pharmacy-detail.html               # Individual pharmacy profile page
├── about.html                         # About the project
├── contact.html                       # Contact form
│
├── admin/                             # Admin panel section
│   ├── index.html                     # Admin dashboard
│   ├── login.php                      # Admin login
│   ├── logout.html                    # Admin logout screen
│   ├── medicines/
│   │   ├── index.html                 # List all medicines
│   │   ├── add.php                    # Add new medicine to catalog
│   │   ├── edit.php                   # Edit medicine details
│   │   ├── delete.php                 # Delete medicine
│   │
│   ├── pharmacies/
│   │   ├── index.html                 # List all pharmacies
│   │   ├── approve.php                # Approve pending pharmacies
│   │   ├── view.html                  # View pharmacy details
│   │   ├── delete.php                 # Delete pharmacy
│   │
│   └── neighborhoods/
│       ├── index.html                 # Manage neighborhoods/areas
│       ├── add.php                    # Add new area
│       ├── edit.php                   # Edit area
│       ├── delete.php                 # Delete area
│
├── pharmacy/                          # Pharmacy owner panel
│   ├── index.html                     # Pharmacy dashboard
│   ├── login.php                      # Pharmacy login
│   ├── register.php                   # Pharmacy registration
│   ├── logout.html                    # Logout screen
│   ├── profile.php                    # Edit pharmacy profile
│   ├── inventory/
│   │   ├── index.html                 # View current inventory
│   │   ├── add.php                    # Add medicine to inventory
│   │   ├── update.php                 # Update stock/price
│   │   ├── delete.php                 # Remove from inventory
│
├── includes/                          # Reusable PHP files
│   ├── config.php                     # Database configuration
│   ├── db-connect.php                 # Database connection
│   ├── functions.php                  # Common functions
│   ├── header.php                     # Common header
│   ├── footer.php                     # Common footer
│   ├── admin-auth.php                 # Admin authentication check
│   ├── pharmacy-auth.php              # Pharmacy authentication check
│
├── css/                               # Stylesheets
│   ├── style.css                      # Main stylesheet
│   ├── admin.css                      # Admin panel styles
│   ├── pharmacy.css                   # Pharmacy panel styles
│   ├── responsive.css                 # Mobile responsive styles
│
├── js/                                # JavaScript files
│   ├── main.js                        # Main JavaScript
│   ├── search.js                      # Search functionality
│   ├── validation.js                  # Form validation
│
├── images/                            # Images
│   ├── logo.png
│   ├── default-pharmacy.jpg
│   └── icons/
│       ├── in-stock.png
│       ├── limited.png
│       ├── out-of-stock.png
│
├── uploads/                           # User uploaded files
│   └── pharmacy-logos/
│
└── sql/
    └── database.sql                   # Database schema
```

## Detailed Features Breakdown

### **1. Public-Facing Features (Patient/User Side)**

#### **A. Home Page (index.html)**
- **Hero Section**: Large search bar with placeholder "Search for medicine..."
- **Quick Search**: Auto-suggest dropdown using jQuery (searches as user types)
- **Featured Pharmacies**: Display 6-8 pharmacies with good ratings
- **How It Works**: 3-step visual guide (Search → Find → Call)
- **Statistics Counter**: Total medicines, pharmacies, neighborhoods

#### **B. Search Results (search-results.html)**
**Input Parameters:**
- Medicine name (required)
- Neighborhood filter (optional dropdown)
- Stock status filter (optional: All/In Stock/Limited)

**Display:**
- **Result Cards** (each showing):
  - Pharmacy name
  - Address with neighborhood badge
  - Phone number (click-to-call link: `tel:+251...`)
  - Stock status indicator (colored badge)
  - Price (if available)
  - "View Details" button
- **Filter Sidebar**: 
  - Neighborhood checkboxes
  - Stock status radio buttons
  - Apply/Reset buttons
- **Results Count**: "Found 12 pharmacies with Insulin in Bole"
- **No Results**: Helpful message with suggestions

#### **C. Pharmacy Detail Page (pharmacy-detail.html)**
- Full pharmacy profile
- Google Maps embed (if coordinates available)
- All available medicines list with prices
- Operating hours
- Contact information
- Directions/landmarks

---

### **2. Pharmacy Owner Panel**

#### **A. Registration (pharmacy/register.php)**
**Form Fields:**
- Pharmacy name*
- Owner name*
- Email*
- Phone number*
- Password* (confirm password)
- Address*
- Neighborhood (dropdown)*
- License number*
- Operating hours
- Upload logo (optional)

**Validation:**
- Email format check
- Phone number format (Ethiopian: +251 or 09)
- Password strength (min 8 chars)
- Unique email/phone
- Required fields marked

**Process:**
- Submit → Admin approval required → Email notification

#### **B. Login (pharmacy/login.php)**
- Email/Username + Password
- "Remember Me" checkbox
- Session management
- Redirect to dashboard after login
- "Forgot Password" link

#### **C. Dashboard (pharmacy/index.html)**
**Statistics Cards:**
- Total medicines in inventory
- Low stock alerts (< 10 units)
- Today's searches for your pharmacy
- Pending updates

**Quick Actions:**
- Add new medicine
- Update stock
- Edit profile
- View analytics

**Recent Activity:**
- Last 10 inventory updates
- Recent searches for your medicines

#### **D. Inventory Management (pharmacy/inventory/)**

**View Inventory (index.html):**
- Data table with columns:
  - Medicine name
  - Category
  - Quantity
  - Price
  - Status (color-coded)
  - Last updated
  - Actions (Edit/Delete)
- Search/filter within inventory
- Export to Excel/PDF (optional)
- Pagination

**Add Medicine (add.php):**
- Medicine dropdown (from admin catalog)
- Quantity (number)
- Price (Birr)
- Status (In Stock/Limited/Out of Stock)
- Expiry date (optional)
- Notes (optional)

**Update Stock (update.php):**
- Quick update form
- Adjust quantity (+/-)
- Change price
- Update status
- Timestamp tracking

#### **E. Profile Management (pharmacy/profile.php)**
- Edit all registration details
- Change password
- Upload/change logo
- Update operating hours
- Add social media links

---

### **3. Admin Panel**

#### **A. Login (admin/login.php)**
- Username + Password
- Session with timeout (30 min)
- Single admin account or multiple roles

#### **B. Dashboard (admin/index.html)**
**Statistics Overview:**
- Total pharmacies (Active/Pending)
- Total medicines in catalog
- Total searches today/week/month
- System health indicators

**Charts (using Chart.js or Google Charts):**
- Pharmacies by neighborhood (bar chart)
- Most searched medicines (pie chart)
- Daily search trends (line graph)

**Pending Tasks:**
- Pharmacy approvals
- Reported issues
- Low stock alerts

#### **C. Medicine Catalog Management (admin/medicines/)**

**List Medicines (index.html):**
- Data table with:
  - Medicine name
  - Generic name
  - Category
  - Number of pharmacies stocking
  - Actions (Edit/Delete)
- Add new medicine button
- Search and filter
- Alphabetical sorting

**Add Medicine (add.php):**
- Medicine name (brand)*
- Generic name
- Category (dropdown: Antibiotic, Painkiller, Diabetes, etc.)
- Description
- Typical uses
- Standard dosage info

**Edit/Delete:**
- Update details
- Merge duplicates
- Archive instead of delete (data integrity)

#### **D. Pharmacy Management (admin/pharmacies/)**

**List Pharmacies (index.html):**
- Filter by: Status (All/Active/Pending/Suspended)
- Table columns:
  - Pharmacy name
  - Owner
  - Phone
  - Neighborhood
  - Status
  - Registration date
  - Actions

**Approve Pharmacy (approve.php):**
- Review submitted details
- Verify license number
- Approve/Reject with reason
- Send email notification

**View Details (view.html):**
- Full profile
- Inventory list
- Activity log
- Suspend/Activate button

#### **E. Neighborhood Management (admin/neighborhoods/)**
- List all areas
- Add new (name, description)
- Edit/delete
- Assign to zone (optional: e.g., West, East, North, South)

---

### **4. Database Structure (sql/database.sql)**

**Tables:**

```sql
-- Users/Admins
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Neighborhoods
CREATE TABLE neighborhoods (
    neighborhood_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    zone VARCHAR(50)
);

-- Pharmacies
CREATE TABLE pharmacies (
    pharmacy_id INT PRIMARY KEY AUTO_INCREMENT,
    pharmacy_name VARCHAR(200) NOT NULL,
    owner_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255),
    address TEXT,
    neighborhood_id INT,
    license_number VARCHAR(50),
    logo VARCHAR(255),
    operating_hours TEXT,
    status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (neighborhood_id) REFERENCES neighborhoods(neighborhood_id)
);

-- Medicine Catalog
CREATE TABLE medicines (
    medicine_id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200),
    category VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pharmacy Inventory
CREATE TABLE inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    pharmacy_id INT,
    medicine_id INT,
    quantity INT DEFAULT 0,
    price DECIMAL(10,2),
    status ENUM('in_stock', 'limited', 'out_of_stock') DEFAULT 'in_stock',
    expiry_date DATE,
    notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id),
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id),
    UNIQUE KEY unique_pharmacy_medicine (pharmacy_id, medicine_id)
);

-- Search Logs (for analytics)
CREATE TABLE search_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    medicine_name VARCHAR(200),
    neighborhood_id INT,
    results_count INT,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **5. Key Technical Implementations**

#### **A. Search Functionality (jQuery + PHP)**
**Frontend (js/search.js):**
```javascript
// Auto-suggest with debouncing
$('#searchBox').on('keyup', function() {
    var query = $(this).val();
    if(query.length > 2) {
        $.ajax({
            url: 'includes/search-suggest.php',
            method: 'POST',
            data: {query: query},
            success: function(data) {
                $('#suggestions').html(data).show();
            }
        });
    }
});
```

**Backend (includes/search-suggest.php):**
- Query medicines table with LIKE
- Return HTML list of matching medicines
- Limit to 10 results

#### **B. Authentication System**
**Login Process:**
1. Validate input (email, password)
2. Hash password check (password_verify)
3. Create session variables
4. Set cookies if "Remember Me"
5. Redirect to dashboard

**Session Check (includes/pharmacy-auth.php):**
```php
session_start();
if(!isset($_SESSION['pharmacy_id'])) {
    header("Location: login.php");
    exit();
}
```

#### **C. Form Validation**
**Client-side (js/validation.js):**
- jQuery validation plugin
- Real-time field checking
- Custom error messages in Amharic if needed

**Server-side (PHP):**
- Never trust client validation
- Sanitize all inputs (mysqli_real_escape_string or prepared statements)
- Validate data types, lengths, formats
- Return errors in session or as URL parameters

#### **D. Security Measures**
- **SQL Injection**: Use prepared statements
- **XSS**: htmlspecialchars() on output
- **CSRF**: Token validation on forms
- **Password**: password_hash() and password_verify()
- **Session Hijacking**: Regenerate session ID after login
- **File Upload**: Validate type, size, rename files

---

### **6. Additional Features (If Time Permits)**

1. **Email Notifications**:
   - Registration confirmation
   - Approval status
   - Password reset

2. **Rating System**:
   - Users can rate pharmacy service
   - Display average rating

3. **Advanced Search**:
   - Filter by price range
   - Sort by distance, price, rating

4. **Mobile Responsiveness**:
   - Bootstrap or custom media queries
   - Touch-friendly buttons
   - Simplified mobile navigation

5. **Export/Print**:
   - Print search results
   - Export inventory to Excel

6. **Analytics Dashboard**:
   - Most searched medicines
   - Peak search times
   - Popular neighborhoods

