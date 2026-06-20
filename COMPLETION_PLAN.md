# MedFinder Project - Completion Plan

**Current Status**: 85% Complete (MVP Functional)  
**Last Updated**: May 27, 2026  
**Scope**: Phase 5 Customer Features + Polish

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Current Status Summary](#current-status-summary)
3. [Remaining Work](#remaining-work)
4. [Implementation Roadmap](#implementation-roadmap)
5. [Detailed Implementation Guides](#detailed-implementation-guides)
6. [Testing Strategy](#testing-strategy)
7. [Deployment Checklist](#deployment-checklist)

---

## Project Overview

**MedFinder Ethiopia** is a medicine availability platform connecting customers with pharmacies in Addis Ababa.

### Three User Roles:
1. **System Admin** - Manage the platform (pharmacies, medicines, neighborhoods, customers)
2. **Pharmacy Owner** - Manage inventory and profile
3. **Customer** - Search medicines, save favorites, view pharmacy details

### Tech Stack:
- Backend: PHP 7.4+ (no framework)
- Database: MySQL 5.7+
- Frontend: HTML/CSS/JS (vanilla)
- Session-based auth (no JWT/OAuth)

---

## Current Status Summary

### ✅ COMPLETED (85%)

**Backend Services** (All 7 service classes):
- AdminService - Pharmacy, medicine, neighborhood, customer management
- PharmacyService - Profile, stats, auth
- MedicineService - Catalog search & suggestions
- NeighborhoodService - Geographic areas
- InventoryService - Medicine stock per pharmacy
- SearchService - Full-text search with filters
- CustomerService - ✅ JUST IMPLEMENTED

**Authentication & Authorization**:
- ✅ Admin auth guard + session
- ✅ Pharmacy auth guard + session
- ✅ Customer auth guard + session
- ✅ Centralized logout endpoint

**Admin Portal** (`/admin/`):
- ✅ Dashboard with stats
- ✅ Pharmacy management (approve/reject/suspend/delete)
- ✅ Medicine management
- ✅ Neighborhood management
- ✅ Customer management (full CRUD) - NEW

**Pharmacy Portal** (`/pharmacy/`):
- ✅ Login/Register
- ✅ Profile editing (name, address, hours, logo, neighborhood)
- ✅ Password management
- ✅ Inventory management (add/edit/delete medicines)

**Public Features** (`/`):
- ✅ Homepage with hero section & featured pharmacies
- ✅ Search page with filters (medicine, neighborhood, status)
- ✅ Pharmacy detail page
- ✅ Medicine search API (AJAX autocomplete)

**UX/UI (Phases 1-4)**:
- ✅ Session-aware navigation header (shows role-specific buttons)
- ✅ Role-based CSS theming (Maroon/Blue/Green themes)
- ✅ Welcome banners on homepage per role
- ✅ Unified logout system

**Database**:
- ✅ admins (5 columns)
- ✅ pharmacies (14 columns)
- ✅ medicines (6 columns)
- ✅ inventory (9 columns)
- ✅ neighborhoods (4 columns)
- ✅ search_logs (7 columns)
- ✅ customers (10 columns) - NEW
- ✅ customer_favorites (3 columns) - NEW

**Security**:
- ✅ CSRF token validation on all forms
- ✅ Password hashing with bcrypt (cost 12)
- ✅ Session regeneration on login/logout
- ✅ Input sanitization & XSS protection
- ✅ SQL injection prevention (prepared statements)

---

## Remaining Work

### Phase 5: Customer Features (15%)

**Priority 1 (Core Features - Required)**
1. ❌ Customer Dashboard (`/customer/dashboard.php`)
   - View profile info
   - View favorite pharmacies
   - View search history (optional)
   - Update profile
   
2. ❌ Favorites UI Integration
   - "Add to Favorites" button on search results
   - "Add to Favorites" button on pharmacy detail page
   - Show favorite status (heart icon filled/empty)
   - Remove from favorites functionality

3. ❌ Customer Profile Page (`/customer/profile.php`)
   - Edit name, email, phone
   - Change password
   - View account created date
   - Deactivate account

4. ❌ Update Header Navigation
   - Add "My Profile" link for customers
   - Add "My Favorites" link for customers
   - Add logout button for customers

**Priority 2 (Polish - Nice to Have)**
5. ❌ Search History
   - Log customer searches
   - Display recent searches on dashboard
   - Clear search history option

6. ❌ Customer Reviews/Feedback
   - Rate pharmacies (1-5 stars)
   - Leave comments
   - View average ratings on search results

7. ❌ Responsive Design
   - Mobile optimization
   - Touch-friendly buttons
   - Mobile menu

8. ❌ Email Notifications (Future)
   - Out-of-stock alerts
   - New medicine notifications
   - Pharmacy status updates

---

## Implementation Roadmap

### Phase 5A: Customer Dashboard (Est. 45 mins)

**Files to Create/Modify**:
```
/customer/dashboard.php           (NEW) - Main customer dashboard
/includes/header.php              (MODIFY) - Add customer nav links
/css/style.css                    (MODIFY) - Add dashboard styling
```

**Dependencies**:
- CustomerService::getCustomer() - Already implemented ✅
- CustomerService::getFavorites() - Already implemented ✅
- Session auth guard - Already implemented ✅

**Features**:
- Display customer profile card (name, email, phone, joined date)
- Display favorite pharmacies in grid/list
- "Remove from Favorites" button per pharmacy
- "Edit Profile" button
- Account status indicator

---

### Phase 5B: Favorites UI Integration (Est. 60 mins)

**Files to Create/Modify**:
```
/includes/functions.php           (MODIFY) - Add favorite helper function
/search-results.php               (MODIFY) - Add favorite button to results
/pharmacy-detail.php              (MODIFY) - Add favorite button to detail page
/customer/add-favorite.php        (NEW) - AJAX endpoint
/customer/remove-favorite.php     (NEW) - AJAX endpoint
/css/style.css                    (MODIFY) - Add favorite button styling
/js/main.js                       (MODIFY) - Add favorite toggle logic
```

**Key Implementation Points**:
- Check if customer is logged in before showing favorite buttons
- Use heart icon (❤️) for filled, ♡ for empty
- AJAX endpoints return JSON for instant feedback
- Update UI without page reload
- Show toast/flash message on success

**Dependencies**:
- CustomerService methods - Already implemented ✅
- Customer auth guard - Already implemented ✅

---

### Phase 5C: Customer Profile Page (Est. 40 mins)

**Files to Create/Modify**:
```
/customer/profile.php             (NEW) - Customer profile edit page
/customer/edit.php                (NEW) - Form handler (or POST to profile.php)
/includes/header.php              (MODIFY) - Link to profile
```

**Dependencies**:
- CustomerService::getCustomer() - ✅
- CustomerService::updateCustomer() - ✅
- CustomerService::updatePassword() - ✅

**Features**:
- Edit form with first_name, last_name, email, phone
- Email validation (must be unique)
- Change password section
- Account deactivation button (with warning)
- Session validation

---

### Phase 5D: Update Header for Customers (Est. 20 mins)

**Files to Modify**:
```
/includes/header.php              (MODIFY) - Add customer nav
/css/style.css                    (MODIFY) - Style customer buttons
```

**Current Header**:
```php
if ($is_admin) {
    // Shows "Admin Panel" + "Logout"
} elseif ($is_pharmacy) {
    // Shows "Dashboard" + "My Inventory" + "Logout"
} else {
    // Shows "Pharmacy Login" + "Register"
}
```

**After Update**:
```php
if ($is_admin) {
    // Shows "Admin Panel" + "Logout"
} elseif ($is_pharmacy) {
    // Shows "Dashboard" + "My Inventory" + "Logout"
} elseif ($is_customer) {
    // Shows "My Profile" + "My Favorites" + "Logout"
} else {
    // Shows "Pharmacy Login" + "Customer Login" + "Register"
}
```

---

## Detailed Implementation Guides

### Guide 1: Customer Dashboard

**File: `/customer/dashboard.php`**

```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

$customer = CustomerService::getCustomer($_SESSION['customer_id']);
$favorites = CustomerService::getFavorites($_SESSION['customer_id']);

$asset_path = '../';
$page_title = 'Dashboard - MedFinder';
include '../includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <h1>Welcome back, <?= h($customer['first_name']) ?></h1>
    </section>

    <!-- Profile Card Section -->
    <div class="card">
        <h2>Your Profile</h2>
        <p>Email: <?= h($customer['email']) ?></p>
        <p>Phone: <?= h($customer['phone'] ?? 'Not provided') ?></p>
        <p>Member since: <?= date('M d, Y', strtotime($customer['created_at'])) ?></p>
        <a class="btn btn-secondary" href="profile.php">Edit Profile</a>
    </div>

    <!-- Favorites Section -->
    <div class="card">
        <h2>My Favorite Pharmacies (<?= count($favorites) ?>)</h2>
        <?php if (empty($favorites)): ?>
            <p>You haven't added any favorites yet.</p>
            <a class="btn btn-primary" href="../search-results.php">Explore Pharmacies</a>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($favorites as $pharmacy): ?>
                    <div class="card pharmacy-card">
                        <h3><?= h($pharmacy['pharmacy_name']) ?></h3>
                        <p><?= h($pharmacy['address']) ?></p>
                        <button class="btn btn-danger btn-sm remove-favorite" data-pharmacy-id="<?= h($pharmacy['pharmacy_id']) ?>">
                            Remove
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
```

**Styling Considerations**:
- Use existing card-grid layout
- Show pharmacy name, address, phone
- Remove button should be red/danger color
- Responsive grid (2-3 columns on desktop, 1 on mobile)

---

### Guide 2: Add Favorite Button to Search Results

**Modify: `/search-results.php`**

Around line 60 where pharmacy cards are rendered, add:

```php
<div class="card result-card">
    <div class="card-header">
        <h3><?= h($row['pharmacy_name']) ?></h3>
        <?php if (!empty($_SESSION['customer_id'])): ?>
            <button class="favorite-btn" 
                    data-pharmacy-id="<?= h($row['pharmacy_id']) ?>"
                    data-is-favorited="<?= CustomerService::isFavorited($_SESSION['customer_id'], $row['pharmacy_id']) ? '1' : '0' ?>">
                <span class="heart-icon">
                    <?php if (CustomerService::isFavorited($_SESSION['customer_id'], $row['pharmacy_id'])): ?>
                        ❤️
                    <?php else: ?>
                        🤍
                    <?php endif; ?>
                </span>
            </button>
        <?php endif; ?>
    </div>
    <!-- Rest of card content -->
</div>
```

**JavaScript Handler** (in `/js/main.js`):

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const favButtons = document.querySelectorAll('.favorite-btn');
    
    favButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const pharmacyId = this.dataset.pharmacyId;
            const isFavorited = this.dataset.isFavorited === '1';
            
            const endpoint = isFavorited 
                ? '/customer/remove-favorite.php'
                : '/customer/add-favorite.php';
            
            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pharmacy_id: pharmacyId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'ok') {
                    const heartIcon = this.querySelector('.heart-icon');
                    if (isFavorited) {
                        heartIcon.textContent = '🤍';
                        this.dataset.isFavorited = '0';
                    } else {
                        heartIcon.textContent = '❤️';
                        this.dataset.isFavorited = '1';
                    }
                }
            });
        });
    });
});
```

---

### Guide 3: AJAX Endpoints for Favorites

**File: `/customer/add-favorite.php`**

```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$pharmacy_id = (int)($data['pharmacy_id'] ?? 0);

if (!$pharmacy_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid pharmacy']);
    exit;
}

if (CustomerService::addFavorite($_SESSION['customer_id'], $pharmacy_id)) {
    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add favorite']);
}
```

**File: `/customer/remove-favorite.php`** (Same structure, different method call)

```php
if (CustomerService::removeFavorite($_SESSION['customer_id'], $pharmacy_id)) {
    echo json_encode(['status' => 'ok']);
}
```

---

### Guide 4: Customer Profile Edit Page

**File: `/customer/profile.php`**

```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/customer-auth.php';
require_once '../includes/services/CustomerService.php';

$customer = CustomerService::getCustomer($_SESSION['customer_id']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = sanitize($_POST['action'] ?? 'profile');

    if ($action === 'profile') {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        // Validation
        if (empty($first_name)) $errors['first_name'] = 'Required';
        if (empty($last_name)) $errors['last_name'] = 'Required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email required';
        } elseif (CustomerService::emailExists($email, $_SESSION['customer_id'])) {
            $errors['email'] = 'Email taken';
        }

        if (empty($errors)) {
            CustomerService::updateCustomer(
                $_SESSION['customer_id'],
                $first_name, $last_name, $email, $phone
            );
            flash('success', 'Profile updated');
            $customer = CustomerService::getCustomer($_SESSION['customer_id']);
        }
    } elseif ($action === 'password') {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Verify old password
        $verify = CustomerService::verifyPassword($customer['email'], $old_password);
        if (!$verify) {
            $errors['old_password'] = 'Incorrect password';
        }
        if ($new_password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        if (strlen($new_password) < 8) {
            $errors['new_password'] = 'Minimum 8 characters';
        }

        if (empty($errors)) {
            CustomerService::updatePassword($_SESSION['customer_id'], $new_password);
            flash('success', 'Password updated');
        }
    }
}

// ... HTML form with two sections: Profile Edit & Password Change
```

---

### Guide 5: Update Header Navigation

**Modify: `/includes/header.php`**

Find the header-actions section (around line 45) and update to:

```php
<div class="header-actions">
    <?php if ($is_admin): ?>
        <!-- Admin Portal -->
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>admin/index.html">Admin Panel</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>logout.php">Logout</a>
    <?php elseif ($is_pharmacy): ?>
        <!-- Pharmacy Portal -->
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/index.php">Dashboard</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/inventory/index.html">My Inventory</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>logout.php">Logout</a>
    <?php elseif ($is_customer): ?>
        <!-- Customer Portal -->
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>customer/dashboard.php">Dashboard</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>customer/profile.php">My Profile</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>logout.php">Logout</a>
    <?php else: ?>
        <!-- Public/Guest Portal -->
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>pharmacy/login.php">Pharmacy Login</a>
        <a class="btn btn-secondary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>customer/login.php">Customer Login</a>
        <a class="btn btn-primary" href="<?php echo htmlspecialchars($asset_path, ENT_QUOTES, 'UTF-8'); ?>customer/register.php">Register</a>
    <?php endif; ?>
</div>
```

Also add to header.php top (detect customer role):

```php
$is_customer = !empty($_SESSION['customer_id']);
```

---

## Testing Strategy

### Unit Testing (Manual)

**Test Case 1: Add to Favorites**
```
1. Log in as customer
2. Go to search results page
3. Click heart icon on a pharmacy
4. Verify heart changes to filled
5. Go to dashboard
6. Verify pharmacy appears in favorites
7. Refresh page - should still appear
```

**Test Case 2: Remove from Favorites**
```
1. Dashboard shows favorite pharmacy
2. Click "Remove" button
3. Pharmacy disappears from dashboard
4. Go to search results
5. Heart icon should be empty again
```

**Test Case 3: Customer Profile Edit**
```
1. Go to /customer/profile.php
2. Change name/email/phone
3. Submit form
4. Verify flash message shows
5. Refresh - values should persist
6. Check database to verify save
```

**Test Case 4: Customer Password Change**
```
1. On profile page, change password section
2. Enter old password (incorrect)
3. Should show error
4. Enter correct old password + new password
5. Submit
6. Log out
7. Log back in with new password
8. Should succeed
```

**Test Case 5: Header Navigation**
```
1. Guest user - sees "Pharmacy Login", "Customer Login", "Register"
2. Log in as customer - sees "Dashboard", "My Profile", "Logout"
3. Log in as pharmacy - sees "Dashboard", "My Inventory", "Logout"
4. Log in as admin - sees "Admin Panel", "Logout"
5. Click logout - back to guest view
```

### Browser Testing

**Desktop** (Chrome/Firefox/Safari):
- [ ] Favorite buttons clickable
- [ ] AJAX works (no page reload)
- [ ] Flash messages appear
- [ ] Dashboard responsive
- [ ] Profile form validation

**Mobile** (iOS/Android):
- [ ] Buttons readable
- [ ] Forms fillable
- [ ] No layout shifts
- [ ] Touch targets 44px minimum

### Edge Cases

```
1. Customer adds same pharmacy twice - should not duplicate
2. Customer favorite in database but deleted pharmacy - graceful handling
3. Email already taken by another customer - error message
4. Session expires during favorite add - redirect to login
5. Rapid clicks on favorite button - debounce/disable button
```

---

## Deployment Checklist

### Before Going Live

- [ ] All PHP files pass syntax check: `php -l <file>`
- [ ] Database migrations run successfully
- [ ] Demo customer account tested end-to-end
- [ ] Customer can register → login → add favorite → view dashboard
- [ ] Customer can edit profile and change password
- [ ] All redirects use base_url() (not hardcoded)
- [ ] CSRF tokens present on all forms
- [ ] Flash messages display correctly
- [ ] Error handling graceful (no white screens)
- [ ] Passwords hashed with bcrypt
- [ ] Session IDs regenerated on login/logout
- [ ] No sensitive data in logs
- [ ] Test on both Apache and PHP CLI server

### Production Setup

```bash
# After deploying:
1. Delete sql/setup.php (security risk)
2. Change default passwords for admin & demo accounts
3. Set up email for notifications (optional)
4. Enable HTTPS for login pages
5. Set up database backups
6. Monitor error logs for issues
7. Test payment integration (if adding in future)
```

---

## File Checklist

### Create (6 new files)
- [ ] `/customer/dashboard.php`
- [ ] `/customer/profile.php`
- [ ] `/customer/add-favorite.php`
- [ ] `/customer/remove-favorite.php`

### Modify (5 files)
- [ ] `/includes/header.php` - Add customer nav + detect $is_customer
- [ ] `/search-results.php` - Add favorite button
- [ ] `/pharmacy-detail.php` - Add favorite button
- [ ] `/js/main.js` - Add favorite toggle logic
- [ ] `/css/style.css` - Add favorite button styling

---

## Effort Estimation

| Task | Est. Time | Difficulty |
|------|-----------|-----------|
| Customer Dashboard | 45 min | Easy |
| Favorites UI | 60 min | Medium |
| Profile Edit Page | 40 min | Easy |
| Header Update | 20 min | Easy |
| Testing & Polish | 45 min | Medium |
| **TOTAL** | **3.5 hours** | - |

---

## Future Enhancements (Post-MVP)

1. **Search History**
   - Log searches to database
   - Show "Recent searches" on dashboard

2. **Pharmacy Reviews**
   - 1-5 star ratings
   - Customer comments
   - Average rating display

3. **Email Notifications**
   - Out-of-stock alerts
   - Favorite pharmacy updates
   - New medicine alerts

4. **Mobile App**
   - React Native or Flutter app
   - Push notifications
   - Camera QR code scanner

5. **Payment Integration**
   - In-app ordering
   - Payment gateway (Stripe/PayPal)
   - Delivery/pickup options

6. **Analytics Dashboard**
   - Search trends
   - Popular medicines
   - Pharmacy performance

7. **Admin Features**
   - Customer analytics
   - Pharmacy performance metrics
   - Revenue tracking

---

## Database Schema (Already Complete)

```sql
-- Customer tables already exist:
CREATE TABLE customers (
    customer_id INT, email VARCHAR(100), password VARCHAR(255),
    first_name VARCHAR(100), last_name VARCHAR(100), phone VARCHAR(20),
    is_active TINYINT, created_at TIMESTAMP, updated_at TIMESTAMP
);

CREATE TABLE customer_favorites (
    favorite_id INT, customer_id INT, pharmacy_id INT, created_at TIMESTAMP
);
```

---

## Session Variables Reference

```php
// Admin
$_SESSION['admin_id']      = 1
$_SESSION['admin_username'] = 'admin'

// Pharmacy Owner  
$_SESSION['pharmacy_id']   = 1
$_SESSION['pharmacy_name'] = 'Unity Pharmacy'
$_SESSION['pharmacy_status'] = 'active'

// Customer (NEW)
$_SESSION['customer_id']   = 1
$_SESSION['customer_email'] = 'customer@test.et'

// All Roles
$_SESSION['_csrf_token']   = 'token...'
$_SESSION['_flash']        = ['type' => 'success', 'message' => '...']
```

---

## Git Commit Messages (When Implementing)

```bash
# Phase 5A
git commit -m "feat: Add customer dashboard with profile and favorites view"

# Phase 5B
git commit -m "feat: Implement favorites UI with add/remove buttons"
git commit -m "feat: Create AJAX endpoints for favorite management"

# Phase 5C
git commit -m "feat: Add customer profile edit and password change pages"

# Phase 5D
git commit -m "style: Update header navigation for customer role"

# Final
git commit -m "style: Add favorite button styling and animations"
git commit -m "test: Complete Phase 5 customer features implementation"
```

---

## Quick Reference URLs

Once implemented, these URLs will be available:

```
Public:
  GET  / → Homepage
  GET  /search-results.php → Search results
  GET  /pharmacy-detail.php → Pharmacy details
  GET  /customer/register.php → Register new customer
  GET  /customer/login.php → Customer login
  
Customer:
  GET  /customer/dashboard.php → Customer dashboard
  GET  /customer/profile.php → Edit profile
  POST /customer/add-favorite.php → Add to favorites (AJAX)
  POST /customer/remove-favorite.php → Remove favorite (AJAX)
  GET  /customer/logout.php → Logout
  
Admin:
  GET  /admin/customers/index.php → List customers
  GET  /admin/customers/add.php → Add customer
  GET  /admin/customers/edit.php → Edit customer
  GET  /admin/customers/delete.php → Delete customer

Pharmacy:
  GET  /pharmacy/dashboard.php → Dashboard
  GET  /pharmacy/inventory/index.php → Inventory list
```

---

**Status**: Ready for implementation  
**Priority**: Medium (MVP complete without these features)  
**Complexity**: Low-Medium (mostly UI integration, backend ready)  
**Risk**: Low (no breaking changes to existing code)

