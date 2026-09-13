<?php
require_once __DIR__ . '/config/constants.php';
$pageTitle = 'Checkout — NOVA';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<main class="checkout-page-main">
  <div class="nova-container">
    <div class="product-breadcrumb"><a href="index.php">Home</a><span>/</span><a href="cart.php">Cart</a><span>/</span><span>Checkout</span></div>
    <div class="checkout-heading">
      <p class="product-category-label--detail">NOVA CHECKOUT</p>
      <h1>Complete your order</h1>
      <p>Secure delivery details for your selected pieces.</p>
    </div>
    <section id="checkout-content" class="checkout-grid" aria-live="polite"></section>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="js/main.js" defer></script>
<script src="js/checkout.js" defer></script>
<script src="js/animations.js" defer></script>
</body>
</html>
