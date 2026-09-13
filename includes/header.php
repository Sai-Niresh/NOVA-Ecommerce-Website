<?php
/**
 * NOVA — Header Include
 * Sticky header: transparent on hero, solid on scroll
 * Mobile: off-canvas drawer
 */
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

<!-- Sign-in modal (frontend demo for Phase 3) -->
<div class="account-modal-overlay" id="account-modal" aria-hidden="true">
  <section class="account-modal" role="dialog" aria-modal="true" aria-labelledby="account-modal-title">
    <button class="account-modal-close" id="account-modal-close" type="button" aria-label="Close sign in dialog">&times;</button>
    <p class="account-modal-eyebrow">NOVA ACCOUNT</p>
    <h2 id="account-modal-title">Welcome back</h2>
    <p class="account-modal-copy" id="account-modal-copy">Sign in to save your wishlist and keep your NOVA shopping experience together.</p>
    <form class="account-form" id="account-form" novalidate>
      <label for="account-name" class="account-signup-only">Full name</label>
      <input id="account-name" class="account-signup-only" name="name" type="text" autocomplete="name">
      <label for="account-email">Email address</label>
      <input id="account-email" name="email" type="email" autocomplete="email" required>
      <label for="account-password">Password</label>
      <div class="account-password-field">
        <input id="account-password" name="password" type="password" autocomplete="current-password" required minlength="6">
        <button class="account-password-toggle" type="button" data-password-target="account-password" aria-label="Show password">Show</button>
      </div>
      <button class="account-forgot-password" id="account-forgot-password" type="button">Forgot password?</button>
      <div class="account-reset-only">
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
      <label for="account-confirm-password" class="account-signup-only">Confirm password</label>
      <div class="account-password-field account-signup-only">
        <input id="account-confirm-password" name="confirm_password" type="password" autocomplete="new-password" minlength="6">
        <button class="account-password-toggle" type="button" data-password-target="account-confirm-password" aria-label="Show confirm password">Show</button>
      </div>
      <button class="btn btn-primary" type="submit">Sign In</button>
      <button class="account-mode-toggle" id="account-mode-toggle" type="button">Create an account</button>
      <p class="account-form-message" id="account-form-message" role="status" aria-live="polite"></p>
    </form>
  </section>
</div>

<div class="header-panel-overlay" id="header-panel" aria-hidden="true">
  <section class="header-panel" role="dialog" aria-modal="true" aria-labelledby="header-panel-title">
    <button class="header-panel-close" id="header-panel-close" type="button" aria-label="Close panel">&times;</button>
    <p class="account-modal-eyebrow">NOVA SHOP</p>
    <h2 id="header-panel-title">Search NOVA</h2>
    <p class="header-panel-lead">Search the collection by product, category, or style.</p>
    <form class="header-search-form" id="header-search-form">
      <label class="sr-only" for="header-search-input">Search products</label>
      <span class="header-search-icon" aria-hidden="true">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </span>
      <input id="header-search-input" type="search" placeholder="Try “leather bag”" autocomplete="off">
      <button class="header-search-submit" type="submit" aria-label="Submit product search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </button>
    </form>
    <div class="header-panel-content" id="header-panel-content"></div>
    <div class="search-discovery" id="search-discovery">
      <p class="search-discovery-label">Explore by category</p>
      <div class="search-category-links">
        <a href="category.php?c=Men">Men</a>
        <a href="category.php?c=Women">Women</a>
        <a href="category.php?c=Shoes">Shoes</a>
        <a href="category.php?c=Bags">Bags</a>
        <a href="category.php?c=Watches">Watches</a>
        <a href="category.php?c=Accessories">Accessories</a>
      </div>
    </div>
  </section>
</div>

<!-- Drawer Backdrop -->
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
    <button type="button" class="btn btn-primary btn-sm js-account-open" style="flex:1; text-align:center; justify-content:center;">Sign In</button>
    <button type="button" class="btn btn-outline btn-sm js-account-open" data-account-mode="signup" style="flex:1; text-align:center; justify-content:center;">Register</button>
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
      <button class="nav-icon-btn js-account-open" aria-label="My account" type="button" role="listitem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </button>

      <!-- Wishlist -->
      <button class="nav-icon-btn" aria-label="Wishlist" id="nav-wishlist-btn" type="button" role="listitem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
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
</header>
