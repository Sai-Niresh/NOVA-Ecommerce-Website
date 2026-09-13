<?php
/**
 * NOVA — Footer Include
 * Full footer: brand, links, social, newsletter, copyright
 */
?>
<footer class="nova-footer" role="contentinfo">
  <div class="nova-container">

    <!-- Footer Grid -->
    <div class="footer-grid">

      <!-- Brand Column -->
      <div class="footer-col">
        <a href="/" class="footer-brand-logo" aria-label="NOVA — Home">NOVA</a>
        <p class="footer-brand-text">
          Premium fashion and lifestyle, designed for the modern world. Curated collections for those who live with intention.
        </p>
        <!-- Social Links -->
        <div class="footer-social" role="list">
          <a href="<?= NOVA_SOCIAL_INSTAGRAM ?>" class="footer-social-link" aria-label="Follow NOVA on Instagram" target="_blank" rel="noopener noreferrer" role="listitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
          <a href="<?= NOVA_SOCIAL_FACEBOOK ?>" class="footer-social-link" aria-label="Follow NOVA on Facebook" target="_blank" rel="noopener noreferrer" role="listitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
          <a href="<?= NOVA_SOCIAL_LINKEDIN ?>" class="footer-social-link" aria-label="Follow NOVA on LinkedIn" target="_blank" rel="noopener noreferrer" role="listitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
              <rect x="2" y="9" width="4" height="12"></rect>
              <circle cx="4" cy="4" r="2"></circle>
            </svg>
          </a>
          <a href="<?= NOVA_SOCIAL_TWITTER ?>" class="footer-social-link" aria-label="Follow NOVA on X (Twitter)" target="_blank" rel="noopener noreferrer" role="listitem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.741l7.73-8.835L1.254 2.25H8.08l4.254 5.622L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- Shop Column -->
      <div class="footer-col">
        <h3 class="footer-col-heading">Shop</h3>
        <ul class="footer-links" role="list">
          <li><a href="shop.php" class="footer-link">Shop All</a></li>
          <li><a href="category.php?c=Men" class="footer-link">Men</a></li>
          <li><a href="category.php?c=Women" class="footer-link">Women</a></li>
          <li><a href="category.php?c=Shoes" class="footer-link">Shoes</a></li>
          <li><a href="category.php?c=Watches" class="footer-link">Watches</a></li>
          <li><a href="category.php?c=Bags" class="footer-link">Bags</a></li>
          <li><a href="category.php?c=Accessories" class="footer-link">Accessories</a></li>
          <li><a href="shop.php?sort=newest" class="footer-link">New Arrivals</a></li>
        </ul>
      </div>

      <!-- Customer Service Column -->
      <div class="footer-col">
        <h3 class="footer-col-heading">Customer Service</h3>
        <ul class="footer-links" role="list">
          <li><a href="#" class="footer-link">Contact Us</a></li>
          <li><a href="#" class="footer-link">FAQ</a></li>
          <li><a href="#" class="footer-link">Shipping Info</a></li>
          <li><a href="#" class="footer-link">Returns & Exchanges</a></li>
          <li><a href="#" class="footer-link">Track Your Order</a></li>
          <li><a href="#" class="footer-link">Size Guide</a></li>
        </ul>
      </div>

      <!-- Policies Column -->
      <div class="footer-col">
        <h3 class="footer-col-heading">Company</h3>
        <ul class="footer-links" role="list">
          <li><a href="#" class="footer-link">About NOVA</a></li>
          <li><a href="#" class="footer-link">Careers</a></li>
          <li><a href="#" class="footer-link">Privacy Policy</a></li>
          <li><a href="#" class="footer-link">Terms of Service</a></li>
          <li><a href="#" class="footer-link">Refund Policy</a></li>
          <li><a href="#" class="footer-link">Cookie Policy</a></li>
          <li><a href="admin.php" class="footer-link">Admin Login</a></li>
        </ul>
      </div>

    </div><!-- /footer-grid -->

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <p class="footer-copyright">
        &copy; <?= date('Y') ?> NOVA. All rights reserved.
      </p>
      <div class="footer-bottom-links" role="list">
        <a href="#" class="footer-bottom-link" role="listitem">Privacy</a>
        <a href="#" class="footer-bottom-link" role="listitem">Terms</a>
        <a href="#" class="footer-bottom-link" role="listitem">Cookies</a>
      </div>
      <!-- Payment icons row (text fallback) -->
      <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
        <span style="font-size:0.7rem; letter-spacing:0.08em; text-transform:uppercase; color:rgba(255,255,255,0.25);">Secure payments:</span>
        <span style="font-size:0.65rem; letter-spacing:0.05em; color:rgba(255,255,255,0.25);">VISA · Mastercard · UPI · PayPal</span>
      </div>
    </div>

  </div>
</footer>
