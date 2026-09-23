/**
 * NOVA — main.js
 * Navigation, mobile drawer, scroll header, cart counter, smooth scroll
 */

'use strict';

/* ─── STATE ──────────────────────────────────────────────── */
const Nova = {
  cartCount: 0,
  wishlist: new Set(),
};

/* ─── DOM REFS ───────────────────────────────────────────── */
const header       = document.getElementById('nova-header');
const mobileBtn    = document.getElementById('mobile-menu-btn');
const drawer       = document.getElementById('mobile-drawer');
const backdrop     = document.getElementById('drawer-backdrop');
const drawerClose  = document.getElementById('drawer-close');
const cartCountEl  = document.getElementById('cart-count');
const scrollProg   = document.getElementById('scroll-progress');
const accountModal = document.getElementById('account-modal');
const accountClose = document.getElementById('account-modal-close');
const accountForm  = document.getElementById('account-form');
const accountMsg   = document.getElementById('account-form-message');
const accountTitle = document.getElementById('account-modal-title');
const accountCopy  = document.getElementById('account-modal-copy');
const accountSubmit = accountForm?.querySelector('button[type="submit"]');
const accountToggle = document.getElementById('account-mode-toggle');
const accountForgot = document.getElementById('account-forgot-password');
const headerPanel = document.getElementById('header-panel');
const headerPanelTitle = document.getElementById('header-panel-title');
const headerPanelLead = document.querySelector('.header-panel-lead');
const headerPanelContent = document.getElementById('header-panel-content');
const headerPanelClose = document.getElementById('header-panel-close');
const headerSearchBarOverlay = document.getElementById('header-search-bar-overlay');
const headerSearchBarInput = document.getElementById('header-search-bar-input');
const headerSearchBarForm = document.getElementById('header-search-bar-form');
const headerSearchBarClose = document.getElementById('header-search-bar-close');
let accountMode = 'signin';

function openHeaderSearchBar() {
  if (!headerSearchBarOverlay) return;
  closeHeaderPanel();
  headerSearchBarOverlay.classList.add('is-visible');
  headerSearchBarOverlay.setAttribute('aria-hidden', 'false');
  document.getElementById('search-btn')?.classList.add('is-active');
  setTimeout(() => {
    headerSearchBarInput?.focus();
  }, 50);
}

function closeHeaderSearchBar() {
  if (!headerSearchBarOverlay) return;
  headerSearchBarOverlay.classList.remove('is-visible');
  headerSearchBarOverlay.setAttribute('aria-hidden', 'true');
  document.getElementById('search-btn')?.classList.remove('is-active');
}

function openHeaderPanel(mode) {
  if (!headerPanel) return;
  closeHeaderSearchBar();
  document.querySelectorAll('#nav-wishlist-btn, #cart-btn').forEach((button) => {
    button.classList.toggle('is-active', button.id === (mode === 'wishlist' ? 'nav-wishlist-btn' : 'cart-btn'));
  });
  headerPanelTitle.textContent = mode === 'wishlist' ? 'Your Wishlist' : 'Your Cart';
  if (headerPanelLead) {
    headerPanelLead.textContent = mode === 'wishlist'
      ? 'A considered edit of the pieces you want to keep close.'
      : 'Review your selected pieces before checkout.';
  }
  if (mode === 'wishlist') {
    renderWishlistPanel();
  } else {
    const items = getCartItems();
    const count = items.reduce((total, item) => total + item.quantity, 0);
    headerPanelContent.innerHTML = count
      ? `<div class="cart-panel-list">${items.map((item) => `<div class="cart-panel-item"><div class="cart-panel-item-top"><strong>${item.name}</strong><span class="cart-panel-item-qty">Qty ${item.quantity}</span></div><b>₹${Number(item.price).toLocaleString('en-IN')}</b></div>`).join('')}</div><div class="cart-panel-footer"><strong>${count} item${count === 1 ? '' : 's'} in your cart</strong><a class="btn btn-primary panel-action" href="cart.php">View cart</a></div>`
      : '<div class="panel-state"><span class="panel-state-icon">▢</span><strong>Your cart is empty</strong><p>Add something exceptional to get started.</p><a class="btn btn-primary panel-action" href="shop.php">Explore the shop</a></div>';
  }
  headerPanel.classList.add('is-visible');
  headerPanel.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function renderWishlistPanel() {
  if (!headerPanelContent) return;
  const savedIds = Array.from(Nova.wishlist);
  if (!savedIds.length) {
    headerPanelContent.innerHTML =
      '<div class="panel-state panel-state--empty"><span class="panel-state-icon">♡</span><strong>Your wishlist is empty</strong><p>Tap the heart on a product to save it here.</p></div>';
    return;
  }

  let html = '<div class="wishlist-panel-list">';
  savedIds.forEach(id => {
    const heartBtn = document.querySelector(`.product-wishlist-btn[data-id="${id}"]`);
    const card = heartBtn?.closest('.product-card');
    if (!card) return;
    const name = card.querySelector('.product-name')?.textContent?.trim() || 'Product';
    const priceText = card.querySelector('.product-price-current')?.textContent?.trim() || '';
    const image = card.querySelector('.product-img-primary')?.src || '';
    const productUrl = card.querySelector('.product-name a')?.getAttribute('href') || `product.php?id=${id}`;

    html += `
      <div class="wishlist-panel-item">
        <a href="${productUrl}" class="wishlist-panel-item-link" data-wishlist-product-id="${id}">
          <div class="wishlist-panel-item-img">
            <img src="${image}" alt="${name}" loading="lazy">
          </div>
          <div class="wishlist-panel-item-details">
            <p class="product-category-label--detail">WISHLIST</p>
            <h3 class="wishlist-panel-item-name">${name}</h3>
            <p class="wishlist-panel-item-price">${priceText}</p>
          </div>
        </a>
        <div class="wishlist-panel-item-actions">
          <button class="btn btn-outline wishlist-panel-remove" data-wishlist-remove-id="${id}" type="button">Remove</button>
          <button class="btn btn-primary wishlist-panel-cart" data-wishlist-cart-id="${id}" type="button">Add to Cart</button>
        </div>
      </div>`;
  });
  html += '</div>';
  headerPanelContent.innerHTML = html;

  // Wire up remove buttons
  headerPanelContent.querySelectorAll('[data-wishlist-remove-id]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const removeId = btn.dataset.wishlistRemoveId;
      const heartBtn = document.querySelector(`.product-wishlist-btn[data-id="${removeId}"]`);
      if (heartBtn) {
        heartBtn.classList.remove('is-active');
        const icon = heartBtn.querySelector('svg');
        if (icon) {
          icon.style.fill = '';
          icon.classList.remove('wishlist-activated');
        }
      }
      Nova.wishlist.delete(String(removeId));
      renderWishlistPanel();
    });
  });

  // Wire up Add to Cart buttons
  headerPanelContent.querySelectorAll('[data-wishlist-cart-id]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const cartId = btn.dataset.wishlistCartId;
      const heartBtn = document.querySelector(`.product-wishlist-btn[data-id="${cartId}"]`);
      const card = heartBtn?.closest('.product-card');
      if (card && window.NovaApp?.addCartItem) {
        const name = card.querySelector('.product-name')?.textContent?.trim() || 'Item';
        const price = parseFloat(card?.dataset?.price || 0);
        const image = card.querySelector('.product-img-primary')?.src || '';
        window.NovaApp.addCartItem({ id: cartId, name, price, image }, 1);
        showCartFeedback(name);
        const original = btn.textContent;
        btn.textContent = '✓ Added';
        btn.style.background = '#16a34a';
        setTimeout(() => {
          btn.textContent = original;
          btn.style.background = '';
        }, 1400);
      }
    });
  });
}

function getCartItems() {
  try {
    const items = JSON.parse(localStorage.getItem('nova_cart') || '[]');
    return Array.isArray(items) ? items : [];
  } catch (error) {
    return [];
  }
}

Nova.cartCount = getCartItems().reduce((total, item) => total + Number(item.quantity || 0), 0);
if (cartCountEl) cartCountEl.textContent = String(Nova.cartCount);

function addCartItem(item, quantity = 1) {
  const items = getCartItems();
  const existing = items.find((entry) => String(entry.id) === String(item.id));
  if (existing) {
    existing.quantity += quantity;
  } else {
    items.push({ id: item.id, name: item.name, price: Number(item.price) || 0, quantity });
  }
  localStorage.setItem('nova_cart', JSON.stringify(items));
  updateCartCount(quantity);
}

function closeHeaderPanel() {
  headerPanel?.classList.remove('is-visible');
  headerPanel?.setAttribute('aria-hidden', 'true');
  document.querySelectorAll('#search-btn, #nav-wishlist-btn, #cart-btn').forEach((button) => button.classList.remove('is-active'));
  document.body.style.overflow = '';
}

document.getElementById('search-btn')?.addEventListener('click', (e) => {
  e.preventDefault();
  if (headerSearchBarOverlay?.classList.contains('is-visible')) {
    closeHeaderSearchBar();
  } else {
    openHeaderSearchBar();
  }
});
document.getElementById('nav-wishlist-btn')?.addEventListener('click', () => openHeaderPanel('wishlist'));
document.getElementById('cart-btn')?.addEventListener('click', () => openHeaderPanel('cart'));
headerPanelClose?.addEventListener('click', closeHeaderPanel);
headerSearchBarClose?.addEventListener('click', closeHeaderSearchBar);
headerPanel?.addEventListener('click', (event) => {
  if (event.target === headerPanel) closeHeaderPanel();
});
headerSearchBarForm?.addEventListener('submit', (event) => {
  event.preventDefault();
  const query = headerSearchBarInput?.value.trim();
  if (query) window.location.href = `shop.php?search=${encodeURIComponent(query)}`;
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    closeHeaderSearchBar();
    closeHeaderPanel();
  }
});

document.querySelectorAll('.account-password-toggle').forEach((button) => {
  button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.passwordTarget);
    if (!input) return;
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    button.textContent = showing ? 'Show' : 'Hide';
    button.setAttribute('aria-label', `${showing ? 'Show' : 'Hide'} ${input.id === 'account-password' ? 'password' : 'confirm password'}`);
  });
});

function setAccountMode(mode = 'signin') {
  accountMode = mode;
  const isSignup = accountMode === 'signup';
  const isReset = accountMode === 'reset';
  document.querySelectorAll('.account-tab-btn').forEach((tab) => {
    tab.classList.toggle('is-active', (tab.dataset.tabMode || 'signin') === accountMode);
  });
  accountTitle.textContent = isSignup ? 'Create your account' : (isReset ? 'Reset your password' : 'Welcome back');
  accountCopy.textContent = isSignup
    ? 'Create your NOVA account to save your wishlist and shop faster.'
    : (isReset ? 'Confirm your current password and choose a new one for your NOVA account.' : 'Sign in to save your wishlist and keep your NOVA shopping experience together.');
  accountSubmit.textContent = isSignup ? 'Create Account' : (isReset ? 'Reset Password' : 'Sign In');
  accountToggle.textContent = isSignup ? 'Already have an account? Sign In' : (isReset ? 'Back to Sign In' : 'Create an account');
  accountForm.querySelectorAll('.account-signup-only').forEach((field) => {
    field.hidden = !isSignup;
    field.required = isSignup;
  });
  accountForgot.hidden = isSignup || isReset;
  document.querySelectorAll('.account-reset-only').forEach((field) => {
    field.hidden = !isReset;
    field.querySelectorAll('input').forEach((input) => { input.required = isReset; });
  });
  accountMsg.textContent = '';
  accountMsg.className = 'account-form-message';
}

function openAccountModal(mode = 'signin') {
  if (!accountModal) return;
  setAccountMode(mode);
  accountModal.classList.add('is-visible');
  accountModal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
  document.getElementById('account-email')?.focus();
}

function closeAccountModal() {
  if (!accountModal) return;
  accountModal.classList.remove('is-visible');
  accountModal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

document.querySelectorAll('.js-account-open').forEach(button => {
  button.addEventListener('click', () => openAccountModal(button.dataset.accountMode || 'signin'));
});

/* ─── USER DROPDOWN MENU (signed-in state) ────────────────── */
const userMenuTrigger = document.getElementById('user-menu-trigger');
const userDropdownMenu = document.getElementById('user-dropdown-menu');

function closeUserDropdown() {
  userDropdownMenu?.classList.remove('is-open');
  userDropdownMenu?.setAttribute('aria-hidden', 'true');
  userMenuTrigger?.setAttribute('aria-expanded', 'false');
}

userMenuTrigger?.addEventListener('click', (event) => {
  event.stopPropagation();
  const isOpen = userDropdownMenu?.classList.toggle('is-open');
  userDropdownMenu?.setAttribute('aria-hidden', String(!isOpen));
  userMenuTrigger.setAttribute('aria-expanded', String(!!isOpen));
});

document.addEventListener('click', (event) => {
  if (!event.target.closest('#user-menu-wrap')) closeUserDropdown();
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') closeUserDropdown();
});

/* ─── SIGN OUT ────────────────────────────────────────────── */
document.querySelectorAll('.js-account-logout').forEach(button => {
  button.addEventListener('click', async () => {
    button.disabled = true;
    try {
      await fetch('auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' }),
      });
    } catch (error) {
      // Even if the request fails, reload to the signed-out header
    }
    window.location.href = 'index.php';
  });
});

accountClose?.addEventListener('click', closeAccountModal);
document.querySelectorAll('.account-tab-btn').forEach((tab) => {
  tab.addEventListener('click', () => setAccountMode(tab.dataset.tabMode || 'signin'));
});
accountToggle?.addEventListener('click', () => {
  setAccountMode(accountMode === 'signup' || accountMode === 'reset' ? 'signin' : 'signup');
});
accountForgot?.addEventListener('click', () => {
  setAccountMode('reset');
  document.getElementById('account-email')?.focus();
});
accountModal?.addEventListener('click', (event) => {
  if (event.target === accountModal) closeAccountModal();
});

accountForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const email = document.getElementById('account-email');
  const password = document.getElementById('account-password');
  const name = document.getElementById('account-name');
  const confirmPassword = document.getElementById('account-confirm-password');
  const resetPassword = document.getElementById('account-reset-password');
  const resetConfirm = document.getElementById('account-reset-confirm');

  if (accountMode === 'signup' && !name?.value.trim()) {
    accountMsg.textContent = 'Enter your full name.';
    accountMsg.className = 'account-form-message is-error';
    name?.focus();
    return;
  }

  if (accountMode === 'reset' && (!resetPassword?.value || resetPassword.value.length < 6)) {
    accountMsg.textContent = 'New password must be at least 6 characters.';
    accountMsg.className = 'account-form-message is-error';
    resetPassword?.focus();
    return;
  }

  if (accountMode === 'reset' && !password?.value) {
    accountMsg.textContent = 'Enter your current password to reset it.';
    accountMsg.className = 'account-form-message is-error';
    password?.focus();
    return;
  }

  if (accountMode === 'reset' && resetPassword.value !== resetConfirm?.value) {
    accountMsg.textContent = 'New passwords do not match.';
    accountMsg.className = 'account-form-message is-error';
    resetConfirm?.focus();
    return;
  }

  if (!email?.value.trim() || !isValidEmail(email.value.trim())) {
    accountMsg.textContent = 'Enter a valid email address.';
    accountMsg.className = 'account-form-message is-error';
    email?.focus();
    return;
  }

  if (!password?.value || password.value.length < 6) {
    accountMsg.textContent = 'Password must be at least 6 characters.';
    accountMsg.className = 'account-form-message is-error';
    password?.focus();
    return;
  }

  if (accountMode === 'signup' && password.value !== confirmPassword?.value) {
    accountMsg.textContent = 'Passwords do not match.';
    accountMsg.className = 'account-form-message is-error';
    confirmPassword?.focus();
    return;
  }

  const payload = {
    action: accountMode === 'signup' ? 'register' : (accountMode === 'reset' ? 'reset_password' : 'login'),
    name: name?.value.trim() || '',
    email: email.value.trim(),
    password: password.value,
    confirm_password: confirmPassword?.value || '',
    new_password: resetPassword?.value || '',
    new_password_confirm: resetConfirm?.value || '',
  };

  accountSubmit.disabled = true;
  accountSubmit.textContent = accountMode === 'signup' ? 'Creating Account...' : (accountMode === 'reset' ? 'Resetting Password...' : 'Signing In...');
  accountMsg.textContent = 'Please wait...';
  accountMsg.className = 'account-form-message';

  try {
    const response = await fetch('auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Authentication failed.');
    }

    accountMsg.textContent = result.message;
    accountMsg.className = 'account-form-message is-success';
    accountForm.reset();

    if (accountMode === 'signup') {
      // Show success briefly, then reload so the header reflects the new session
      setTimeout(() => {
        window.location.reload();
      }, 900);
    } else if (accountMode === 'login') {
      // Reload so the server-rendered header shows the signed-in state
      setTimeout(() => {
        window.location.reload();
      }, 400);
    }
  } catch (error) {
    accountMsg.textContent = error.message || 'Unable to connect to the authentication service.';
    accountMsg.className = 'account-form-message is-error';
  } finally {
    accountSubmit.disabled = false;
    accountSubmit.textContent = accountMode === 'signup' ? 'Create Account' : (accountMode === 'reset' ? 'Reset Password' : 'Sign In');
  }
});

/* ─── HEADER SCROLL BEHAVIOR ─────────────────────────────── */
let lastScroll = 0;

function onScroll() {
  const current = window.scrollY;

  // Scrolled state: solid background
  if (current > 60) {
    header.classList.add('is-scrolled');
  } else {
    header.classList.remove('is-scrolled');
  }

  // Scroll progress bar
  if (scrollProg) {
    const docH   = document.documentElement.scrollHeight - window.innerHeight;
    const pct    = docH > 0 ? (current / docH) * 100 : 0;
    scrollProg.style.width = pct + '%';
  }

  lastScroll = current;
}

window.addEventListener('scroll', onScroll, { passive: true });
onScroll(); // run on load

/* ─── LOADER FAILSAFE ────────────────────────────────────── */
/* Independent of GSAP: if the entrance timeline never completes
   (CDN hang/error), force-dismiss the loader after 4s so content
   is never trapped behind the overlay. */
setTimeout(() => {
  const loader = document.getElementById('page-loader');
  if (loader && !loader.classList.contains('is-complete')) {
    loader.classList.add('is-complete');
    loader.style.opacity = '0';
    loader.style.pointerEvents = 'none';
  }
}, 4000);

/* ─── MOBILE DRAWER ──────────────────────────────────────── */
function openDrawer() {
  drawer.classList.add('is-open');
  backdrop.classList.add('is-open');
  drawer.setAttribute('aria-hidden', 'false');
  mobileBtn.setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
  // Focus management
  drawerClose?.focus();
}

function closeDrawer() {
  drawer.classList.remove('is-open');
  backdrop.classList.remove('is-open');
  drawer.setAttribute('aria-hidden', 'true');
  mobileBtn.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
  mobileBtn?.focus();
}

mobileBtn?.addEventListener('click', openDrawer);
drawerClose?.addEventListener('click', closeDrawer);
backdrop?.addEventListener('click', closeDrawer);

// Close on Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
    closeDrawer();
  }
});

// Trap focus in drawer when open
drawer?.addEventListener('keydown', (e) => {
  if (e.key !== 'Tab') return;
  const focusables = drawer.querySelectorAll(
    'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
  );
  const first = focusables[0];
  const last  = focusables[focusables.length - 1];
  if (e.shiftKey && document.activeElement === first) {
    e.preventDefault();
    last.focus();
  } else if (!e.shiftKey && document.activeElement === last) {
    e.preventDefault();
    first.focus();
  }
});

/* ─── CART COUNT ─────────────────────────────────────────── */
function updateCartCount(delta = 1) {
  Nova.cartCount = Math.max(0, Nova.cartCount + delta);
  if (cartCountEl) {
    cartCountEl.textContent = Nova.cartCount;
    cartCountEl.style.transform = 'scale(1.4)';
    setTimeout(() => { cartCountEl.style.transform = ''; }, 200);
  }
}

/* ─── CART FEEDBACK TOAST ────────────────────────────────── */
const cartFeedback    = document.getElementById('cart-feedback');
const cartFeedbackMsg = document.getElementById('cart-feedback-msg');
let cartFeedbackTimer = null;

function showCartFeedback(name = 'Item') {
  if (!cartFeedback) return;
  if (cartFeedbackMsg) cartFeedbackMsg.textContent = `"${name}" added to cart!`;
  cartFeedback.classList.add('is-visible');
  clearTimeout(cartFeedbackTimer);
  cartFeedbackTimer = setTimeout(() => {
    cartFeedback.classList.remove('is-visible');
  }, 3000);
}

/* ─── ADD TO CART ────────────────────────────────────────── */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.product-add-btn');
  if (!btn) return;
  const card = btn.closest('.product-card');
  const name = card?.querySelector('.product-name')?.textContent?.trim() || 'Item';
  addCartItem({ id: btn.dataset.id || card?.dataset.productId, name, price: card?.dataset.price || 0, image: card?.querySelector('.product-img-primary')?.src || '' });
  showCartFeedback(name);

  // Brief button feedback
  const original = btn.textContent;
  btn.textContent = '✓ Added';
  btn.style.background = '#16a34a';
  setTimeout(() => {
    btn.textContent = original;
    btn.style.background = '';
  }, 1800);
});

/* ─── SMOOTH SCROLL FOR ANCHOR LINKS ─────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', (e) => {
    const target = document.querySelector(anchor.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    const offset = parseInt(getComputedStyle(document.documentElement)
      .getPropertyValue('--header-height') || '72');
    const top = target.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top, behavior: 'smooth' });
    closeDrawer();
  });
});

/* ─── NEWSLETTER FORM ────────────────────────────────────── */
const newsletterForms = document.querySelectorAll('.newsletter-form');

newsletterForms.forEach(form => {
  const input   = form.querySelector('.newsletter-input');
  const btn     = form.querySelector('.newsletter-btn');
  const msgEl   = form.querySelector('.newsletter-msg');

  if (!input || !btn) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const email = input.value.trim();

    if (!email) {
      showNewsletterState(input, msgEl, 'error', 'Please enter your email address.');
      return;
    }

    if (!isValidEmail(email)) {
      showNewsletterState(input, msgEl, 'error', 'Please enter a valid email address.');
      return;
    }

    // Simulate success (Phase 2: real API call)
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>';
    setTimeout(() => {
      showNewsletterState(input, msgEl, 'success', '🎉 You\'re on the list! Welcome to NOVA.');
      input.value = '';
      btn.disabled = false;
      btn.innerHTML = 'Subscribe';
    }, 1200);
  });
});

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showNewsletterState(input, msgEl, type, text) {
  if (!msgEl) return;
  // Reset
  input.classList.remove('newsletter-input--error', 'newsletter-input--success');
  msgEl.className = 'newsletter-msg';
  msgEl.style.display = 'none';

  // Apply
  if (type === 'error') {
    input.classList.add('newsletter-input--error');
    msgEl.classList.add('newsletter-msg--error');
  } else {
    input.classList.add('newsletter-input--success');
    msgEl.classList.add('newsletter-msg--success');
  }
  msgEl.textContent = text;
  msgEl.style.display = 'flex';
}

/* ─── LAZY IMAGE LOADING ─────────────────────────────────── */
if ('IntersectionObserver' in window) {
  const imgObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        if (img.dataset.src) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        }
        obs.unobserve(img);
      }
    });
  }, { rootMargin: '200px 0px' });

  document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
}

/* ─── EXPOSE GLOBALS ─────────────────────────────────────── */
window.NovaApp = { updateCartCount, addCartItem, getCartItems, showCartFeedback, Nova };
