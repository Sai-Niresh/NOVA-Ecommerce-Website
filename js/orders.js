(() => {
  const content = document.getElementById('orders-content');
  if (!content) return;

  fetch('orders.php').then((response) => response.json()).then((result) => {
    if (!result.success) throw new Error(result.message);
    if (!result.orders.length) {
      content.innerHTML = '<div class="cart-empty-state"><h2>No orders yet</h2><p>Your completed purchases will appear here.</p><a class="btn btn-primary" href="shop.php">Explore the shop</a></div>';
      return;
    }
    content.innerHTML = `<div class="orders-list">${result.orders.map((order) => `<article class="order-card"><div><p class="product-category-label--detail">ORDER #${order.id}</p><h2>${order.city}</h2><p>${new Date(order.created_at).toLocaleDateString()} · ${order.payment_method.toUpperCase()}</p></div><div class="order-card-meta"><strong>₹${Number(order.total_amount).toLocaleString('en-IN')}</strong><span class="order-status">${order.status}</span></div></article>`).join('')}</div>`;
  }).catch((error) => {
    content.innerHTML = `<div class="cart-empty-state"><h2>${error.message}</h2><p>Sign in to view your saved orders.</p></div>`;
  });
})();
