<?php
require_once __DIR__ . '/config/constants.php';
$pageTitle = 'My Orders — NOVA';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<main class="orders-page-main"><div class="nova-container"><div class="checkout-heading"><p class="product-category-label--detail">NOVA ACCOUNT</p><h1>My Orders</h1><p>Track your NOVA purchases and delivery progress.</p></div><section id="orders-content" class="orders-content" aria-live="polite"></section></div></main>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="js/main.js" defer></script><script src="js/orders.js" defer></script>
</body>
</html>
