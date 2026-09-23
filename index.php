<?php
/**
 * NOVA — index.php
 * Homepage: Hero → Categories → Featured → Promo → New Arrivals → Brand Story → Newsletter
 */
require_once 'config/constants.php';
require_once 'includes/products-data.php';

// ── Helper: star rating HTML ──────────────────────────────
function starRating(float $rating, int $count): string {
  $html = '<div class="product-rating">';
  $html .= '<div class="stars" aria-hidden="true">';
  for ($i = 1; $i <= 5; $i++) {
    $filled = $i <= round($rating);
    $html .= '<svg width="12" height="12" viewBox="0 0 24 24" fill="' . ($filled ? 'currentColor' : 'none') . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
    $html .= '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>';
    $html .= '</svg>';
  }
  $html .= '</div>';
  $html .= '<span class="rating-count" aria-label="' . number_format($rating, 1) . ' out of 5 — ' . $count . ' reviews">(' . $count . ')</span>';
  $html .= '</div>';
  return $html;
}

// ── Helper: product card HTML ─────────────────────────────
function productCard(array $p): string {
  $sym     = NOVA_CURRENCY_SYMBOL;
  $hasDisc = isset($p['original_price']) && $p['original_price'] > $p['price'];
  $disc    = $hasDisc ? round((($p['original_price'] - $p['price']) / $p['original_price']) * 100) : 0;
  $id      = (int)($p['id'] ?? 1);

  $html  = '<article class="product-card" data-product-id="' . $id . '" data-price="' . $p['price'] . '" data-category="' . htmlspecialchars($p['category']) . '" data-rating="' . $p['rating'] . '" aria-label="' . htmlspecialchars($p['name']) . '">';
  $html .= '  <div class="product-image-wrapper">';

  // Badge
  if ($hasDisc) {
    $html .= '    <span class="product-badge product-badge--sale">' . $disc . '% OFF</span>';
  } elseif (!empty($p['badge'])) {
    $html .= '    <span class="product-badge product-badge--new">' . htmlspecialchars($p['badge']) . '</span>';
  }

  // Image
  $html .= '    <img class="product-img product-img-primary" src="' . htmlspecialchars(function_exists('nova_product_image_url') ? nova_product_image_url($p['image'] ?? '') : ($p['image'] ?? '')) . '" alt="' . htmlspecialchars($p['name']) . '" loading="lazy" width="400" height="500">';

  // Wishlist
  $html .= '    <button class="product-wishlist-btn" data-id="' . $id . '" aria-label="Add ' . htmlspecialchars($p['name']) . ' to wishlist">';
  $html .= '      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
  $html .= '    </button>';
  $html .= '  </div>';

  // Info
  $html .= '  <div class="product-info">';
  $html .= '    <span class="product-category-label">' . htmlspecialchars($p['category']) . '</span>';
  $html .= '    <h3 class="product-name"><a href="product.php?id=' . $id . '">' . htmlspecialchars($p['name']) . '</a></h3>';
  $html .= starRating($p['rating'], $p['reviews']);
  $html .= '    <div class="product-price-row">';
  $html .= '      <span class="product-price-current">' . $sym . number_format($p['price']) . '</span>';
  if ($hasDisc) {
    $html .= '      <span class="product-price-original">' . $sym . number_format($p['original_price']) . '</span>';
    $html .= '      <span class="product-discount">' . $disc . '% off</span>';
  }
  $html .= '    </div>';
  $html .= '    <button class="product-add-btn" data-id="' . $id . '" aria-label="Add ' . htmlspecialchars($p['name']) . ' to cart">Add to Cart</button>';
  $html .= '  </div>';
  $html .= '</article>';
  return $html;
}

// ── Dynamic Products Data from Database ────────────────────
$allProducts = getAllProducts();
$featured = getFeaturedProducts(4);
$newArrivals = getNewArrivals(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="NOVA — Premium fashion and lifestyle e-commerce. Discover the latest collections for men, women, shoes, watches, bags and accessories.">
  <meta name="theme-color" content="#0a0a0a">

  <!-- Open Graph -->
  <meta property="og:title"       content="NOVA — Define Your Style">
  <meta property="og:description" content="Premium fashion and lifestyle, designed for the modern world.">
  <meta property="og:type"        content="website">

  <title>NOVA — Define Your Style | Premium Fashion E-Commerce</title>

  <!-- Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://images.unsplash.com">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

  <!-- Tailwind CSS Play CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            nova: { black: '#0a0a0a', white: '#fafafa', accent: '#c8a96e' }
          },
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
        }
      },
      corePlugins: { preflight: false }
    }
  </script>

  <!-- NOVA Stylesheets -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

  <!-- GSAP (before body close) -->
  <!-- Loaded at bottom for performance -->
</head>

<body class="no-js header-transparent">
<script>document.body.classList.remove('no-js');</script>

<?php include 'includes/header.php'; ?>

<main id="main-content">

  <!-- ══════════════════════════════════════════════════════
       SECTION 1 — HERO
  ══════════════════════════════════════════════════════════ -->
  <section class="hero-section" id="hero" aria-label="Hero banner — Define Your Style">

    <!-- Three.js canvas for particle accent -->
    <canvas id="hero-canvas" aria-hidden="true" style="position:absolute;inset:0;width:100%;height:100%;z-index:2;pointer-events:none;"></canvas>

    <!-- Hero Image -->
    <div class="hero-image-container" aria-hidden="true">
      <img
        class="hero-image"
        src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=1920&q=85"
        alt="Fashion editorial — woman in premium clothing"
        width="1920"
        height="1080"
        fetchpriority="high"
      >
      <div class="hero-overlay"></div>
    </div>

    <!-- Hero Content -->
    <div class="nova-container hero-content">
      <div style="max-width: 700px;">
        <div class="hero-label">
          <span class="hero-label-line" aria-hidden="true"></span>
          New Season 2026
        </div>
        <h1 class="hero-heading">DEFINE YOUR STYLE</h1>
        <p class="hero-subtext">Discover the latest fashion, designed for the way you live.</p>
        <div class="hero-cta-group">
          <a href="#featured" class="btn btn-secondary btn-xl magnetic-btn" aria-label="Shop the latest collection">
            <span class="btn-inner">
              <span>SHOP COLLECTION</span>
              <svg class="btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </span>
          </a>
          <a href="#categories" class="btn btn-outline-light" aria-label="Browse categories">
            Browse Categories
          </a>
        </div>
      </div>
    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll-indicator" aria-label="Scroll down">
      <div class="scroll-line" aria-hidden="true"></div>
      <span>SCROLL</span>
    </div>

  </section>
  <!-- /hero -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 2 — TRENDING CATEGORIES
  ══════════════════════════════════════════════════════════ -->
  <section class="section-padding" id="categories" aria-labelledby="categories-heading">
    <div class="nova-container">
      <div class="section-header">
        <div>
          <span class="section-eyebrow">Shop By</span>
          <h2 class="section-heading" id="categories-heading">Trending Categories</h2>
        </div>
        <a href="shop.php" class="view-all-link" aria-label="View all categories">
          View All
          <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>

      <div class="categories-grid">
        <?php
        $categories = [
          ['name' => 'Men',        'image' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=500&q=80', 'alt' => 'Men\'s fashion collection'],
          ['name' => 'Women',      'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=500&q=80', 'alt' => 'Women\'s fashion collection'],
          ['name' => 'Shoes',      'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80', 'alt' => 'Shoes and sneakers collection'],
          ['name' => 'Watches',    'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=500&q=80', 'alt' => 'Luxury watches collection'],
          ['name' => 'Bags',       'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=500&q=80', 'alt' => 'Bags and handbags collection'],
          ['name' => 'Accessories','image' => 'https://images.unsplash.com/photo-1576053139778-7e32f2ae3cda?auto=format&fit=crop&w=500&q=80', 'alt' => 'Fashion accessories collection'],
        ];
        foreach ($categories as $cat): ?>
        <a href="category.php?c=<?= urlencode($cat['name']) ?>" class="category-card" aria-label="Shop <?= htmlspecialchars($cat['name']) ?>">
          <div class="category-card-image">
            <img
              class="category-card-img"
              src="<?= $cat['image'] ?>"
              alt="<?= htmlspecialchars($cat['alt']) ?>"
              loading="lazy"
              width="300"
              height="400"
            >
            <div class="category-card-overlay" aria-hidden="true"></div>
          </div>
          <div class="category-card-info">
            <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
            <span class="category-arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <!-- /categories -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 3 — FEATURED PRODUCTS
  ══════════════════════════════════════════════════════════ -->
  <section class="section-padding" id="featured" style="background: var(--nova-gray-50);" aria-labelledby="featured-heading">
    <div class="nova-container">
      <div class="section-header">
        <div>
          <span class="section-eyebrow">Handpicked For You</span>
          <h2 class="section-heading" id="featured-heading">Featured Products</h2>
        </div>
        <a href="shop.php" class="view-all-link" aria-label="View all featured products">
          View All
          <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>

      <div class="products-grid">
        <?php foreach ($featured as $product): ?>
          <?= productCard($product) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <!-- /featured -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 4 — PROMOTIONAL BANNER
  ══════════════════════════════════════════════════════════ -->
  <section class="promo-section section-padding-sm" aria-label="New season promotional banner">
    <div class="promo-image-container" aria-hidden="true">
      <img
        class="promo-image"
        src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1920&q=80"
        alt="New season fashion editorial"
        loading="lazy"
        width="1920"
        height="800"
      >
      <div class="promo-overlay" aria-hidden="true"></div>
    </div>

    <div class="nova-container" style="position:relative; z-index:2; width:100%;">
      <div class="promo-content">
        <div class="promo-eyebrow">
          <span style="width:24px; height:1px; background:var(--nova-accent); display:inline-block;" aria-hidden="true"></span>
          Limited Time
        </div>
        <h2 class="promo-heading">New Season.<br>New Energy.</h2>
        <p class="promo-text">Explore our latest collection — fresh cuts, refined silhouettes, and everyday luxury.</p>
        <a href="#new-arrivals" class="btn btn-secondary btn-lg magnetic-btn" aria-label="Explore the new collection">
          <span class="btn-inner">
            <span>EXPLORE NOW</span>
            <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>
        </a>
      </div>
    </div>
  </section>
  <!-- /promo -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 5 — NEW ARRIVALS
  ══════════════════════════════════════════════════════════ -->
  <section class="section-padding" id="new-arrivals" aria-labelledby="arrivals-heading">
    <div class="nova-container">
      <div class="section-header">
        <div>
          <span class="section-eyebrow">Just Dropped</span>
          <h2 class="section-heading" id="arrivals-heading">New Arrivals</h2>
        </div>
        <a href="#" class="view-all-link" aria-label="View all new arrivals">
          View All
          <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>

      <div class="products-grid">
        <?php foreach ($newArrivals as $product): ?>
          <?= productCard($product) ?>
        <?php endforeach; ?>
      </div>

      <!-- Center CTA -->
      <div style="text-align:center; margin-top: 3rem;">
        <a href="#" class="btn btn-outline btn-lg" aria-label="View all new arrival products">
          VIEW ALL NEW ARRIVALS
        </a>
      </div>
    </div>
  </section>
  <!-- /new-arrivals -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 6 — BRAND STORY
  ══════════════════════════════════════════════════════════ -->
  <section class="brand-story-section section-padding" id="about" aria-labelledby="story-heading">
    <div class="nova-container">
      <div class="brand-story-grid">

        <!-- Image -->
        <div class="brand-story-image-wrapper">
          <img
            class="brand-story-img"
            src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=900&q=80"
            alt="NOVA fashion store — curated premium clothing"
            loading="lazy"
            width="900"
            height="1125"
          >
          <!-- Stat accent -->
          <div class="brand-story-accent" aria-label="10 years of premium fashion">
            <div class="brand-story-stat-number">10+</div>
            <div class="brand-story-stat-label">Years of Premium Fashion</div>
          </div>
        </div>

        <!-- Content -->
        <div class="brand-story-content">
          <span class="section-eyebrow">Our Story</span>
          <h2 class="brand-story-heading" id="story-heading">
            Fashion Built for<br>
            <em>Real Life</em>
          </h2>
          <p class="brand-story-text">
            NOVA was born from a simple belief — that great design and everyday wearability are not opposites. We combine contemporary aesthetics with lasting quality, creating fashion that moves with you through every moment.
          </p>
          <p class="brand-story-text">
            Each piece in our collection is thoughtfully crafted — from fabric sourcing to final cut — to give you the confidence of looking exceptional, effortlessly.
          </p>

          <!-- Pillars -->
          <div class="brand-story-pillars">
            <div class="pillar-item">
              <span class="pillar-number">01</span>
              <span class="pillar-label">Premium Quality</span>
            </div>
            <div class="pillar-item">
              <span class="pillar-number">02</span>
              <span class="pillar-label">Modern Design</span>
            </div>
            <div class="pillar-item">
              <span class="pillar-number">03</span>
              <span class="pillar-label">Sustainable</span>
            </div>
          </div>

          <div style="margin-top: 1rem;">
            <a href="#" class="btn btn-primary btn-lg magnetic-btn" aria-label="Discover the NOVA story">
              <span class="btn-inner">
                <span>DISCOVER NOVA</span>
                <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!-- /brand-story -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 7 — USP / TRUST BAR
  ══════════════════════════════════════════════════════════ -->
  <section aria-label="Why choose NOVA" style="background: var(--nova-black); padding: 3.5rem 0;">
    <div class="nova-container">
      <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; text-align:center;">
        <?php
        $usps = [
          ['icon' => '<path d="M5 12h14M12 5l7 7-7 7"/>', 'title' => 'Free Shipping', 'sub' => 'On orders above ₹999'],
          ['icon' => '<polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>', 'title' => 'Easy Returns', 'sub' => '30-day return policy'],
          ['icon' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>', 'title' => 'Secure Payments', 'sub' => 'SSL encrypted checkout'],
          ['icon' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.15 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.06 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 17z"/>', 'title' => '24/7 Support', 'sub' => 'Always here for you'],
        ];
        foreach ($usps as $usp): ?>
        <div style="display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
          <div style="width:48px; height:48px; border-radius:12px; background:rgba(255,255,255,0.07); display:flex; align-items:center; justify-content:center; color:var(--nova-accent);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <?= $usp['icon'] ?>
            </svg>
          </div>
          <div style="font-size:0.875rem; font-weight:700; color:var(--nova-white); letter-spacing:0.01em;"><?= $usp['title'] ?></div>
          <div style="font-size:0.75rem; color:rgba(255,255,255,0.45); letter-spacing:0.02em;"><?= $usp['sub'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <!-- /usp -->


  <!-- ══════════════════════════════════════════════════════
       SECTION 8 — NEWSLETTER
  ══════════════════════════════════════════════════════════ -->
  <section class="newsletter-section section-padding" id="newsletter" aria-labelledby="newsletter-heading">
    <div class="nova-container">
      <div class="newsletter-inner">

        <!-- Left: Text -->
        <div>
          <span class="section-eyebrow" style="color:rgba(255,255,255,0.45);">Stay Connected</span>
          <h2 class="newsletter-heading" id="newsletter-heading">Stay in<br>the Loop</h2>
          <p class="newsletter-text">Get updates on new collections, exclusive offers, and early access to NOVA releases.</p>
        </div>

        <!-- Right: Form -->
        <div>
          <form class="newsletter-form" id="newsletter-form" novalidate aria-label="Newsletter subscription form">
            <div class="newsletter-input-row">
              <label for="newsletter-email" class="visually-hidden">Email address</label>
              <input
                type="email"
                id="newsletter-email"
                class="newsletter-input"
                placeholder="Enter your email address"
                autocomplete="email"
                required
                aria-required="true"
                aria-describedby="newsletter-hint"
              >
              <button type="submit" class="btn btn-secondary newsletter-btn" aria-label="Subscribe to NOVA newsletter">
                Subscribe
              </button>
            </div>
            <div class="newsletter-msg" id="newsletter-feedback" role="status" aria-live="polite"></div>
            <p class="newsletter-disclaimer" id="newsletter-hint">
              By subscribing you agree to our Privacy Policy. Unsubscribe anytime.
            </p>
          </form>

          <!-- Trust signals -->
          <div style="display:flex; gap:1.5rem; margin-top:1.5rem; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span style="font-size:0.75rem; color:rgba(255,255,255,0.4);">No spam, ever</span>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span style="font-size:0.75rem; color:rgba(255,255,255,0.4);">Exclusive member discounts</span>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span style="font-size:0.75rem; color:rgba(255,255,255,0.4);">Early access to new drops</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!-- /newsletter -->

</main>

<?php include 'includes/footer.php'; ?>

<!-- ══════════════════════════════════════════════════════
     SCRIPTS — loaded at bottom for performance
══════════════════════════════════════════════════════════ -->

<!-- Three.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>

<!-- GSAP (deferred; same version as all other pages) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>

<script>
  window.NOVA_PRODUCTS_DATA = <?= json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<!-- NOVA Scripts -->
<script src="js/main.js"        defer></script>
<script src="js/animations.js"  defer></script>
<script src="js/interactions.js"defer></script>

<!-- Responsive USP grid fix -->
<style>
  @media (max-width: 768px) {
    .usp-grid-inner {
      grid-template-columns: repeat(2, 1fr) !important;
    }
  }
  @media (max-width: 425px) {
    .usp-grid-inner {
      grid-template-columns: repeat(2, 1fr) !important;
    }
  }
  /* USP bar responsive via inline grid */
  section[aria-label="Why choose NOVA"] .nova-container > div {
    grid-template-columns: repeat(4, 1fr);
  }
  @media (max-width: 768px) {
    section[aria-label="Why choose NOVA"] .nova-container > div {
      grid-template-columns: repeat(2, 1fr);
      gap: 2.5rem 1.5rem;
    }
  }
  @media (max-width: 425px) {
    section[aria-label="Why choose NOVA"] .nova-container > div {
      grid-template-columns: repeat(2, 1fr);
      gap: 2rem 1rem;
    }
  }
  /* Newsletter input submit btn alignment on mobile */
  @media (max-width: 640px) {
    .newsletter-input-row {
      flex-direction: column;
    }
    .newsletter-btn {
      width: 100%;
    }
  }
</style>

</body>
</html>
