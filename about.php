<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'About MedFinder Ethiopia';
include 'includes/header.php';
?>

<main id="main-content">
    <section class="page-hero">
        <div class="container page-hero-inner">
            <div>
                <p class="eyebrow">About us</p>
                <h1 class="page-title">Medicine access you can trust.</h1>
                <p class="page-subtitle">MedFinder connects patients with verified local pharmacies, giving families a fast way to check medicine availability before leaving home.</p>
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <span>About</span>
                </div>
            </div>
            <div class="page-actions">
                <a class="btn btn-primary" href="search-results.php">Search pharmacies</a>
                <a class="btn btn-secondary" href="pharmacy/register.php">Partner with us</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container card-grid">
            <article class="card reveal">
                <h3>Our mission</h3>
                <p>Make essential medicines discoverable in minutes, saving time for patients and strengthening local pharmacies.</p>
            </article>
            <article class="card reveal delay-1">
                <h3>How we verify</h3>
                <p>Every partner pharmacy is reviewed and must update inventory regularly to appear in search results.</p>
            </article>
            <article class="card reveal delay-2">
                <h3>Built for Ethiopia</h3>
                <p>We focus on neighborhoods first, making the service reliable for families and community clinics.</p>
            </article>
        </div>
    </section>

    <section class="section section-accent">
        <div class="container steps-grid">
            <div class="steps-header">
                <p class="eyebrow">Our approach</p>
                <h2>Simple, transparent, and always local.</h2>
                <p class="lead">We prioritize clear information over complex features, keeping the experience dependable for every user.</p>
            </div>
            <div class="steps-list">
                <div class="step-card reveal">
                    <span class="step-number">1</span>
                    <h3>Neighborhood-first data</h3>
                    <p>Search results show the closest pharmacies so families can act quickly.</p>
                </div>
                <div class="step-card reveal delay-1">
                    <span class="step-number">2</span>
                    <h3>Verified partners</h3>
                    <p>Only approved pharmacies appear, with clear inventory status updates.</p>
                </div>
                <div class="step-card reveal delay-2">
                    <span class="step-number">3</span>
                    <h3>Direct contact</h3>
                    <p>Patients can call pharmacies instantly to confirm and reserve medicine.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container stat-grid">
            <div class="stat-card reveal">
                <h3>50+</h3>
                <p>Pharmacies onboarding</p>
            </div>
            <div class="stat-card reveal delay-1">
                <h3>7</h3>
                <p>Neighborhood zones live</p>
            </div>
            <div class="stat-card reveal delay-2">
                <h3>24 hours</h3>
                <p>Average onboarding time</p>
            </div>
            <div class="stat-card reveal delay-3">
                <h3>1 goal</h3>
                <p>Better access for every family</p>
            </div>
        </div>
    </section>

    <section class="section cta">
        <div class="container cta-inner">
            <div>
                <p class="eyebrow">Join the network</p>
                <h2>Serve more patients with verified updates.</h2>
                <p class="lead">Pharmacies can publish availability, prices, and hours with a simple dashboard.</p>
            </div>
            <div class="cta-actions">
                <a class="btn btn-primary" href="pharmacy/register.php">Register pharmacy</a>
                <a class="btn btn-secondary" href="contact.html">Contact the team</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
