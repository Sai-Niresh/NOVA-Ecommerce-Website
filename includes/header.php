<?php
/**
 * NOVA — Header Include
 * Sticky header: transparent on hero, solid on scroll
 * Mobile: off-canvas drawer
 */
require_once __DIR__ . '/../config/bootstrap.php';
nova_session_start();
$currentUser = $_SESSION['nova_user'] ?? null;
?>
<!-- Page Loader -->
<div class="page-loader" id="page-loader" aria-hidden="true">
  <div class="loader-logo" id="loader-logo">NOVA</div>
  <div class="loader-bar-track">
    <div class="loader-bar" id="loader-bar"></div>
  </div>
</div>

<!-- Custom Cursor (desktop) -->
<div class="custom-cursor" id="custom-cursor" aria-hidden="true"></div>
<div class="cursor-follower" id="cursor-follower" aria-hidden="true"></div>

<!-- Scroll Progress -->
<div class="scroll-progress" id="scroll-progress" aria-hidden="true"></div>

<!-- Cart Feedback Toast -->
<div class="cart-feedback" id="cart-feedback" role="status" aria-live="polite">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <polyline points="20 6 9 17 4 12"></polyline>
  </svg>
  <span id="cart-feedback-msg">Added to cart!</span>
</div>

<!-- Sign-in / Sign-up modal -->
<div class="account-modal-overlay" id="account-modal" aria-hidden="true">
  <section class="account-modal" role="dialog" aria-modal="true" aria-labelledby="account-modal-title">
    <button class="account-modal-close" id="account-modal-close" type="button" aria-label="Close sign in dialog">&times;</button>
    <p class="account-modal-eyebrow">NOVA ACCOUNT</p>
    <h2 id="account-modal-title">Welcome back</h2>
    <p class="account-modal-copy" id="account-modal-copy">Sign in to save your wishlist and keep your NOVA shopping experience together.</p>

    <div class="account-modal-tabs" id="account-modal-tabs">
      <button type="button" class="account-tab-btn is-active" id="account-tab-signin" data-tab-mode="signin">Sign In</button>
      <button type="button" class="account-tab-btn" id="account-tab-signup" data-tab-mode="signup">Create Account</button>
    </div>

    <form class="account-form" id="account-form" action="auth.php" method="POST" novalidate>
      <input type="hidden" name="action" id="account-action" value="login">
      <?= nova_csrf_field() ?>

      <div class="account-signup-only" hidden>
        <label for="account-name">Full name</label>
        <input id="account-name" name="name" type="text" autocomplete="name" placeholder="e.g. Eleanor Vance">
      </div>

      <label for="account-email">Email address</label>
      <input id="account-email" name="email" type="email" autocomplete="email" placeholder="name@example.com" required>

      <label for="account-password">Password</label>
      <div class="account-password-field">
        <input id="account-password" name="password" type="password" autocomplete="current-password" placeholder="At least 6 characters" required minlength="6">
        <button class="account-password-toggle" type="button" data-password-target="account-password" aria-label="Show password">Show</button>
      </div>

      <div class="account-signin-only">
        <button class="account-forgot-password" id="account-forgot-password" type="button">Forgot password?</button>
      </div>

      <div class="account-reset-only" hidden>
        <label for="account-reset-password">New password</label>
        <div class="account-password-field">
          <input id="account-reset-password" name="reset_password" type="password" autocomplete="new-password" minlength="6">
          <button class="account-password-toggle" type="button" data-password-target="account-reset-password" aria-label="Show new password">Show</button>
        </div>
        <label for="account-reset-confirm">Confirm new password</label>
        <div class="account-password-field">
          <input id="account-reset-confirm" name="reset_confirm" type="password" autocomplete="new-password" minlength="6">
          <button class="account-password-toggle" type="button" data-password-target="account-reset-confirm" aria-label="Show confirmed new password">Show</button>
        </div>
      </div>

      <div class="account-signup-only" hidden>
        <label for="account-confirm-password">Confirm password</label>
        <div class="account-password-field">
          <input id="account-confirm-password" name="confirm_password" type="password" autocomplete="new-password" placeholder="Re-enter password" minlength="6">
          <button class="account-password-toggle" type="button" data-password-target="account-confirm-password" aria-label="Show confirm password">Show</button>
        </div>
      </div>

      <button class="btn btn-primary" id="account-submit-btn" type="submit">Sign In</button>
      <button class="account-mode-toggle" id="account-mode-toggle" type="button">Don't have an account? Create one</button>
      <p class="account-form-message" id="account-form-message" role="status" aria-live="polite"></p>
    </form>
  </section>
</div>


<div class="drawer-backdrop" id="drawer-backdrop" aria-hidden="true"></div>

<!-- Mobile Drawer -->
<nav class="mobile-drawer" id="mobile-drawer" aria-label="Mobile navigation" aria-hidden="true">
  <div class="drawer-header">
    <span class="drawer-logo" aria-hidden="true">NOVA</span>
    <button class="drawer-close" id="drawer-close" aria-label="Close navigation menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <div class="drawer-nav" role="list">
    <a href="shop.php" class="drawer-nav-link" role="listitem">
      <span>Shop All</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Men" class="drawer-nav-link" role="listitem">
      <span>Men</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Women" class="drawer-nav-link" role="listitem">
      <span>Women</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Shoes" class="drawer-nav-link" role="listitem">
      <span>Shoes</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Watches" class="drawer-nav-link" role="listitem">
      <span>Watches</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Bags" class="drawer-nav-link" role="listitem">
      <span>Bags</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="category.php?c=Accessories" class="drawer-nav-link" role="listitem">
      <span>Accessories</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
    <a href="admin.php" class="drawer-nav-link drawer-nav-link--admin" role="listitem">
      <span>Admin Login</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v18"></path><path d="M3 12h18"></path></svg>
    </a>
  </div>

  <div class="drawer-footer">
    <?php if ($currentUser): ?>
      <div style="width:100%; display:flex; flex-direction:column; gap:0.6rem;">
        <div style="font-size:0.875rem; color:var(--nova-gray-700);">Signed in as <strong style="color:var(--nova-black);"><?= htmlspecialchars($currentUser['name']) ?></strong></div>
        <div style="display:flex; gap:0.5rem;">
          <a href="orders-page.php" class="btn btn-outline btn-sm" style="flex:1; text-align:center; justify-content:center;">My Orders</a>
          <button type="button" class="btn btn-primary btn-sm js-account-logout" style="flex:1; text-align:center; justify-content:center;">Sign Out</button>
        </div>
      </div>
    <?php else: ?>
      <button type="button" class="btn btn-primary btn-sm js-account-open" data-account-mode="signin" style="flex:1; text-align:center; justify-content:center;">Sign In</button>
      <button type="button" class="btn btn-outline btn-sm js-account-open" data-account-mode="signup" style="flex:1; text-align:center; justify-content:center;">Register</button>
    <?php endif; ?>
  </div>
</nav>

<!-- ─── MAIN HEADER ─── -->
<header class="nova-header" id="nova-header" role="banner">
  <div class="header-inner">

    <!-- Logo -->
    <a href="index.php" class="nova-logo" aria-label="NOVA — Home">NOVA</a>

    <!-- Desktop Navigation -->
    <nav class="nova-nav" aria-label="Primary navigation">
      <a href="shop.php" class="nav-link">Shop</a>
      <a href="category.php?c=Men" class="nav-link">Men</a>
      <a href="category.php?c=Women" class="nav-link">Women</a>
      <a href="category.php?c=Shoes" class="nav-link">Shoes</a>
      <a href="category.php?c=Watches" class="nav-link">Watches</a>
      <a href="category.php?c=Bags" class="nav-link">Bags</a>
      <a href="category.php?c=Accessories" class="nav-link">Accessories</a>
      <a href="admin.php" class="nav-link nav-link--admin">Admin</a>
    </nav>

    <!-- Right Actions -->
    <div class="nav-actions" role="list">
      <!-- Search -->
      <button class="nav-icon-btn" aria-label="Search" id="search-btn" role="listitem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>

      <!-- Account -->
      <?php if ($currentUser): ?>
        <div class="user-menu-wrap" id="user-menu-wrap" role="listitem">
          <button class="nav-icon-btn user-menu-trigger" id="user-menu-trigger" aria-label="User menu" type="button" aria-expanded="false" title="<?= htmlspecialchars($currentUser['name']) ?>">
            <span class="user-avatar-badge"><?= htmlspecialchars(mb_strtoupper(mb_substr($currentUser['name'] ?? 'U', 0, 1))) ?></span>
          </button>
          <div class="user-dropdown-menu" id="user-dropdown-menu" aria-hidden="true">
            <div class="user-dropdown-header">
              <strong id="user-display-name"><?= htmlspecialchars($currentUser['name']) ?></strong>
              <span id="user-display-email"><?= htmlspecialchars($currentUser['email']) ?></span>
            </div>
            <a href="orders-page.php" class="user-dropdown-item">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
              My Orders
            </a>
            <button type="button" class="user-dropdown-item js-account-logout">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              Sign Out
            </button>
          </div>
        </div>
      <?php else: ?>
        <div class="user-menu-wrap" id="user-menu-wrap" role="listitem">
          <button class="nav-icon-btn js-account-open" aria-label="My account" type="button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </button>
          <div class="user-dropdown-menu" id="user-dropdown-menu" aria-hidden="true" style="display:none;"></div>
        </div>
      <?php endif; ?>

      <!-- Wishlist -->
      <button class="nav-icon-btn" aria-label="Wishlist" id="nav-wishlist-btn" type="button" role="listitem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span class="cart-badge wishlist-badge" id="wishlist-count" aria-hidden="true" style="display:none;">0</span>
      </button>

      <!-- Cart -->
      <button class="nav-icon-btn" aria-label="Shopping cart" id="cart-btn" type="button" role="listitem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span class="cart-badge" id="cart-count" aria-hidden="true">0</span>
      </button>

      <!-- Mobile Menu Button -->
      <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobile-drawer">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="3" y1="6"  x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="15" y2="18"></line>
        </svg>
      </button>
      <a href="admin.php" class="mobile-admin-link" aria-label="Admin login">Admin</a>
    </div>

  </div>

<!-- ─── DEDICATED FULL-WIDTH TOP HEADER SEARCH ─── -->
<div class="header-search-bar-overlay" id="header-search-bar-overlay" aria-hidden="true">
  <div class="header-search-bar-container nova-container">
    <form class="header-search-bar-form" id="header-search-bar-form" method="get" action="shop.php">
      <svg class="header-search-bar-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="search" id="header-search-bar-input" name="search" class="header-search-bar-input" placeholder="Search for products, categories, or styles..." autocomplete="off" aria-label="Search products">
      <button type="submit" class="header-search-bar-submit" aria-label="Submit search">SEARCH</button>
    </form>
    <div class="header-search-bar-shortcuts">
      <span class="search-shortcut-label">POPULAR:</span>
      <a href="shop.php?search=Men" class="search-shortcut-tag">Men</a>
      <a href="shop.php?search=Women" class="search-shortcut-tag">Women</a>
      <a href="shop.php?search=Shoes" class="search-shortcut-tag">Shoes</a>
      <a href="shop.php?search=Watches" class="search-shortcut-tag">Watches</a>
      <a href="shop.php?search=Bags" class="search-shortcut-tag">Bags</a>
      <a href="shop.php?search=Accessories" class="search-shortcut-tag">Accessories</a>
    </div>
    <button class="header-search-bar-close" id="header-search-bar-close" aria-label="Close search">&times;</button>
  </div>
</div>

<!-- ─── HEADER WISHLIST & CART PANEL ─── -->
<div class="header-panel-overlay" id="header-panel" aria-hidden="true">
  <div class="header-panel" role="dialog" aria-modal="true" aria-labelledby="header-panel-title">
    <button class="header-panel-close" id="header-panel-close" aria-label="Close panel">&times;</button>
    <h2 class="header-panel-title" id="header-panel-title">Your Cart</h2>
    <p class="header-panel-lead" id="header-panel-lead">Review your selected pieces before checkout.</p>
    <div class="header-panel-content" id="header-panel-content"></div>
  </div>
</div>

</header>

