/**
 * NOVA — js/quick-view.js
 * GSAP-powered interactive Quick View modal system
 */

'use strict';

(function initQuickView() {
  // Global modal state
  let currentProduct = null;
  let selectedColor = null;
  let selectedSize = null;
  let currentQty = 1;

  // DOM Elements
  let overlay = document.getElementById('quick-view-overlay');

  /**
   * Ensures Quick View modal markup exists in DOM
   */
  function createModalDOM() {
    if (overlay) return;

    overlay = document.createElement('div');
    overlay.id = 'quick-view-overlay';
    overlay.className = 'quick-view-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');

    overlay.innerHTML = `
      <div class="quick-view-dialog" id="quick-view-dialog">
        <button class="quick-view-close" id="quick-view-close" aria-label="Close modal">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="qv-grid">
          <!-- Left Column: Gallery -->
          <div class="qv-image-column">
            <div class="qv-gallery-main">
              <img id="qv-main-img" src="" alt="Product view" loading="eager" width="450" height="562">
            </div>
            <div class="qv-gallery-thumbs" id="qv-thumbs-container"></div>
          </div>

          <!-- Right Column: Product Details -->
          <div class="qv-info">
            <span class="qv-category" id="qv-category">Category</span>
            <h2 class="qv-title" id="qv-title">Product Name</h2>

            <div class="qv-rating-row" id="qv-rating-container"></div>

            <div class="qv-price-row">
              <span class="qv-price-current" id="qv-price-current">₹0</span>
              <span class="qv-price-original" id="qv-price-original"></span>
              <span class="qv-discount-tag" id="qv-discount-tag"></span>
            </div>

            <p class="qv-desc" id="qv-desc"></p>

            <!-- Colors -->
            <div id="qv-colors-wrapper">
              <span class="qv-section-title">Color: <strong id="qv-color-name">Default</strong></span>
              <div class="qv-color-list" id="qv-colors-container"></div>
            </div>

            <!-- Sizes -->
            <div id="qv-sizes-wrapper">
              <span class="qv-section-title">Size: <strong id="qv-size-name">Select Size</strong></span>
              <div class="qv-size-list" id="qv-sizes-container"></div>
            </div>

            <!-- Quantity -->
            <div class="qv-qty-row">
              <span class="qv-section-title">Quantity</span>
              <div class="qv-qty-selector">
                <button class="qv-qty-btn" id="qv-qty-minus" aria-label="Decrease quantity">-</button>
                <span class="qv-qty-val" id="qv-qty-val">1</span>
                <button class="qv-qty-btn" id="qv-qty-plus" aria-label="Increase quantity">+</button>
              </div>
            </div>

            <!-- Actions -->
            <div class="qv-cta-group">
              <button class="btn btn-primary qv-add-cart-btn" id="qv-add-cart">
                <span>Add to Cart</span>
              </button>
              <button class="btn btn-outline product-wishlist-btn" id="qv-wishlist-btn" aria-label="Wishlist">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(overlay);

    // Event bindings for modal close
    document.getElementById('quick-view-close')?.addEventListener('click', closeQuickView);
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeQuickView();
    });

    // Quantity buttons
    document.getElementById('qv-qty-minus')?.addEventListener('click', () => {
      if (currentQty > 1) {
        currentQty--;
        document.getElementById('qv-qty-val').textContent = currentQty;
      }
    });

    document.getElementById('qv-qty-plus')?.addEventListener('click', () => {
      currentQty++;
      document.getElementById('qv-qty-val').textContent = currentQty;
    });

    // Add to Cart inside modal
    document.getElementById('qv-add-cart')?.addEventListener('click', () => {
      if (!currentProduct) return;
      if (window.NovaApp && typeof window.NovaApp.updateCartCount === 'function') {
        window.NovaApp.updateCartCount(currentQty);
        window.NovaApp.showCartFeedback(`${currentProduct.name} (${selectedColor || ''} ${selectedSize || ''})`);
      }
      closeQuickView();
    });
  }

  /**
   * Opens Quick View modal for given product ID
   */
  function openQuickView(productId) {
    createModalDOM();

    // Fetch product data from window array or DOM
    const products = window.NOVA_PRODUCTS_DATA || [];
    currentProduct = products.find(p => p.id === parseInt(productId)) || null;

    if (!currentProduct) {
      console.warn('Product data not found for ID:', productId);
      return;
    }

    // Reset selection state
    currentQty = 1;
    selectedColor = currentProduct.colors && currentProduct.colors.length ? currentProduct.colors[0].name : '';
    selectedSize = currentProduct.sizes && currentProduct.sizes.length ? currentProduct.sizes[0] : '';

    // Populate Modal Content
    document.getElementById('qv-main-img').src = currentProduct.image;
    document.getElementById('qv-main-img').alt = currentProduct.name;
    document.getElementById('qv-category').textContent = currentProduct.category;
    document.getElementById('qv-title').textContent = currentProduct.name;
    document.getElementById('qv-desc').textContent = currentProduct.description || 'Premium design with fine craftsmanship.';
    document.getElementById('qv-qty-val').textContent = currentQty;

    // Currency Formatting
    const sym = window.NOVA_CURRENCY_SYMBOL || '₹';
    document.getElementById('qv-price-current').textContent = sym + Number(currentProduct.price).toLocaleString();

    const origEl = document.getElementById('qv-price-original');
    const discEl = document.getElementById('qv-discount-tag');

    if (currentProduct.original_price && currentProduct.original_price > currentProduct.price) {
      origEl.textContent = sym + Number(currentProduct.original_price).toLocaleString();
      origEl.style.display = 'inline';
      const pct = currentProduct.discount || Math.round(((currentProduct.original_price - currentProduct.price) / currentProduct.original_price) * 100);
      discEl.textContent = `${pct}% OFF`;
      discEl.style.display = 'inline';
    } else {
      origEl.style.display = 'none';
      discEl.style.display = 'none';
    }

    // Rating Stars
    const ratingContainer = document.getElementById('qv-rating-container');
    ratingContainer.innerHTML = '';
    const roundedRating = Math.round(currentProduct.rating || 5);
    let starsHTML = '<div class="stars" style="display:flex; gap:2px; color:var(--nova-accent);">';
    for (let i = 1; i <= 5; i++) {
      const fill = i <= roundedRating ? 'currentColor' : 'none';
      starsHTML += `<svg width="14" height="14" viewBox="0 0 24 24" fill="${fill}" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`;
    }
    starsHTML += `</div><span style="font-size:0.8rem; color:var(--nova-gray-600); font-weight:600;">(${currentProduct.reviews || 0} reviews)</span>`;
    ratingContainer.innerHTML = starsHTML;

    // Thumbnails Gallery
    const thumbsContainer = document.getElementById('qv-thumbs-container');
    thumbsContainer.innerHTML = '';
    const images = [currentProduct.image];
    if (currentProduct.secondary_image && currentProduct.secondary_image !== currentProduct.image) {
      images.push(currentProduct.secondary_image);
    }

    images.forEach((imgUrl, index) => {
      const thumb = document.createElement('div');
      thumb.className = `qv-thumb ${index === 0 ? 'is-active' : ''}`;
      thumb.innerHTML = `<img src="${imgUrl}" alt="Thumbnail ${index + 1}">`;
      thumb.addEventListener('click', () => {
        document.querySelectorAll('.qv-thumb').forEach(t => t.classList.remove('is-active'));
        thumb.classList.add('is-active');
        document.getElementById('qv-main-img').src = imgUrl;
      });
      thumbsContainer.appendChild(thumb);
    });

    // Colors
    const colorsContainer = document.getElementById('qv-colors-container');
    const colorNameEl = document.getElementById('qv-color-name');
    colorsContainer.innerHTML = '';

    if (currentProduct.colors && currentProduct.colors.length) {
      document.getElementById('qv-colors-wrapper').style.display = 'block';
      colorNameEl.textContent = selectedColor;

      currentProduct.colors.forEach((col, idx) => {
        const item = document.createElement('div');
        item.className = `color-swatch-item ${col.name === selectedColor ? 'is-selected' : ''}`;
        item.style.backgroundColor = col.hex;
        item.title = col.name;
        item.addEventListener('click', () => {
          selectedColor = col.name;
          colorNameEl.textContent = selectedColor;
          document.querySelectorAll('#qv-colors-container .color-swatch-item').forEach(s => s.classList.remove('is-selected'));
          item.classList.add('is-selected');
        });
        colorsContainer.appendChild(item);
      });
    } else {
      document.getElementById('qv-colors-wrapper').style.display = 'none';
    }

    // Sizes
    const sizesContainer = document.getElementById('qv-sizes-container');
    const sizeNameEl = document.getElementById('qv-size-name');
    sizesContainer.innerHTML = '';

    if (currentProduct.sizes && currentProduct.sizes.length) {
      document.getElementById('qv-sizes-wrapper').style.display = 'block';
      sizeNameEl.textContent = selectedSize;

      currentProduct.sizes.forEach((sz) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `size-chip ${sz === selectedSize ? 'is-selected' : ''}`;
        btn.textContent = sz;
        btn.addEventListener('click', () => {
          selectedSize = sz;
          sizeNameEl.textContent = selectedSize;
          document.querySelectorAll('#qv-sizes-container .size-chip').forEach(s => s.classList.remove('is-selected'));
          btn.classList.add('is-selected');
        });
        sizesContainer.appendChild(btn);
      });
    } else {
      document.getElementById('qv-sizes-wrapper').style.display = 'none';
    }

    // Show overlay and animate with GSAP if available
    overlay.classList.add('is-active');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    if (window.gsap) {
      const dialog = document.getElementById('quick-view-dialog');
      gsap.fromTo(overlay, { opacity: 0 }, { opacity: 1, duration: 0.25, ease: 'power2.out' });
      gsap.fromTo(dialog,
        { scale: 0.9, y: 30, opacity: 0 },
        { scale: 1, y: 0, opacity: 1, duration: 0.4, ease: 'power3.out' }
      );
    }
  }

  /**
   * Closes Quick View modal with GSAP animation
   */
  function closeQuickView() {
    if (!overlay || !overlay.classList.contains('is-active')) return;

    if (window.gsap) {
      const dialog = document.getElementById('quick-view-dialog');
      gsap.to(dialog, { scale: 0.94, y: 15, opacity: 0, duration: 0.2, ease: 'power2.in' });
      gsap.to(overlay, {
        opacity: 0,
        duration: 0.25,
        ease: 'power2.in',
        onComplete: () => {
          overlay.classList.remove('is-active');
          overlay.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
        }
      });
    } else {
      overlay.classList.remove('is-active');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  }

  // Global event delegation for Quick View buttons across page
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.product-quickview-btn, .product-quickview-link');
    if (!btn) return;
    e.preventDefault();
    const id = btn.dataset.id || btn.closest('.product-card')?.dataset.productId;
    if (id) openQuickView(id);
  });

  // ESC Key listener
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay && overlay.classList.contains('is-active')) {
      closeQuickView();
    }
  });

  // Expose global QuickView API
  window.NovaQuickView = { open: openQuickView, close: closeQuickView };
})();
