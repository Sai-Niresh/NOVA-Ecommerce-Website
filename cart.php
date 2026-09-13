<?php
require_once __DIR__ . '/config/constants.php';
$pageTitle = 'Your Cart — NOVA';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="Review your selected NOVA products before checkout.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<main class="cart-page-main">
  <div class="nova-container">
    <div class="product-breadcrumb"><a href="index.php">Home</a><span>/</span><span>Cart</span></div>
    <div class="cart-page-heading">
      <p class="product-category-label--detail">NOVA SHOPPING BAG</p>
      <h1>Your Cart</h1>
      <p>Review your selected pieces before checkout.</p>
    </div>
    <section id="cart-page-content" class="cart-page-content" aria-live="polite"></section>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="js/main.js" defer></script>
<script src="js/cart.js" defer></script>
<script src="js/animations.js" defer></script>
</body>
</html>
