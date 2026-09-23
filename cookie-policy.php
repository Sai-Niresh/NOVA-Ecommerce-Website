<?php
require_once 'config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cookie Policy — <?= NOVA_BRAND_NAME ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
</head>
<body>
<?php include_once 'includes/header.php'; ?>
<main id="main-content">
  <section class="shop-hero">
    <div class="nova-container">
      <div class="shop-hero-content">
        <span class="shop-tagline">NOVA POLICY</span>
        <h1 class="shop-hero-title">Cookie Policy</h1>
      </div>
    </div>
  </section>
  <section class="nova-section" style="padding-top:var(--space-8);padding-bottom:var(--space-12)">
    <div class="nova-container">
      <div style="max-width:760px">
        <p><strong>Last updated: September 2026</strong></p>
        <p style="margin-top:1rem">This Cookie Policy explains what cookies are, how NOVA uses them, and how you can control your cookie preferences. By using our website, you consent to the use of cookies in accordance with this policy.</p>
        <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>What Are Cookies?</h2><p>Cookies are small text files stored on your device when you visit a website. They help websites function properly, improve security, provide a better user experience, and understand how the site performs. Cookies can be session cookies (expire when you close your browser) or persistent cookies (remain until deleted).</p></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>How We Use Cookies</h2><p>NOVA uses cookies for the following purposes:</p><ul style="margin-bottom:1rem;padding-left:1.25rem"><li><strong>Essential cookies:</strong> Required for the website to function — enable login, cart functionality, and purchases. Cannot be disabled.</li><li><strong>Performance and analytics cookies:</strong> Help us understand visitor behavior anonymously — page views, time on pages, error messages.</li><li><strong>Functional cookies:</strong> Enable enhanced features like remembering your preferences, recently viewed products, and wishlist items.</li><li><strong>Marketing cookies:</strong> Used to deliver relevant advertisements and measure campaign effectiveness. May be set by third-party partners.</li></ul></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>Cookies We Use</h2><p style="margin-bottom:1rem"><strong>session_id</strong> — Maintains your browsing session and login state (Session, Essential)<br><strong>cart_items</strong> — Stores items in your shopping cart (1 year, Essential)<br><strong>wishlist_items</strong> — Stores saved wishlist items (1 year, Essential)<br><strong>csrf_token</strong> — Security token against cross-site request forgery (Session, Essential)<br><strong>recently_viewed</strong> — Remembers recently viewed products (30 days, Functional)<br><strong>grid_preference</strong> — Remembers your grid view layout preference (1 year, Functional)<br><strong>cookie_consent</strong> — Remembers your cookie preference choices (1 year, Functional)</p></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>Third-Party Cookies</h2><p>We may work with trusted third-party providers who set their own cookies for analytics, payment processing, and email marketing. These are subject to the privacy policies of the respective providers.</p></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>Managing Your Preferences</h2><p>You can control cookies through your browser settings, our cookie consent banner, or industry opt-out mechanisms. Disabling essential cookies may affect website functionality and your ability to make purchases.</p></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>Updates to This Policy</h2><p>We may update this Cookie Policy from time to time. The updated policy will be posted on this page with an updated revision date.</p></div>
        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)"><h2>Contact Us</h2><p><strong>NOVA Privacy Team</strong><br>Email: privacy@nova.local<br>Address: NOVA Headquarters, Design District, India</p></div>
      </div>
    </div>
  </section>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>
