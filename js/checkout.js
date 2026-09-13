(() => {
  const content = document.getElementById('checkout-content');
  if (!content) return;

  const getItems = () => {
    try {
      const items = JSON.parse(localStorage.getItem('nova_cart') || '[]');
      return Array.isArray(items) ? items : [];
    } catch (error) {
      return [];
    }
  };

  const getValidItems = () => getItems().filter((item) => (
    Number(item.id) > 0 &&
    String(item.name || '').trim() !== '' &&
    Number(item.price) >= 0 &&
    Number(item.quantity) > 0
  ));

  const items = getValidItems();
  const total = items.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 0), 0);

  if (!items.length) {
    content.innerHTML = '<div class="cart-empty-state"><div class="panel-state-icon">▢</div><h2>Your cart is empty</h2><p>Add a product before starting checkout.</p><a class="btn btn-primary" href="shop.php">Explore the shop</a></div>';
    return;
  }

  content.innerHTML = `<form class="checkout-form" id="checkout-form"><div class="checkout-form-panel"><p class="product-category-label--detail">DELIVERY DETAILS</p><h2>Where should we send it?</h2><div class="checkout-form-grid"><label>Full name<input name="name" type="text" autocomplete="name" required></label><label>Phone number<input name="phone" type="tel" autocomplete="tel" required></label><label class="checkout-full">Address<input name="address" type="text" autocomplete="street-address" required></label><label>City<input name="city" type="text" autocomplete="address-level2" required></label><label>Postal code<input name="postal" type="text" autocomplete="postal-code" required></label></div><p class="product-category-label--detail checkout-payment-label">PAYMENT METHOD</p><div class="payment-options"><label class="payment-option"><input type="radio" name="payment_method" value="upi" required><span><strong>UPI</strong><small>Fast digital payment</small></span></label><label class="payment-option"><input type="radio" name="payment_method" value="card"><span><strong>Card</strong><small>Credit or debit card</small></span></label><label class="payment-option"><input type="radio" name="payment_method" value="cod"><span><strong>Cash on delivery</strong><small>Pay when it arrives</small></span></label></div><button class="btn btn-primary btn-large" type="submit">Place order</button><p class="checkout-message" id="checkout-message" role="status"></p></div><aside class="checkout-summary cart-summary"><p class="product-category-label--detail">ORDER SUMMARY</p><h2>Your NOVA edit</h2>${items.map((item) => `<div class="checkout-item"><span>${item.name} × ${item.quantity}</span><strong>₹${(Number(item.price) * Number(item.quantity)).toLocaleString('en-IN')}</strong></div>`).join('')}<div class="summary-total"><span>Total</span><strong>₹${total.toLocaleString('en-IN')}</strong></div><p class="summary-note">Payment methods are presented for demonstration. No live charge is processed.</p></aside></form>`;

  document.getElementById('checkout-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = document.getElementById('checkout-message');
    const form = event.currentTarget;
    const submit = form.querySelector('button[type="submit"]');
    const data = new FormData(form);
    const latestItems = getValidItems();
    if (!latestItems.length) {
      message.textContent = 'Your cart is empty. Add a product before placing the order.';
      message.className = 'checkout-message is-error';
      return;
    }
    submit.disabled = true;
    submit.textContent = 'Placing order...';
    try {
      const response = await fetch('orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          customer: Object.fromEntries(data.entries()),
          items: latestItems,
          payment_method: data.get('payment_method'),
        }),
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Order could not be placed.');
      localStorage.removeItem('nova_cart');
      message.innerHTML = `${result.message} <strong>Order #${result.order_id}</strong><br><a href="index.php">Continue shopping</a>`;
      message.className = 'checkout-message is-success';
      form.reset();
    } catch (error) {
      message.textContent = error.message;
      message.className = 'checkout-message is-error';
    } finally {
      submit.disabled = false;
      submit.textContent = 'Place order';
    }
  });
})();
