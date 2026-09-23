<?php
/**
 * NOVA — shop.php
 * Main Shop & Catalog Page
 */
require_once 'config/constants.php';
require_once 'includes/products-data.php';
require_once 'includes/product-card.php';

$allProducts = getAllProducts();
$totalCount  = count($allProducts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop All Collections — <?= NOVA_BRAND_NAME ?></title>
  <meta name="description" content="Explore NOVA's complete collection of luxury men's & women's apparel, footwear, watches, leather bags, and artisanal accessories.">
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/animations.css">
<link rel="preconnect" href="https://images.unsplash.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  
  <!-- GSAP & ScrollTrigger -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
</head>
<body>

<?php include_once 'includes/header.php'; ?>

<main id="main-content">

  <!-- ─── SHOP HERO BANNER ─── -->
  <section class="shop-hero">
    <div class="nova-container">
      <div class="shop-hero-content">
        <span class="shop-tagline">The NOVA Catalog</span>
        <h1 class="shop-hero-title">Shop All Collections</h1>
        <p class="shop-hero-desc">
          Discover modern essential wardrobe pieces, precision horology, handcrafted footwear, and leather goods designed for intentional living.
        </p>
        
        <!-- Breadcrumb -->
        <nav class="shop-breadcrumb" aria-label="Breadcrumb navigation">
          <a href="index.php" class="breadcrumb-link">Home</a>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">Shop</span>
        </nav>
      </div>
    </div>
  </section>

  <!-- ─── MAIN SHOP SECTION ─── -->
  <section class="nova-section" style="padding-top: var(--space-8);">
    <div class="nova-container">

      <!-- Active Filters Indicator Bar -->
      <div class="active-filters-bar" id="active-filters-bar" style="display: none;">
        <span class="active-filters-title">Active Filters:</span>
        <div id="active-chips-container" style="display: flex; gap: 0.4rem; flex-wrap: wrap;"></div>
        <button type="button" class="clear-all-btn" id="clear-all-filters-btn">Clear All</button>
      </div>

      <!-- Shop Controls Bar -->
      <div class="shop-controls-bar">
        <div class="shop-count-info" id="shop-count-info">
          Showing <strong><?= min(12, $totalCount) ?></strong> of <strong><?= $totalCount ?></strong> products
        </div>

        <div class="shop-controls-right">
          <!-- Sort Dropdown -->
          <div class="sort-select-wrapper">
            <label for="shop-sort-select" class="sort-label">Sort By:</label>
            <select id="shop-sort-select" class="sort-select" aria-label="Sort products">
              <option value="featured">Featured</option>
              <option value="newest">Newest Arrivals</option>
              <option value="price-asc">Price: Low to High</option>
              <option value="price-desc">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
            </select>
          </div>

          <!-- View Mode Toggle -->
          <div class="grid-view-toggle" aria-label="Grid layout toggle">
            <button type="button" class="view-btn is-active" id="view-btn-3" aria-label="3 column grid view" title="3 Columns">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>
            </button>
            <button type="button" class="view-btn" id="view-btn-4" aria-label="4 column grid view" title="4 Columns">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h4v18H3zM10 3h4v18h-4zM17 3h4v18h-4z"/></svg>
            </button>
          </div>

          <!-- Mobile Filter Drawer Trigger -->
          <button type="button" class="mobile-filter-btn" id="mobile-filter-trigger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            <span>Filters</span>
          </button>
        </div>
      </div>

      <!-- Shop Two-Column Layout -->
      <div class="shop-layout">

        <!-- ─── SIDEBAR FILTERS ─── -->
        <aside class="shop-sidebar" id="shop-sidebar">
          
          <!-- Search Widget -->
          <div class="filter-widget">
            <h3 class="filter-widget-title">Search</h3>
            <div class="sidebar-search-box">
              <input type="text" id="sidebar-search-input" class="sidebar-search-input" placeholder="Search products..." aria-label="Search products">
              <svg class="sidebar-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
          </div>

          <!-- Category Filter Widget -->
          <div class="filter-widget">
            <h3 class="filter-widget-title">Categories</h3>
            <ul class="filter-category-list" role="list">
              <li>
                <button type="button" class="filter-cat-btn is-active" data-category="all">
                  <span>All Collections</span>
                  <span class="filter-cat-count">(<?= $totalCount ?>)</span>
                </button>
              </li>
              <?php
              $categories = ['Men', 'Women', 'Shoes', 'Watches', 'Bags', 'Accessories'];
              foreach ($categories as $cat):
                $catProducts = getProductsByCategory($cat);
                $catCount    = count($catProducts);
              ?>
              <li>
                <button type="button" class="filter-cat-btn" data-category="<?= htmlspecialchars($cat) ?>">
                  <span><?= htmlspecialchars($cat) ?></span>
                  <span class="filter-cat-count">(<?= $catCount ?>)</span>
                </button>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Price Range Widget -->
          <div class="filter-widget">
            <h3 class="filter-widget-title">Price Range</h3>
            <div class="price-inputs">
              <div class="price-field">
                <span class="price-currency"><?= NOVA_CURRENCY_SYMBOL ?></span>
                <input type="number" id="price-min-input" class="price-input" value="0" min="0" max="20000" placeholder="Min">
              </div>
              <div class="price-field">
                <span class="price-currency"><?= NOVA_CURRENCY_SYMBOL ?></span>
                <input type="number" id="price-max-input" class="price-input" value="20000" min="0" max="20000" placeholder="Max">
              </div>
            </div>
            <input type="range" id="price-range-slider" class="price-slider-track" min="0" max="20000" step="500" value="20000" aria-label="Maximum price range slider">
          </div>

          <!-- Size Filter Widget -->
          <div class="filter-widget">
            <h3 class="filter-widget-title">Size</h3>
            <div class="size-chips-grid" id="size-filter-container">
              <button type="button" class="size-chip" data-size="XS">XS</button>
              <button type="button" class="size-chip" data-size="S">S</button>
              <button type="button" class="size-chip" data-size="M">M</button>
              <button type="button" class="size-chip" data-size="L">L</button>
              <button type="button" class="size-chip" data-size="XL">XL</button>
              <button type="button" class="size-chip" data-size="UK 7">UK 7</button>
              <button type="button" class="size-chip" data-size="UK 8">UK 8</button>
              <button type="button" class="size-chip" data-size="UK 9">UK 9</button>
              <button type="button" class="size-chip" data-size="UK 10">UK 10</button>
              <button type="button" class="size-chip" data-size="One Size">One Size</button>
            </div>
          </div>

          <!-- Color Swatches Filter Widget -->
          <div class="filter-widget">
            <h3 class="filter-widget-title">Color</h3>
            <div class="color-swatches-grid" id="color-filter-container">
              <div class="color-swatch-item" style="background-color: #0a0a0a;" data-color-name="Black" title="Black"></div>
              <div class="color-swatch-item" style="background-color: #ffffff;" data-color-name="White" title="White"></div>
              <div class="color-swatch-item" style="background-color: #d4c5b9;" data-color-name="Beige" title="Beige"></div>
              <div class="color-swatch-item" style="background-color: #1b263b;" data-color-name="Navy" title="Navy"></div>
              <div class="color-swatch-item" style="background-color: #556b2f;" data-color-name="Olive" title="Olive"></div>
              <div class="color-swatch-item" style="background-color: #a0522d;" data-color-name="Tan Leather" title="Tan"></div>
              <div class="color-swatch-item" style="background-color: #c8a96e;" data-color-name="Champagne Gold" title="Gold"></div>
              <div class="color-swatch-item" style="background-color: #dc2626;" data-color-name="Red / White" title="Red"></div>
            </div>
          </div>

          <!-- Availability & Sale Checkboxes -->
          <div class="filter-widget" style="border-bottom: none;">
            <h3 class="filter-widget-title">Options</h3>
            <div class="filter-checkbox-list">
              <label class="checkbox-label">
                <input type="checkbox" id="filter-instock">
                <span>In Stock Only</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" id="filter-onsale">
                <span>On Sale</span>
              </label>
            </div>
          </div>

        </aside>

        <!-- ─── MAIN PRODUCT GRID COLUMN ─── -->
        <div class="shop-main">

          <!-- Skeleton Loading Grid (Hidden by default) -->
          <div class="skeleton-grid" id="shop-skeleton-grid" style="display: none;">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="skeleton-card">
              <div class="skeleton-img"></div>
              <div class="skeleton-body">
                <div class="skeleton-line w-40"></div>
                <div class="skeleton-line w-80"></div>
                <div class="skeleton-line w-60"></div>
              </div>
            </div>
            <?php endfor; ?>
          </div>

          <!-- Empty State Container -->
          <div class="shop-empty-state" id="shop-empty-state" style="display: none;">
            <div class="empty-icon-wrap">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <h3 class="empty-title">No products found</h3>
            <p class="empty-desc">We couldn't find any products matching your selected search query or filters. Try adjusting your parameters.</p>
            <button type="button" class="btn btn-primary btn-sm" id="reset-empty-btn">Reset All Filters</button>
          </div>

          <!-- Live Product Grid -->
          <div class="shop-product-grid grid-3-col" id="shop-product-grid">
            <?php
            // Initial SSR Render for first 12 products
            $initialProducts = array_slice($allProducts, 0, 12);
            foreach ($initialProducts as $product) {
              echo renderProductCard($product);
            }
            ?>
          </div>

          <!-- Pagination / Load More -->
          <div class="shop-pagination-wrap">
            <button type="button" class="btn btn-outline load-more-btn" id="load-more-btn">
              LOAD MORE
            </button>
          </div>

        </div><!-- /shop-main -->

      </div><!-- /shop-layout -->

    </div>
  </section>

</main>

<?php include_once 'includes/footer.php'; ?>

<!-- Export Server Data to JS -->
<script>
  window.NOVA_PRODUCTS_DATA = <?= json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  window.NOVA_CURRENCY_SYMBOL = "<?= NOVA_CURRENCY_SYMBOL ?>";
</script>

<!-- Scripts -->
<script src="js/main.js" defer></script>
<script src="js/animations.js" defer></script>
<script src="js/interactions.js" defer></script>
<script src="js/quick-view.js" defer></script>
<script src="js/shop.js" defer></script>

</body>
</html>
