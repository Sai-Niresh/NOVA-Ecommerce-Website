<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/products-data.php';

session_start();

if (isset($_GET['logout'])) {
  unset($_SESSION['nova_admin']);
  header('Location: admin.php');
  exit;
}

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
  try {
    $db = novaDb();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $query = $db->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = ? LIMIT 1');
    $query->execute([$email]);
    $admin = $query->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['nova_admin'] = ['id' => (int) $admin['id'], 'email' => $admin['email']];
      header('Location: admin.php');
      exit;
    }
    $loginError = 'Incorrect admin email or password.';
  } catch (Throwable $error) {
    $loginError = 'Database connection failed. Start MySQL in XAMPP.';
  }
}

$isAdmin = !empty($_SESSION['nova_admin']);
$metrics = ['revenue' => 0, 'orders' => 0, 'customers' => 0, 'products' => count(getAllProducts())];
$recentOrders = [];

if ($isAdmin) {
  try {
    $db = novaDb();
    $metrics['revenue'] = (float) $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $metrics['orders'] = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $metrics['customers'] = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $recentOrders = $db->query('SELECT id, customer_name, city, total_amount, payment_method, status, created_at FROM orders ORDER BY created_at DESC LIMIT 8')->fetchAll();
  } catch (Throwable $error) {
    $loginError = 'Unable to load dashboard data.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isAdmin ? 'Admin Dashboard — NOVA' : 'Admin Login — NOVA' ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="admin-body">
<?php if (!$isAdmin): ?>
  <main class="admin-login-page"><section class="admin-login-card"><p class="product-category-label--detail">NOVA CONTROL CENTER</p><h1>Admin sign in</h1><p>Manage orders, customers, and catalog performance.</p><?php if ($loginError): ?><div class="admin-alert"><?= htmlspecialchars($loginError) ?></div><?php endif; ?><form method="post" class="admin-login-form"><input type="hidden" name="action" value="login"><label>Email address<input name="email" type="email" value="admin@nova.local" required></label><label>Password<input name="password" type="password" required></label><button class="btn btn-primary btn-large" type="submit">Sign in to dashboard</button></form><p class="admin-demo-note">Local demo account: admin@nova.local / admin123</p><a href="index.php">Back to storefront</a></section></main>
<?php else: ?>
  <main class="admin-page"><div class="admin-container"><header class="admin-topbar"><div><p class="product-category-label--detail">NOVA CONTROL CENTER</p><h1>Dashboard</h1></div><div><span><?= htmlspecialchars($_SESSION['nova_admin']['email']) ?></span><a class="admin-logout" href="admin.php?logout=1">Log out</a></div></header><section class="admin-metrics"><article><span>Total revenue</span><strong>₹<?= number_format($metrics['revenue']) ?></strong></article><article><span>Total orders</span><strong><?= number_format($metrics['orders']) ?></strong></article><article><span>Customers</span><strong><?= number_format($metrics['customers']) ?></strong></article><article><span>Products</span><strong><?= number_format($metrics['products']) ?></strong></article></section><section class="admin-section"><div class="admin-section-heading"><div><p class="product-category-label--detail">OPERATIONS</p><h2>Recent orders</h2></div><a href="orders-page.php">Customer order view</a></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>City</th><th>Payment</th><th>Total</th><th>Status</th></tr></thead><tbody><?php foreach ($recentOrders as $order): ?><tr><td>#<?= (int) $order['id'] ?></td><td><?= htmlspecialchars($order['customer_name']) ?></td><td><?= htmlspecialchars($order['city']) ?></td><td><?= htmlspecialchars(strtoupper($order['payment_method'])) ?></td><td>₹<?= number_format((float) $order['total_amount']) ?></td><td><span class="admin-status"><?= htmlspecialchars($order['status']) ?></span></td></tr><?php endforeach; ?><?php if (!$recentOrders): ?><tr><td colspan="6">No orders have been placed yet.</td></tr><?php endif; ?></tbody></table></div></section></div></main>
<?php endif; ?>
</body>
</html>