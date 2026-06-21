
## Folder Structure

```
medfinder-ethiopia/
│
├── index.php                         # Landing/Home page with search
├── search-results.php                # Medicine search results page
├── pharmacy-detail.php               # Individual pharmacy profile page
├── about.php                         # About the project
├── contact.php                       # Contact form
│
├── admin/                             # Admin panel section
│   ├── index.php                     # Admin dashboard
│   ├── login.php                      # Admin login
│   ├── logout.php                     # Admin logout
│   ├── medicines/
│   │   ├── index.php                 # List all medicines
│   │   ├── add.php                    # Add new medicine to catalog
│   │   ├── edit.php                   # Edit medicine details
│   │   ├── delete.php                 # Delete medicine
│   │
│   ├── pharmacies/
│   │   ├── index.php                 # List all pharmacies
│   │   ├── approve.php                # Approve pending pharmacies
│   │   ├── view.php                   # View pharmacy details
│   │   ├── delete.php                 # Delete pharmacy
│   │
│   └── neighborhoods/
│       ├── index.php                 # Manage neighborhoods/areas
│       ├── add.php                    # Add new area
│       ├── edit.php                   # Edit area
│       ├── delete.php                 # Delete area
│
├── pharmacy/                          # Pharmacy owner panel
│   ├── index.php                     # Pharmacy dashboard
│   ├── login.php                      # Pharmacy login
│   ├── register.php                   # Pharmacy registration
│   ├── logout.php                     # Logout
│   ├── profile.php                    # Edit pharmacy profile
│   ├── inventory/
│   │   ├── index.php                 # View current inventory
│   │   ├── add.php                    # Add medicine to inventory
│   │   ├── update.php                 # Update stock/price
│   │   ├── delete.php                 # Remove from inventory
│
├── includes/                          # Reusable PHP files & Services
│   ├── config.php                     # Configuration & Constants
│   ├── db.php                         # PDO Database connection
│   ├── functions.php                  # Common helpers
│   ├── header.php                     # Layout header
│   ├── footer.php                     # Layout footer
│   ├── admin-auth.php                 # Admin guards
│   ├── pharmacy-auth.php              # Pharmacy guards
│   └── services/                      # Business logic layer
│       ├── AdminService.php
│       ├── InventoryService.php
│       ├── MedicineService.php
│       ├── NeighborhoodService.php
│       └── SearchService.php
│
├── css/                               # Stylesheets
│   ├── style.css                      # Global styles
│   ├── admin.css                      # Admin specific
│   ├── pharmacy.css                   # Pharmacy specific
│   ├── responsive.css                 # Layout adjustments
│
├── js/                                # JavaScript files
│   ├── main.js                        # UI interactions
│   ├── search.js                      # Live search
│   ├── validation.js                  # Frontend validation
│
├── uploads/                           # Store uploaded logos
└── sql/
    └── database.sql                   # Schema export
```

## Features Breakdown

### **1. Public Features**

#### **A. Home Page (index.php)**
- **Global Search**: Search by medicine name and filter by neighborhood.
- **Dynamic Stats**: Live counts for medicines and pharmacies.

#### **B. Search Results (search-results.php)**
- **Filtering**: Filter by stock level (In Stock, Limited, Out of Stock).
- **Sorting**: Sort by price or recently updated.
- **Direct Contact**: One-click call for mobile users.

#### **C. Pharmacy Detail (pharmacy-detail.php)**
- **Complete Inventory**: Shows every medicine the pharmacy stocks.
- **Status Pills**: Visual indicators for stock levels.
- **Contact Info**: Phone and address displayed.

### **2. Pharmacy Dashboard**
- **Inventory Management**: Add, update, or remove medicines easily.
- **Profile Management**: Update logo, hours, and contact details.

### **3. Admin Management**
- **Catalog Control**: Centrally manage the list of valid medicines.
- **Approval Workflow**: Review and approve new pharmacy signups.
- **Area Management**: Manage neighborhoods/zones.

## Security & Validation
- **Authentication**: Secure password hashing with bcrypt.
- **CSRF Protection**: Tokens enforced on all POST requests.
- **Form Validation**: Required fields marked with `*` and server-side validation.
- **Input Sanitization**: Protection against SQL injection and XSS.

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

