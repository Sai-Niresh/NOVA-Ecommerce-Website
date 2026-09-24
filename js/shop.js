/**
 * NOVA — js/shop.js
 * Multi-criteria filter, search, sort, dynamic rendering, and grid view manager
 */

'use strict';

(function initShopEngine() {
  // Wait for DOM
  document.addEventListener('DOMContentLoaded', () => {
    // Check if shop grid exists on current page
    const gridEl = document.getElementById('shop-product-grid');
    if (!gridEl) return;

    // Filter State
    const state = {
      searchQuery: '',
      category: 'all',
      minPrice: 0,
      maxPrice: 20000,
      selectedSizes: new Set(),
      selectedColors: new Set(),
      inStockOnly: false,
      onSaleOnly: false,
      minDiscount: 0,
      sortBy: 'featured',
      gridCols: 3,
      itemsPerPage: 12,
      displayedCount: 12,
    };

    // Product Database reference
    let rawProducts = window.NOVA_PRODUCTS_DATA || [];

    // DOM Elements
    const countInfoEl      = document.getElementById('shop-count-info');
    const skeletonEl       = document.getElementById('shop-skeleton-grid');
    const emptyStateEl     = document.getElementById('shop-empty-state');
    const activeFiltersEl  = document.getElementById('active-filters-bar');
    const activeChipsEl    = document.getElementById('active-chips-container');
    const sortSelectEl     = document.getElementById('shop-sort-select');
    const searchInputEl    = document.getElementById('sidebar-search-input');
    const priceSliderEl    = document.getElementById('price-range-slider');
    const minPriceInputEl  = document.getElementById('price-min-input');
    const maxPriceInputEl  = document.getElementById('price-max-input');
    const loadMoreBtn      = document.getElementById('load-more-btn');
    const mobileFilterBtn  = document.getElementById('mobile-filter-trigger');
    const sidebarEl        = document.getElementById('shop-sidebar');

    /**
     * Parse initial URL Parameters
     */
    function parseURLParams() {
      const params = new URLSearchParams(window.location.search);

      if (params.has('c')) {
        state.category = params.get('c').toLowerCase();
      } else if (window.NOVA_CURRENT_CATEGORY) {
        state.category = window.NOVA_CURRENT_CATEGORY.toLowerCase();
      }

      if (params.has('search')) {
        state.searchQuery = params.get('search').trim();
        if (searchInputEl) searchInputEl.value = state.searchQuery;
      }

      if (params.has('sort')) {
        state.sortBy = params.get('sort');
        if (sortSelectEl) sortSelectEl.value = state.sortBy;
      }

      if (params.has('min_price')) state.minPrice = parseInt(params.get('min_price')) || 0;
      if (params.has('max_price')) state.maxPrice = parseInt(params.get('max_price')) || 20000;
    }

    /**
     * Filter Products Array based on current state
     */
    function getFilteredProducts() {
      return rawProducts.filter(product => {
        // Search query
        if (state.searchQuery) {
          const q = state.searchQuery.toLowerCase();
          const matchName = product.name.toLowerCase().includes(q);
          const matchCat  = product.category.toLowerCase().includes(q);
          const matchDesc = (product.description || '').toLowerCase().includes(q);
          if (!matchName && !matchCat && !matchDesc) return false;
        }

        // Category
        if (state.category !== 'all' && product.category.toLowerCase() !== state.category) {
          return false;
        }

        // Price Range
        if (product.price < state.minPrice || product.price > state.maxPrice) {
          return false;
        }

        // Sizes
        if (state.selectedSizes.size > 0) {
          const hasSize = product.sizes && product.sizes.some(s => state.selectedSizes.has(s));
          if (!hasSize) return false;
        }

        // Colors
        if (state.selectedColors.size > 0) {
          const hasColor = product.colors && product.colors.some(c => state.selectedColors.has(c.name));
          if (!hasColor) return false;
        }

        // In Stock
        if (state.inStockOnly && product.stock <= 0) {
          return false;
        }

        // On Sale
        if (state.onSaleOnly && (!product.original_price || product.original_price <= product.price)) {
          return false;
        }

        // Discount
        if (state.minDiscount > 0) {
          const disc = product.discount || (product.original_price ? Math.round(((product.original_price - product.price) / product.original_price) * 100) : 0);
          if (disc < state.minDiscount) return false;
        }

        return true;
      });
    }

    /**
     * Sort Products Array
     */
    function sortProducts(products) {
      const list = [...products];
      switch (state.sortBy) {
        case 'price-asc':
          return list.sort((a, b) => a.price - b.price);
        case 'price-desc':
          return list.sort((a, b) => b.price - a.price);
        case 'newest':
          return list.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
        case 'rating':
          return list.sort((a, b) => b.rating - a.rating);
        case 'featured':
        default:
          return list.sort((a, b) => (b.featured ? 1 : 0) - (a.featured ? 1 : 0));
      }
    }

    /**
     * Render Product Card HTML String matching product-card.php
     */
    function buildProductCardHTML(p) {
      const sym = window.NOVA_CURRENCY_SYMBOL || '₹';
      const hasDisc = p.original_price && p.original_price > p.price;
      const disc = hasDisc ? (p.discount || Math.round(((p.original_price - p.price) / p.original_price) * 100)) : 0;
      const secImg = p.secondary_image && p.secondary_image !== p.image ? p.secondary_image : p.image;

      let badgeHTML = '';
      if (hasDisc) {
        badgeHTML = `<span class="product-badge product-badge--sale">${disc}% OFF</span>`;
      } else if (p.badge) {
        const badgeClass = p.badge.toLowerCase() === 'new' ? 'product-badge--new' : (p.badge.toLowerCase() === 'trending' ? 'product-badge--trending' : 'product-badge--limited');
        badgeHTML = `<span class="product-badge ${badgeClass}">${p.badge}</span>`;
      }

      let secImgHTML = '';
      if (secImg !== p.image) {
        secImgHTML = `<img class="product-img product-img-secondary" src="${secImg}" alt="${p.name} alt view" loading="lazy" width="400" height="500">`;
      }

      let colorDotsHTML = '';
      if (p.colors && p.colors.length) {
        colorDotsHTML = '<div class="product-color-preview">';
        p.colors.slice(0, 4).forEach(col => {
          colorDotsHTML += `<span class="color-dot" style="background-color: ${col.hex}" title="${col.name}"></span>`;
        });
        if (p.colors.length > 4) {
          colorDotsHTML += `<span class="color-dot-more">+${p.colors.length - 4}</span>`;
        }
        colorDotsHTML += '</div>';
      }

      return `
        <article class="product-card" data-product-id="${p.id}" data-category="${p.category}" data-price="${p.price}" data-rating="${p.rating}" aria-label="${p.name}">
          <div class="product-image-wrapper">
            ${badgeHTML}
            <img class="product-img product-img-primary" src="${p.image}" alt="${p.name}" loading="lazy" width="400" height="500">
            ${secImgHTML}
            <button class="product-wishlist-btn" data-id="${p.id}" aria-label="Add ${p.name} to wishlist">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </button>
            <div class="product-card-actions">
              <button class="product-quickview-btn" data-id="${p.id}" aria-label="Quick View ${p.name}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>Quick View</span>
              </button>
            </div>
          </div>
          <div class="product-info">
            <span class="product-category-label">${p.category}</span>
            <h3 class="product-name"><a href="product.php?id=${encodeURIComponent(p.id)}">${p.name}</a></h3>
            <div class="product-price-row">
              <span class="product-price-current">${sym}${Number(p.price).toLocaleString()}</span>
              ${hasDisc ? `<span class="product-price-original">${sym}${Number(p.original_price).toLocaleString()}</span><span class="product-discount">${disc}% off</span>` : ''}
            </div>
            ${colorDotsHTML}
            <button class="product-add-btn" data-id="${p.id}" aria-label="Add ${p.name} to cart">Add to Cart</button>
          </div>
        </article>
      `;
    }

    /**
     * Render Active Filter Chips Bar
     */
    function renderActiveFilters() {
      if (!activeChipsEl || !activeFiltersEl) return;

      const chips = [];

      if (state.category !== 'all') {
        chips.push({ key: 'category', label: `Category: ${state.category}` });
      }
      if (state.searchQuery) {
        chips.push({ key: 'search', label: `Search: "${state.searchQuery}"` });
      }
      if (state.minPrice > 0 || state.maxPrice < 20000) {
        chips.push({ key: 'price', label: `Price: ₹${state.minPrice.toLocaleString()} - ₹${state.maxPrice.toLocaleString()}` });
      }
      state.selectedSizes.forEach(sz => {
        chips.push({ key: 'size', value: sz, label: `Size: ${sz}` });
      });
      state.selectedColors.forEach(col => {
        chips.push({ key: 'color', value: col, label: `Color: ${col}` });
      });
      if (state.inStockOnly) {
        chips.push({ key: 'instock', label: 'In Stock Only' });
      }
      if (state.onSaleOnly) {
        chips.push({ key: 'onsale', label: 'On Sale' });
      }
      if (state.minDiscount > 0) {
        chips.push({ key: 'discount', label: `${state.minDiscount}%+ Off` });
      }

      if (chips.length > 0) {
        activeFiltersEl.style.display = 'flex';
        activeChipsEl.innerHTML = chips.map((c, i) => `
          <span class="filter-chip">
            ${c.label}
            <button type="button" class="filter-chip-remove" data-chip-key="${c.key}" data-chip-val="${c.value || ''}" aria-label="Remove filter">✕</button>
          </span>
        `).join('');
      } else {
        activeFiltersEl.style.display = 'none';
        activeChipsEl.innerHTML = '';
      }
    }

    /**
     * Update Category Buttons Active States
     */
    function updateCategoryButtons() {
      document.querySelectorAll('.filter-cat-btn').forEach(btn => {
        const cat = btn.dataset.category || 'all';
        if (cat.toLowerCase() === state.category.toLowerCase()) {
          btn.classList.add('is-active');
        } else {
          btn.classList.remove('is-active');
        }
      });
    }

    /**
     * Main Render Loop
     */
    function renderGrid(animate = true) {
      const filtered = getFilteredProducts();
      const sorted = sortProducts(filtered);

      // Update count info
      if (countInfoEl) {
        const total = sorted.length;
        const visible = Math.min(state.displayedCount, total);
        countInfoEl.innerHTML = total > 0 ? `Showing <strong>${visible}</strong> of <strong>${total}</strong> products` : 'No products found';
      }

      // Handle empty state
      if (sorted.length === 0) {
        gridEl.style.display = 'none';
        if (skeletonEl) skeletonEl.style.display = 'none';
        if (emptyStateEl) emptyStateEl.style.display = 'flex';
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        renderActiveFilters();
        updateCategoryButtons();
        return;
      }

      // Hide empty state & show grid
      if (emptyStateEl) emptyStateEl.style.display = 'none';
      gridEl.style.display = 'grid';

      // Slice for pagination
      const visibleProducts = sorted.slice(0, state.displayedCount);

      // Render cards
      gridEl.innerHTML = visibleProducts.map(p => buildProductCardHTML(p)).join('');

      // Show/Hide Load More Button
      if (loadMoreBtn) {
        if (sorted.length > state.displayedCount) {
          loadMoreBtn.style.display = 'inline-flex';
          loadMoreBtn.textContent = `${sorted.length - state.displayedCount} LEFT`;
        } else {
          loadMoreBtn.style.display = 'none';
        }
      }

      renderActiveFilters();
      updateCategoryButtons();

      // GSAP entrance animation on cards
      if (animate && window.gsap) {
        const cards = gridEl.querySelectorAll('.product-card');
        gsap.fromTo(cards,
          { opacity: 0, y: 20 },
          { opacity: 1, y: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out' }
        );
      }
    }

    /**
     * Reset All Filters
     */
    function resetAllFilters() {
      state.searchQuery = '';
      state.category = 'all';
      state.minPrice = 0;
      state.maxPrice = 20000;
      state.selectedSizes.clear();
      state.selectedColors.clear();
      state.inStockOnly = false;
      state.onSaleOnly = false;
      state.minDiscount = 0;
      state.displayedCount = state.itemsPerPage;

      if (searchInputEl) searchInputEl.value = '';
      if (priceSliderEl) priceSliderEl.value = 20000;
      if (minPriceInputEl) minPriceInputEl.value = 0;
      if (maxPriceInputEl) maxPriceInputEl.value = 20000;

      document.querySelectorAll('.size-chip').forEach(c => c.classList.remove('is-selected'));
      document.querySelectorAll('.color-swatch-item').forEach(c => c.classList.remove('is-selected'));
      document.querySelectorAll('.filter-checkbox-list input').forEach(cb => cb.checked = false);

      renderGrid(true);
    }

    /**
     * Event Listeners Setup
     */

    // Search input
    let searchDebounce = null;
    searchInputEl?.addEventListener('input', (e) => {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(() => {
        state.searchQuery = e.target.value.trim();
        state.displayedCount = state.itemsPerPage;
        renderGrid(true);
      }, 250);
    });

    // Category click
    document.addEventListener('click', (e) => {
      const catBtn = e.target.closest('.filter-cat-btn');
      if (!catBtn) return;
      e.preventDefault();
      state.category = (catBtn.dataset.category || 'all').toLowerCase();
      state.displayedCount = state.itemsPerPage;
      renderGrid(true);
    });

    // Sort Select
    sortSelectEl?.addEventListener('change', (e) => {
      state.sortBy = e.target.value;
      renderGrid(true);
    });

    // Price Slider & Inputs
    priceSliderEl?.addEventListener('input', (e) => {
      state.maxPrice = parseInt(e.target.value) || 20000;
      if (maxPriceInputEl) maxPriceInputEl.value = state.maxPrice;
      renderGrid(false);
    });

    minPriceInputEl?.addEventListener('change', (e) => {
      state.minPrice = parseInt(e.target.value) || 0;
      renderGrid(true);
    });

    maxPriceInputEl?.addEventListener('change', (e) => {
      state.maxPrice = parseInt(e.target.value) || 20000;
      if (priceSliderEl) priceSliderEl.value = state.maxPrice;
      renderGrid(true);
    });

    // Size Chips
    document.querySelectorAll('#size-filter-container .size-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const val = chip.dataset.size || chip.textContent.trim();
        if (state.selectedSizes.has(val)) {
          state.selectedSizes.delete(val);
          chip.classList.remove('is-selected');
        } else {
          state.selectedSizes.add(val);
          chip.classList.add('is-selected');
        }
        renderGrid(true);
      });
    });

    // Color Swatches
    document.querySelectorAll('#color-filter-container .color-swatch-item').forEach(swatch => {
      swatch.addEventListener('click', () => {
        const colName = swatch.dataset.colorName;
        if (state.selectedColors.has(colName)) {
          state.selectedColors.delete(colName);
          swatch.classList.remove('is-selected');
        } else {
          state.selectedColors.add(colName);
          swatch.classList.add('is-selected');
        }
        renderGrid(true);
      });
    });

    // Checkboxes (Stock, Sale, Discount)
    document.getElementById('filter-instock')?.addEventListener('change', (e) => {
      state.inStockOnly = e.target.checked;
      renderGrid(true);
    });

    document.getElementById('filter-onsale')?.addEventListener('change', (e) => {
      state.onSaleOnly = e.target.checked;
      renderGrid(true);
    });

    // Active Chip Removal
    activeChipsEl?.addEventListener('click', (e) => {
      const btn = e.target.closest('.filter-chip-remove');
      if (!btn) return;
      const key = btn.dataset.chipKey;
      const val = btn.dataset.chipVal;

      if (key === 'category') state.category = 'all';
      if (key === 'search') { state.searchQuery = ''; if (searchInputEl) searchInputEl.value = ''; }
      if (key === 'price') { state.minPrice = 0; state.maxPrice = 20000; }
      if (key === 'size') state.selectedSizes.delete(val);
      if (key === 'color') state.selectedColors.delete(val);
      if (key === 'instock') { state.inStockOnly = false; const el = document.getElementById('filter-instock'); if (el) el.checked = false; }
      if (key === 'onsale') { state.onSaleOnly = false; const el = document.getElementById('filter-onsale'); if (el) el.checked = false; }

      renderGrid(true);
    });

    // Clear All Filters button click
    document.getElementById('clear-all-filters-btn')?.addEventListener('click', resetAllFilters);
    document.getElementById('reset-empty-btn')?.addEventListener('click', resetAllFilters);

    // Load More Button
    loadMoreBtn?.addEventListener('click', () => {
      state.displayedCount += state.itemsPerPage;
      renderGrid(false);
    });

    // Grid Column Switcher (3-col vs 4-col)
    document.getElementById('view-btn-3')?.addEventListener('click', function() {
      state.gridCols = 3;
      document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('is-active'));
      this.classList.add('is-active');
      gridEl.className = 'shop-product-grid grid-3-col';
    });

    document.getElementById('view-btn-4')?.addEventListener('click', function() {
      state.gridCols = 4;
      document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('is-active'));
      this.classList.add('is-active');
      gridEl.className = 'shop-product-grid grid-4-col';
    });

    // Mobile Filter Drawer Toggle
    mobileFilterBtn?.addEventListener('click', () => {
      if (!sidebarEl) return;
      sidebarEl.classList.toggle('is-mobile-open');
    });

    // Initial Execution
    parseURLParams();
    renderGrid(true);
  });
})();
