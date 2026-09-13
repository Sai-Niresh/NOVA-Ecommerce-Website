(() => {
  const content = document.getElementById('cart-page-content');
  if (!content) return;

  const readCart = () => {
    try {
      const items = JSON.parse(localStorage.getItem('nova_cart') || '[]');
      return Array.isArray(items) ? items : [];
    } catch (error) {
      return [];
    }
  };

  const render = () => {
    const items = readCart();
    const count = items.reduce((total, item) => total + Number(item.quantity || 0), 0);
    const total = items.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 0), 0);

    if (!items.length) {
      content.innerHTML = '<div class="cart-empty-state"><div class="panel-state-icon">▢</div><h2>Your cart is empty</h2><p>Add a product from the shop to see it here.</p><a class="btn btn-primary" href="shop.php">Explore the shop</a></div>';
      return;
    }

    content.innerHTML = `<div class="cart-page-grid"><div class="cart-page-items"><div class="cart-items-header"><span>${count} item${count === 1 ? '' : 's'} selected</span><a href="shop.php">Continue shopping</a></div>${items.map((item) => `<article class="cart-page-item"><div class="cart-item-image"><img src="${item.image || 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=240&q=80'}" alt="${item.name}" loading="lazy"></div><div class="cart-item-details"><p class="product-category-label--detail">NOVA ESSENTIAL</p><h2>${item.name}</h2><p class="cart-item-price">₹${Number(item.price).toLocaleString('en-IN')}</p><div class="cart-item-actions"><div class="cart-quantity" data-id="${item.id}"><button type="button" data-cart-action="decrease" aria-label="Decrease quantity">−</button><span>${item.quantity}</span><button type="button" data-cart-action="increase" aria-label="Increase quantity">+</button></div><button class="cart-remove" type="button" data-cart-action="remove" data-id="${item.id}">Remove</button></div></div><strong class="cart-item-total">₹${(Number(item.price) * Number(item.quantity)).toLocaleString('en-IN')}</strong></article>`).join('')}</div><aside class="cart-summary"><p class="product-category-label--detail">ORDER SUMMARY</p><h2>Ready when you are.</h2><div class="summary-line"><span>Subtotal</span><strong>₹${total.toLocaleString('en-IN')}</strong></div><div class="summary-line"><span>Shipping</span><strong class="summary-free">Free</strong></div><div class="summary-total"><span>Total</span><strong>₹${total.toLocaleString('en-IN')}</strong></div><a class="btn btn-primary btn-large" href="checkout.php">Continue to checkout</a><p class="summary-note">Taxes and final delivery options are calculated at checkout.</p></aside></div>`;
  };

  content.addEventListener('click', (event) => {
    const action = event.target.closest('[data-cart-action]');
    if (!action) return;
    const id = action.dataset.id || action.closest('[data-id]')?.dataset.id;
    const items = readCart();
    const item = items.find((entry) => String(entry.id) === String(id));
    if (!item) return;
    if (action.dataset.cartAction === 'remove') {
      items.splice(items.indexOf(item), 1);
    } else {
      item.quantity = Math.max(1, Number(item.quantity) + (action.dataset.cartAction === 'increase' ? 1 : -1));
    }
    localStorage.setItem('nova_cart', JSON.stringify(items));
    render();
  });

  render();
})();
