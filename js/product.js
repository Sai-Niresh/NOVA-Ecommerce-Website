'use strict';

(function () {
  const gallery = window.NOVA_GALLERY || [];
  const mainImage = document.getElementById('main-product-image');
  const galleryModal = document.getElementById('gallery-modal');
  const modalImage = document.getElementById('modal-image');
  const modalThumbs = document.getElementById('modal-thumbs');
  const sizeGuideModal = document.getElementById('size-guide-modal');
  const product = window.NOVA_PRODUCT || null;

  const updateRecentlyViewed = () => {
    if (!product || !product.id) return;
    try {
      const raw = localStorage.getItem('nova_recently_viewed');
      const current = raw ? JSON.parse(raw) : [];
      const ids = Array.isArray(current) ? current.filter((id) => Number(id) !== Number(product.id)) : [];
      ids.unshift(Number(product.id));
      const deduped = Array.from(new Set(ids)).slice(0, 4);
      localStorage.setItem('nova_recently_viewed', JSON.stringify(deduped));
    } catch (error) {
      // Ignore localStorage access failure in restricted environments.
    }
  };

  const state = {
    currentIndex: 0,
    quantity: 1,
    selectedColor: '',
    selectedSize: '',
    wishlist: false,
  };

  const selector = { 
    thumbButtons: Array.from(document.querySelectorAll('.thumb-btn')),
    colorOptions: Array.from(document.querySelectorAll('.color-option')),
    sizeOptions: Array.from(document.querySelectorAll('.size-option')),
    qtyValue: document.getElementById('qty-value'),
    qtyBtn: Array.from(document.querySelectorAll('.qty-btn')),
    pinInput: document.querySelector('.pin-input'),
    pinCheckBtn: document.querySelector('.pin-check-btn'),
    pinMessage: document.querySelector('.pin-message'),
    wishlistBtn: document.getElementById('wishlist-btn'),
    readMore: document.querySelector('.btn-read-more'),
    productDescription: document.querySelector('.product-description'),
    addCartBtn: document.querySelector('.product-add-cart'),
    buyNowBtn: document.querySelector('.product-buy-now'),
  };

  function setMainImage(index) {
    if (!gallery.length || !mainImage) return;
    const safeIndex = (index + gallery.length) % gallery.length;
    state.currentIndex = safeIndex;
    mainImage.src = gallery[safeIndex];
    mainImage.setAttribute('data-index', String(safeIndex));
    selector.thumbButtons.forEach((btn, i) => btn.classList.toggle('is-active', i === safeIndex));
  }

  function openModal() {
    if (!galleryModal || !modalImage) return;
    modalImage.src = gallery[state.currentIndex] || mainImage?.src;
    const modalThumbHtml = gallery.map((img, idx) => `
      <button type="button" class="modal-thumb ${idx === state.currentIndex ? 'is-active' : ''}" data-index="${idx}" aria-label="View image ${idx + 1}">
        <img src="${img}" alt="Product image ${idx + 1}">
      </button>
    `).join('');
    if (modalThumbs) modalThumbs.innerHTML = modalThumbHtml;
    galleryModal.classList.add('is-visible');
    galleryModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (!galleryModal) return;
    galleryModal.classList.remove('is-visible');
    galleryModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  function openSizeGuide() {
    if (!sizeGuideModal) return;
    sizeGuideModal.classList.add('is-visible');
    sizeGuideModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeSizeGuide() {
    if (!sizeGuideModal) return;
    sizeGuideModal.classList.remove('is-visible');
    sizeGuideModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  selector.thumbButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const index = Number(button.dataset.index || 0);
      setMainImage(index);
    });
  });

  if (mainImage) {
    mainImage.addEventListener('click', openModal);
    mainImage.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openModal();
      }
    });
    mainImage.tabIndex = 0;
  }

  document.querySelector('.gallery-zoom-btn')?.addEventListener('click', openModal);
  document.querySelector('.modal-close')?.addEventListener('click', closeModal);
  document.querySelector('.size-guide-close')?.addEventListener('click', closeSizeGuide);
  galleryModal?.addEventListener('click', (e) => {
    if (e.target === galleryModal) closeModal();
  });
  sizeGuideModal?.addEventListener('click', (e) => {
    if (e.target === sizeGuideModal) closeSizeGuide();
  });

  document.querySelector('.modal-prev')?.addEventListener('click', () => {
    setMainImage(state.currentIndex - 1);
    if (modalImage) modalImage.src = gallery[state.currentIndex] || mainImage?.src;
  });

  document.querySelector('.modal-next')?.addEventListener('click', () => {
    setMainImage(state.currentIndex + 1);
    if (modalImage) modalImage.src = gallery[state.currentIndex] || mainImage?.src;
  });

  modalThumbs?.addEventListener('click', (event) => {
    const btn = event.target.closest('.modal-thumb');
    if (!btn) return;
    const index = Number(btn.dataset.index || 0);
    setMainImage(index);
    if (modalImage) modalImage.src = gallery[state.currentIndex] || mainImage?.src;
  });

  document.addEventListener('keydown', (event) => {
    if (galleryModal && galleryModal.classList.contains('is-visible')) {
      if (event.key === 'Escape') closeModal();
      if (event.key === 'ArrowLeft') {
        setMainImage(state.currentIndex - 1);
        if (modalImage) modalImage.src = gallery[state.currentIndex] || mainImage?.src;
      }
      if (event.key === 'ArrowRight') {
        setMainImage(state.currentIndex + 1);
        if (modalImage) modalImage.src = gallery[state.currentIndex] || mainImage?.src;
      }
    }

    if (sizeGuideModal && sizeGuideModal.classList.contains('is-visible') && event.key === 'Escape') {
      closeSizeGuide();
    }
  });

  if (selector.colorOptions.length) {
    selector.colorOptions.forEach((option) => {
      option.addEventListener('click', () => {
        selector.colorOptions.forEach((item) => item.classList.remove('is-selected'));
        option.classList.add('is-selected');
        state.selectedColor = option.dataset.colorName || '';
        const selectedName = document.getElementById('selected-color-name');
        if (selectedName) selectedName.textContent = state.selectedColor;
      });
    });
  }

  if (selector.sizeOptions.length) {
    selector.sizeOptions.forEach((option) => {
      option.addEventListener('click', () => {
        selector.sizeOptions.forEach((item) => item.classList.remove('is-selected'));
        option.classList.add('is-selected');
        state.selectedSize = option.dataset.size || '';
      });
    });
  }

  selector.qtyBtn.forEach((button) => {
    button.addEventListener('click', () => {
      const action = button.dataset.action;
      const current = state.quantity;
      if (action === 'decrease') {
        state.quantity = Math.max(1, current - 1);
      }
      if (action === 'increase') {
        state.quantity = current + 1;
      }
      if (selector.qtyValue) {
        selector.qtyValue.textContent = String(state.quantity);
      }
    });
  });

  selector.pinCheckBtn?.addEventListener('click', () => {
    const val = selector.pinInput?.value.trim() || '';
    if (!val) {
      if (selector.pinMessage) {
        selector.pinMessage.textContent = 'Please enter a PIN code.';
        selector.pinMessage.classList.add('is-error');
      }
      return;
    }
    if (selector.pinMessage) {
      selector.pinMessage.textContent = 'Delivery available for this location.';
      selector.pinMessage.classList.remove('is-error');
    }
  });

  selector.wishlistBtn?.addEventListener('click', () => {
    state.wishlist = !state.wishlist;
    selector.wishlistBtn.classList.toggle('is-active', state.wishlist);
    const label = selector.wishlistBtn.querySelector('span');
    if (label) {
      label.textContent = state.wishlist ? 'Added to Wishlist' : 'Add to Wishlist';
    }
  });

  selector.readMore?.addEventListener('click', () => {
    const isExpanded = selector.readMore.getAttribute('aria-expanded') === 'true';
    selector.readMore.setAttribute('aria-expanded', String(!isExpanded));
    selector.readMore.textContent = isExpanded ? 'Read More' : 'Read Less';
    if (selector.productDescription) {
      selector.productDescription.classList.toggle('is-expanded', !isExpanded);
    }
  });

  selector.addCartBtn?.addEventListener('click', () => {
    const name = product?.name || 'Product';
    if (window.NovaApp?.addCartItem) {
      window.NovaApp.addCartItem({ id: product?.id, name, price: product?.price, image: product?.image || '' }, Number(state.quantity || 1));
    }
    const toast = document.getElementById('cart-feedback');
    const toastText = document.getElementById('cart-feedback-msg');
    if (toast && toastText) {
      toastText.textContent = `"${name}" added to cart!`;
      toast.classList.add('is-visible');
      setTimeout(() => toast.classList.remove('is-visible'), 2200);
    }
    selector.addCartBtn.textContent = '✓ Added';
    selector.addCartBtn.disabled = true;
    setTimeout(() => {
      selector.addCartBtn.textContent = 'Add to Cart';
      selector.addCartBtn.disabled = false;
    }, 1400);
  });

  selector.buyNowBtn?.addEventListener('click', () => {
    const toast = document.getElementById('cart-feedback');
    const toastText = document.getElementById('cart-feedback-msg');
    if (toast && toastText) {
      toastText.textContent = 'Buy Now selected. Checkout flow coming soon.';
      toast.classList.add('is-visible');
      setTimeout(() => toast.classList.remove('is-visible'), 2200);
    }
  });

  document.querySelectorAll('.accordion-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const item = trigger.closest('.accordion-item');
      if (!item) return;
      const isOpen = item.classList.contains('is-open');
      item.classList.toggle('is-open', !isOpen);
      trigger.setAttribute('aria-expanded', String(!isOpen));
      const icon = trigger.querySelector('.accordion-icon');
      if (icon) icon.textContent = isOpen ? '+' : '−';
    });
  });

  const sizeGuideTrigger = document.querySelector('.size-guide-trigger');
  sizeGuideTrigger?.addEventListener('click', openSizeGuide);

  updateRecentlyViewed();

  if (gallery.length) {
    setMainImage(0);
  }
})();
