<?php
/**
 * NOVA — Product Card Component Include
 * Reusable product card rendering function
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/products-data.php';

if (!function_exists('renderProductCard')) {
  /**
   * Renders product card HTML markup
   *
   * @param array $p Product array data
   * @param array $opts Display options
   * @return string HTML string
   */
  function renderProductCard(array $p, array $opts = []): string {
    $sym     = NOVA_CURRENCY_SYMBOL;
    $hasDisc = isset($p['original_price']) && $p['original_price'] > $p['price'];
    $disc    = $hasDisc ? (isset($p['discount']) ? $p['discount'] : round((($p['original_price'] - $p['price']) / $p['original_price']) * 100)) : 0;
    $img     = function_exists('nova_product_image_url') ? nova_product_image_url($p['image'] ?? '') : (string) ($p['image'] ?? '');
    $secImg  = !empty($p['secondary_image'])
      ? (function_exists('nova_product_image_url') ? nova_product_image_url($p['secondary_image']) : (string) $p['secondary_image'])
      : $img;

    $html  = '<article class="product-card" data-product-id="' . $p['id'] . '" data-category="' . htmlspecialchars($p['category']) . '" data-price="' . $p['price'] . '" data-rating="' . $p['rating'] . '" aria-label="' . htmlspecialchars($p['name']) . '">';
    $html .= '  <div class="product-image-wrapper">';

    // Badges
    if ($hasDisc) {
      $html .= '    <span class="product-badge product-badge--sale">' . $disc . '% OFF</span>';
    } elseif (!empty($p['badge'])) {
      $badgeClass = strtolower($p['badge']) === 'new' ? 'product-badge--new' : (strtolower($p['badge']) === 'trending' ? 'product-badge--trending' : 'product-badge--limited');
      $html .= '    <span class="product-badge ' . $badgeClass . '">' . htmlspecialchars($p['badge']) . '</span>';
    }

    // Images (Primary + Secondary for hover effect)
    $html .= '    <img class="product-img product-img-primary" src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($p['name']) . '" loading="lazy" width="400" height="500">';
    if ($secImg !== $img) {
      $html .= '    <img class="product-img product-img-secondary" src="' . htmlspecialchars($secImg) . '" alt="' . htmlspecialchars($p['name']) . ' alt view" loading="lazy" width="400" height="500">';
    }

    // Wishlist Button
    $html .= '    <button class="product-wishlist-btn" data-id="' . $p['id'] . '" aria-label="Add ' . htmlspecialchars($p['name']) . ' to wishlist">';
    $html .= '      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
    $html .= '        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>';
    $html .= '      </svg>';
    $html .= '    </button>';

    // Quick View Overlay Button
    $html .= '    <div class="product-card-actions">';
    $html .= '      <button class="product-quickview-btn" data-id="' . $p['id'] . '" aria-label="Quick View ' . htmlspecialchars($p['name']) . '">';
    $html .= '        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
    $html .= '        <span>Quick View</span>';
    $html .= '      </button>';
    $html .= '    </div>';

    $html .= '  </div>'; // /product-image-wrapper

    // Info Section
    $html .= '  <div class="product-info">';
    $html .= '    <span class="product-category-label">' . htmlspecialchars($p['category']) . '</span>';
    $html .= '    <h3 class="product-name"><a href="product.php?id=' . (int) $p['id'] . '">' . htmlspecialchars($p['name']) . '</a></h3>';

    // Star Rating
    $html .= '    <div class="product-rating">';
    $html .= '      <div class="stars" aria-hidden="true">';
    for ($i = 1; $i <= 5; $i++) {
      $filled = $i <= round($p['rating']);
      $html .= '        <svg width="12" height="12" viewBox="0 0 24 24" fill="' . ($filled ? 'currentColor' : 'none') . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
      $html .= '          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>';
      $html .= '        </svg>';
    }
    $html .= '      </div>';
    $html .= '      <span class="rating-count" aria-label="' . number_format($p['rating'], 1) . ' out of 5 — ' . $p['reviews'] . ' reviews">(' . $p['reviews'] . ')</span>';
    $html .= '    </div>';

    // Price Row
    $html .= '    <div class="product-price-row">';
    $html .= '      <span class="product-price-current">' . $sym . number_format($p['price']) . '</span>';
    if ($hasDisc) {
      $html .= '      <span class="product-price-original">' . $sym . number_format($p['original_price']) . '</span>';
      $html .= '      <span class="product-discount">' . $disc . '% off</span>';
    }
    $html .= '    </div>';

    // Color swatches hint (if available)
    // Colors may arrive as objects, plain names or hex strings — never assume an array.
    if (!empty($p['colors']) && is_array($p['colors'])) {
      $swatches = [];
      foreach ($p['colors'] as $col) {
        if (is_array($col)) {
          $cName = (string) ($col['name'] ?? ($col['hex'] ?? ''));
          $cHex  = (string) ($col['hex'] ?? '');
        } else {
          $cName = (string) $col;
          $cHex  = '';
        }
        if ($cName === '' && $cHex === '') continue;
        if ($cHex === '' && preg_match('/^#?[0-9a-fA-F]{3}$|^#?[0-9a-fA-F]{6}$/', trim($cName))) {
          $cHex = strpos(trim($cName), '#') === 0 ? trim($cName) : '#' . trim($cName);
        }
        if ($cHex === '') $cHex = '#cccccc';
        if ($cName === '') $cName = $cHex;
        $swatches[] = ['name' => $cName, 'hex' => $cHex];
      }
      if (!empty($swatches)) {
        $html .= '    <div class="product-color-preview" aria-label="Available colors">';
        foreach (array_slice($swatches, 0, 4) as $col) {
          $html .= '      <span class="color-dot" style="background-color: ' . htmlspecialchars($col['hex']) . '" title="' . htmlspecialchars($col['name']) . '"></span>';
        }
        if (count($swatches) > 4) {
          $html .= '      <span class="color-dot-more">+' . (count($swatches) - 4) . '</span>';
        }
        $html .= '    </div>';
      }
    }

    // Add to Cart Button
    $html .= '    <button class="product-add-btn" data-id="' . $p['id'] . '" aria-label="Add ' . htmlspecialchars($p['name']) . ' to cart">Add to Cart</button>';

    $html .= '  </div>'; // /product-info
    $html .= '</article>';

    return $html;
  }
}
