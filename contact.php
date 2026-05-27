<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Contact MedFinder Ethiopia';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">Contact</p>
                <h1 class="page-title">Talk with the MedFinder team.</h1>
                <p class="page-subtitle">Send questions, report issues, or request onboarding support for your pharmacy.</p>
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <span>Contact</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="pharmacy/register.php">Register pharmacy</a>
                <a class="btn btn-secondary" href="search-results.php">Search pharmacies</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container page-layout">
            <div class="content-area">
                <div class="form-card">
                    <h3>Send a message</h3>
                    <form class="contact-form" action="contact.php" method="post">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="contact-name">Full name <span class="required">*</span></label>
                                <input type="text" id="contact-name" name="name" placeholder="Your name" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-email">Email address <span class="required">*</span></label>
                                <input type="email" id="contact-email" name="email" placeholder="you@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-phone">Phone number</label>
                                <input type="tel" id="contact-phone" name="phone" placeholder="+251 9x xxx xxxx">
                            </div>
                            <div class="form-group">
                                <label for="contact-topic">Topic</label>
                                <select id="contact-topic" name="topic">
                                    <option value="support">Support</option>
                                    <option value="onboarding">Pharmacy onboarding</option>
                                    <option value="feedback">Product feedback</option>
                                </select>
                            </div>
                            <div class="form-group form-group-full">
                                <label for="contact-message">Message <span class="required">*</span></label>
                                <textarea id="contact-message" name="message" placeholder="How can we help?" required></textarea>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button class="btn btn-primary" type="submit">Send message</button>
                            <span class="form-note">We respond within 1 business day.</span>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="sidebar">
                <h3 class="panel-title">Contact details</h3>
                <div class="info-list">
                    <div class="info-row">
                        <span>Phone</span>
                        <a href="tel:+251912345678">+251 91 234 5678</a>
                    </div>
                    <div class="info-row">
                        <span>Email</span>
                        <span>hello@medfinder.et</span>
                    </div>
                    <div class="info-row">
                        <span>Office</span>
                        <span>Bole, Addis Ababa</span>
                    </div>
                    <div class="info-row">
                        <span>Hours</span>
                        <span>Monday - Saturday, 8:30 AM - 6:00 PM</span>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
