<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/products-data.php';
require_once __DIR__ . '/includes/product-card.php';

function buildProductGallery(array $product): array {
  $gallery = [];
  $seen = [];

  $addImage = function (string $src) use (&$gallery, &$seen): void {
    $src = trim($src);
    if ($src === '' || in_array($src, $seen, true)) {
      return;
    }
    $seen[] = $src;
    $gallery[] = $src;
  };

  if (!empty($product['gallery']) && is_array($product['gallery'])) {
    foreach ($product['gallery'] as $img) {
      $addImage((string) $img);
    }
  }

  if (!empty($product['image'])) {
    $addImage((string) $product['image']);
  }
  if (!empty($product['secondary_image'])) {
    $addImage((string) $product['secondary_image']);
  }

  $fallbacks = [
    'men' => [
      'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80'
    ],
    'women' => [
      'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80'
    ],
    'shoes' => [
      'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1543508282-6319a3e2621f?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=900&q=80'
    ],
    'watches' => [
      'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1547996160-81dfa63595aa?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1434056886845-dac89ffe9b56?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1508057198894-247b23fe5ade?auto=format&fit=crop&w=900&q=80'
    ],
    'bags' => [
      'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80'
    ],
    'accessories' => [
      'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?auto=format&fit=crop&w=900&q=80',
      'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80'
    ]
  ];

  $categoryKey = strtolower((string) $product['category']);
  if (isset($fallbacks[$categoryKey])) {
    foreach ($fallbacks[$categoryKey] as $img) {
      $addImage($img);
    }
  }

  if (count($gallery) < 4) {
    $addImage('https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80');
    $addImage('https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80');
    $addImage('https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80');
    $addImage('https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80');
  }

  return array_slice($gallery, 0, 5);
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = $id ? getProductById((int) $id) : null;
if (!$product) {
  $title = 'Product Not Found';
  $titleLabel = 'Product Not Found';
  $product = null;
} else {
  $title = htmlspecialchars($product['name']) . ' — ' . NOVA_BRAND_NAME;
  $titleLabel = htmlspecialchars($product['category']);
}

$gallery = $product ? buildProductGallery($product) : [];
$category = $product ? $product['category'] : 'Shop';
$relatedProducts = $product ? array_values(array_filter(getAllProducts(), function ($p) use ($product) {
  return $p['id'] !== $product['id'] && strtolower($p['category']) === strtolower($product['category']);
})) : [];
$relatedProducts = array_slice($relatedProducts, 0, 4);
$recentlyViewed = [];
if (!empty($_COOKIE['nova_recently_viewed'])) {
  $decoded = json_decode(stripslashes($_COOKIE['nova_recently_viewed']), true);
  if (is_array($decoded)) {
    $recentlyViewed = array_values(array_filter(array_map(function ($item) {
      if (!is_numeric($item)) {
        return null;
      }
      return getProductById((int) $item);
    }, $decoded), fn($product) => $product !== null));
  }
}

if (empty($recentlyViewed) && $product) {
  $recentlyViewed = array_values(array_filter(getAllProducts(), function ($p) use ($product) {
    return (int) $p['id'] !== (int) $product['id'];
  }));
  $recentlyViewed = array_slice($recentlyViewed, 0, 4);
}

$reviewCount = (int) ($product['reviews'] ?? 128);
$stockStatus = ($product['stock'] ?? 0) <= 0 ? 'Out of Stock' : (($product['stock'] ?? 0) <= 5 ? 'Only ' . (int) $product['stock'] . ' left' : 'In Stock');
$currency = NOVA_CURRENCY_SYMBOL;
$hasDiscount = !empty($product['original_price']) && (int) $product['original_price'] > (int) $product['price'];
$discountValue = $hasDiscount ? (int) ($product['discount'] ?? round((($product['original_price'] - $product['price']) / $product['original_price']) * 100)) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>
  <meta name="description" content="Premium product details for <?= htmlspecialchars($product['name'] ?? 'NOVA product') ?>.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
<link rel="preconnect" href="https://images.unsplash.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="product-page-main">
  <?php if (!$product): ?>
    <section class="product-empty-state">
      <div class="nova-container">
        <div class="product-empty-card">
          <p class="section-eyebrow">Product Not Found</p>
          <h1 class="product-empty-title">The product you're looking for may have been removed or is no longer available.</h1>
          <a href="shop.php" class="btn btn-primary">Back to Shop</a>
        </div>
      </div>
    </section>
  <?php else: ?>
    <section class="product-page-header">
      <div class="nova-container">
        <nav class="product-breadcrumb" aria-label="Breadcrumb">
          <a href="index.php">Home</a>
          <span>/</span>
          <a href="shop.php">Shop</a>
          <span>/</span>
          <a href="category.php?c=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a>
          <span>/</span>
          <span><?= htmlspecialchars($product['name']) ?></span>
        </nav>
      </div>
    </section>

    <section class="product-detail-section">
      <div class="nova-container">
        <div class="product-shell">
          <div class="product-gallery" aria-label="Product gallery">
            <div class="thumbnail-column" aria-label="Product thumbnails">
              <?php foreach ($gallery as $index => $image): ?>
                <button type="button" class="thumb-btn <?= $index === 0 ? 'is-active' : '' ?>" data-index="<?= $index ?>" aria-label="View product image <?= $index + 1 ?>">
                  <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($product['name']) ?> thumbnail <?= $index + 1 ?>" loading="lazy">
                </button>
              <?php endforeach; ?>
            </div>

            <div class="gallery-main-wrap" id="gallery-main-wrap" data-current-index="0">
              <img id="main-product-image" class="gallery-main-image" src="<?= htmlspecialchars($gallery[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?> main view" loading="eager">
              <button type="button" class="gallery-zoom-btn" aria-label="Open fullscreen gallery">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="7"></circle>
                  <line x1="16.65" y1="16.65" x2="21" y2="21"></line>
                </svg>
              </button>
            </div>
          </div>

          <div class="product-info-column">
            <p class="product-category-label product-category-label--detail"><?= htmlspecialchars($product['category']) ?></p>
            <h1 class="product-detail-title"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="product-summary-row">
              <div class="product-stars" aria-label="Rated <?= number_format($product['rating'], 1) ?> out of 5">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <svg viewBox="0 0 24 24" fill="<?= $i <= round((float) $product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <?php endfor; ?>
              </div>
              <span class="product-review-value"><?= number_format((float) $product['rating'], 1) ?></span>
              <a href="#reviews" class="product-review-link">(<?= (int) $product['reviews'] ?> reviews)</a>
            </div>

            <div class="product-price-block">
              <div class="product-price-row-detail">
                <span class="detail-current-price"><?= $currency ?><?= number_format((int) $product['price']) ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="detail-original-price"><?= $currency ?><?= number_format((int) $product['original_price']) ?></span>
                  <span class="detail-discount-badge"><?= $discountValue ?>% OFF</span>
                <?php endif; ?>
              </div>
            </div>

            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
            <button type="button" class="text-link btn-read-more" aria-expanded="false">Read More</button>

            <?php
              // Normalize colors so a stored plain-string list (e.g. ["black"])
              // can never break the swatch renderer with a fatal error.
              $productColors = [];
              foreach (($product['colors'] ?? []) as $cItem) {
                if (is_array($cItem)) {
                  $cName = trim((string) ($cItem['name'] ?? ($cItem['hex'] ?? '')));
                  $cHex  = trim((string) ($cItem['hex'] ?? ''));
                } else {
                  $cName = trim((string) $cItem);
                  $cHex  = '';
                }
                if ($cName === '' && $cHex === '') continue;
                if ($cHex === '' && preg_match('/^#?[0-9a-fA-F]{3}$|^#?[0-9a-fA-F]{6}$/', $cName)) {
                  $cHex = strpos($cName, '#') === 0 ? $cName : '#' . $cName;
                }
                if ($cHex === '') $cHex = function_exists('nova_color_name_to_hex') ? nova_color_name_to_hex($cName) : '#cccccc';
                if ($cName === '') $cName = $cHex;
                $productColors[] = ['name' => $cName, 'hex' => $cHex];
              }
            ?>
            <div class="product-option-group">
              <div class="option-header-row">
                <span class="option-heading">Color</span>
                <span class="option-selected-value" id="selected-color-name"><?= htmlspecialchars($productColors[0]['name'] ?? 'Black') ?></span>
              </div>
              <div class="color-options" aria-label="Choose a color">
                <?php foreach ($productColors as $color): ?>
                  <button type="button" class="color-option <?= (!isset($firstColor) ? 'is-selected' : '') ?>" data-color-name="<?= htmlspecialchars($color['name']) ?>" style="background: <?= htmlspecialchars($color['hex']) ?>" aria-label="Select <?= htmlspecialchars($color['name']) ?> color" title="<?= htmlspecialchars($color['name']) ?>">
                    <span class="sr-only"><?= htmlspecialchars($color['name']) ?></span>
                  </button>
                  <?php $firstColor = true; ?>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="product-option-group">
              <div class="option-header-row">
                <span class="option-heading">Size</span>
                <button type="button" class="size-guide-trigger" data-modal="size-guide-modal">Size Guide</button>
              </div>
              <div class="size-options" aria-label="Choose a size">
                <?php foreach ($product['sizes'] ?? [] as $size): ?>
                  <?php $isDisabled = !in_array($size, $product['sizes'], true) || false; ?>
                  <button type="button" class="size-option <?= $size === ($product['sizes'][0] ?? '') ? 'is-selected' : '' ?>" data-size="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="purchase-row">
              <div class="qty-selector" aria-label="Quantity selector">
                <button type="button" class="qty-btn" data-action="decrease" aria-label="Decrease quantity">−</button>
                <span class="qty-value" id="qty-value">1</span>
                <button type="button" class="qty-btn" data-action="increase" aria-label="Increase quantity">+</button>
              </div>

              <div class="stock-status <?= ($product['stock'] ?? 0) <= 0 ? 'is-out' : '' ?>">
                <?php if (($product['stock'] ?? 0) <= 0): ?>
                  <span class="stock-dot"></span>Out of Stock
                <?php elseif (($product['stock'] ?? 0) <= 5): ?>
                  <span class="stock-dot"></span>Only <?= (int) $product['stock'] ?> left
                <?php else: ?>
                  <span class="stock-dot is-live"></span>In Stock
                <?php endif; ?>
              </div>
            </div>

            <div class="cta-row">
              <button type="button" class="btn btn-primary btn-large product-add-cart" data-id="<?= (int) $product['id'] ?>">Add to Cart</button>
              <button type="button" class="btn btn-secondary btn-large product-buy-now">Buy Now</button>
            </div>

            <div class="utility-row">
              <button type="button" class="wishlist-btn" id="wishlist-btn" aria-label="Add to wishlist">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <span>Add to Wishlist</span>
              </button>
            </div>

            <div class="delivery-box">
              <div class="delivery-row">
                <span class="delivery-label">Delivery</span>
                <span class="delivery-status">Free delivery on eligible orders</span>
              </div>
              <div class="pin-row">
                <input type="text" class="pin-input" maxlength="6" placeholder="Enter PIN code" aria-label="Enter pin code">
                <button type="button" class="btn btn-outline pin-check-btn">Check</button>
              </div>
              <p class="pin-message" aria-live="polite"></p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="more-info-section">
      <div class="nova-container">
        <div class="info-accordions">
          <div class="accordion-item is-open">
            <button type="button" class="accordion-trigger" aria-expanded="true">
              <span>Shipping</span>
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-content">
              <p>Free standard shipping on qualifying orders. Express shipping options and delivery estimates are visible during checkout.</p>
            </div>
          </div>

          <div class="accordion-item">
            <button type="button" class="accordion-trigger" aria-expanded="false">
              <span>Returns</span>
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-content">
              <p>Easy returns within the applicable return window. Items should be unworn, in original packaging, and accompanied by proof of purchase.</p>
            </div>
          </div>

          <div class="accordion-item">
            <button type="button" class="accordion-trigger" aria-expanded="false">
              <span>Product Details</span>
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-content">
              <ul class="detail-list">
                <li><strong>Material:</strong> <?= htmlspecialchars($product['material'] ?? 'Premium cotton blend') ?></li>
                <li><strong>Fit:</strong> <?= htmlspecialchars($product['fit'] ?? 'Modern relaxed fit') ?></li>
                <li><strong>Care:</strong> <?= htmlspecialchars($product['care'] ?? 'Machine wash cold') ?></li>
                <li><strong>Country of Origin:</strong> <?= htmlspecialchars($product['origin'] ?? 'India') ?></li>
                <li><strong>SKU:</strong> <?= htmlspecialchars($product['sku'] ?? 'NOVA-' . $product['id']) ?></li>
                <li><strong>Product Type:</strong> <?= htmlspecialchars($product['category']) ?></li>
              </ul>
            </div>
          </div>

          <div class="accordion-item">
            <button type="button" class="accordion-trigger" aria-expanded="false">
              <span>Description</span>
              <span class="accordion-icon">+</span>
            </button>
            <div class="accordion-content">
              <p><?= htmlspecialchars($product['description']) ?> Designed to move seamlessly from workdays to evenings with a refined silhouette and elevated everyday comfort.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="reviews-section" id="reviews">
      <div class="nova-container">
        <div class="reviews-summary-row">
          <div class="review-score-block">
            <p class="section-eyebrow">Customer Reviews</p>
            <div class="review-score-line">
              <span class="review-score-number"><?= number_format((float) $product['rating'], 1) ?></span>
              <span class="review-score-outof">/ 5</span>
            </div>
            <div class="product-stars review-stars" aria-label="Average rating <?= number_format((float) $product['rating'], 1) ?> out of 5">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <svg viewBox="0 0 24 24" fill="<?= $i <= round((float) $product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              <?php endfor; ?>
            </div>
            <p class="review-caption">Based on <?= (int) $product['reviews'] ?> reviews</p>
          </div>

          <div class="rating-breakdown" aria-label="Rating distribution">
            <?php foreach ([5, 4, 3, 2, 1] as $star): ?>
              <div class="rating-row">
                <span><?= $star ?> ★</span>
                <div class="rating-bar"><span style="width: <?= $star === 5 ? '82%' : ($star === 4 ? '12%' : ($star === 3 ? '4%' : ($star === 2 ? '2%' : '0%'))) ?>;"></span></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="reviews-list">
          <?php
          $reviews = [
            ['name' => 'Rahul K.', 'date' => '2 days ago', 'text' => 'Premium quality and the fit is excellent. The material feels luxe without being overdone.', 'rating' => 5, 'verified' => true],
            ['name' => 'Nisha S.', 'date' => '1 week ago', 'text' => 'Stylish and comfortable. It looks elevated and feels like a premium wardrobe piece.', 'rating' => 5, 'verified' => true],
            ['name' => 'Aarav M.', 'date' => '3 weeks ago', 'text' => 'The craftsmanship is great and the color looks even better in person. Well worth it.', 'rating' => 4, 'verified' => false],
          ];
          foreach ($reviews as $review):
          ?>
            <article class="review-card">
              <div class="review-top-row">
                <div class="review-stars" aria-label="Rated <?= $review['rating'] ?> out of 5">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg viewBox="0 0 24 24" fill="<?= $i <= $review['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <?php endfor; ?>
                </div>
                <?php if ($review['verified']): ?>
                  <span class="verified-badge">Verified Purchase</span>
                <?php endif; ?>
              </div>
              <div class="review-meta">
                <strong><?= htmlspecialchars($review['name']) ?></strong>
                <span><?= htmlspecialchars($review['date']) ?></span>
              </div>
              <p class="review-content">“<?= htmlspecialchars($review['text']) ?>”</p>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="review-form-block">
          <h3>Write a Review</h3>
          <form class="review-form" id="review-form">
            <div class="review-form-grid">
              <label>
                <span>Name</span>
                <input type="text" name="name" placeholder="Your name" required>
              </label>
              <label>
                <span>Rating</span>
                <select name="rating" aria-label="Select your rating">
                  <option value="5">5 ★</option>
                  <option value="4">4 ★</option>
                  <option value="3">3 ★</option>
                  <option value="2">2 ★</option>
                  <option value="1">1 ★</option>
                </select>
              </label>
            </div>
            <label>
              <span>Review</span>
              <textarea name="review" rows="5" placeholder="Tell us about your experience" required></textarea>
            </label>
            <button type="submit" class="btn btn-primary">Submit Review</button>
          </form>
        </div>
      </div>
    </section>

    <section class="related-products-section">
      <div class="nova-container">
        <div class="section-heading-row">
          <h2>You May Also Like</h2>
        </div>
        <div class="products-grid related-products-grid">
          <?php foreach ($relatedProducts as $related): ?>
            <?= renderProductCard($related) ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <?php if (!empty($recentlyViewed)): ?>
      <section class="recently-viewed-section">
        <div class="nova-container">
          <div class="section-heading-row">
            <h2>Recently Viewed</h2>
          </div>
          <div class="products-grid recently-viewed-grid">
            <?php foreach (array_slice($recentlyViewed, 0, 4) as $recent): ?>
              <?= renderProductCard($recent) ?>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<div class="product-modal-overlay" id="gallery-modal" aria-hidden="true">
  <div class="product-modal" role="dialog" aria-modal="true" aria-label="Expanded product gallery">
    <button type="button" class="modal-close" aria-label="Close gallery">×</button>
    <button type="button" class="modal-nav modal-prev" aria-label="Previous image">‹</button>
    <div class="modal-image-wrap">
      <img id="modal-image" src="" alt="Expanded product view">
    </div>
    <button type="button" class="modal-nav modal-next" aria-label="Next image">›</button>
    <div class="modal-thumbs" id="modal-thumbs"></div>
  </div>
</div>

<div class="size-guide-modal-overlay" id="size-guide-modal" aria-hidden="true">
  <div class="size-guide-modal" role="dialog" aria-modal="true" aria-label="Size guide">
    <button type="button" class="modal-close size-guide-close" aria-label="Close size guide">×</button>
    <h3>Size Guide</h3>
    <div class="size-guide-table-wrap">
      <table>
        <thead>
          <tr><th>Size</th><th>Chest</th><th>Length</th></tr>
        </thead>
        <tbody>
          <tr><td>XS</td><td>36 in</td><td>26 in</td></tr>
          <tr><td>S</td><td>38 in</td><td>27 in</td></tr>
          <tr><td>M</td><td>40 in</td><td>28 in</td></tr>
          <tr><td>L</td><td>42 in</td><td>29 in</td></tr>
          <tr><td>XL</td><td>44 in</td><td>30 in</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  window.NOVA_PRODUCT = <?= json_encode($product ?: []) ?>;
  window.NOVA_CURRENCY_SYMBOL = "<?= NOVA_CURRENCY_SYMBOL ?>";
  window.NOVA_GALLERY = <?= json_encode($gallery) ?>;
</script>
<script src="js/main.js" defer></script>
<script src="js/product.js" defer></script>
<script src="js/interactions.js" defer></script>
</body>
</html>
