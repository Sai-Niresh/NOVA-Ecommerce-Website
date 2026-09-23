<?php
/**
 * NOVA local database connection.
 * Update these values for a non-default MySQL installation.
 */

// Production-compatible DB env vars (DB_*). When present they win; otherwise
// the original local XAMPP defaults below are used unchanged.
define('NOVA_DB_HOST', getenv('DB_HOST') ?: (getenv('NOVA_DB_HOST') ?: '127.0.0.1'));
define('NOVA_DB_PORT', getenv('DB_PORT') ?: (getenv('NOVA_DB_PORT') ?: '3306'));
define('NOVA_DB_NAME', getenv('DB_NAME') ?: (getenv('NOVA_DB_NAME') ?: 'nova_db'));
define('NOVA_DB_USER', getenv('DB_USER') ?: (getenv('NOVA_DB_USER') ?: 'root'));
define('NOVA_DB_PASSWORD', getenv('DB_PASSWORD') ?: (getenv('NOVA_DB_PASSWORD') ?: ''));

function novaDb(): PDO {
  static $pdo = null;

  if ($pdo instanceof PDO) {
    return $pdo;
  }

  $serverDsn = 'mysql:host=' . NOVA_DB_HOST . ';port=' . NOVA_DB_PORT . ';charset=utf8mb4';
  $server = new PDO($serverDsn, NOVA_DB_USER, NOVA_DB_PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);

  $database = str_replace('`', '``', NOVA_DB_NAME);
  /* Managed MySQL providers often deny CREATE DATABASE. XAMPP still creates it;
     elsewhere we continue and expect the database to already exist. */
  try {
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  } catch (PDOException $e) {
    // Requires the database to exist and the configured user to have access.
  }

  $pdo = new PDO(
    'mysql:host=' . NOVA_DB_HOST . ';port=' . NOVA_DB_PORT . ';dbname=' . NOVA_DB_NAME . ';charset=utf8mb4',
    NOVA_DB_USER,
    NOVA_DB_PASSWORD,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );

  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS users (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'name VARCHAR(120) NOT NULL,' .
    'email VARCHAR(190) NOT NULL UNIQUE,' .
    'password_hash VARCHAR(255) NOT NULL,' .
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS admin_users (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'email VARCHAR(190) NOT NULL UNIQUE,' .
    'password_hash VARCHAR(255) NOT NULL,' .
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  $adminEmail = getenv('NOVA_ADMIN_EMAIL') ?: 'admin@nova.local';
  $adminPassword = getenv('NOVA_ADMIN_PASSWORD') ?: 'admin123';
  $adminCheck = $pdo->prepare('SELECT id FROM admin_users WHERE email = ? LIMIT 1');
  $adminCheck->execute([$adminEmail]);
  if (!$adminCheck->fetch()) {
    $adminInsert = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (?, ?)');
    $adminInsert->execute([$adminEmail, password_hash($adminPassword, PASSWORD_DEFAULT)]);
  }

  /* ── Seed default demo storefront user so the auth modal has a known
     account to sign in with (email: phase3-demo@nova.local,
     password: Demo1234). Only created when no users exist at all. ── */
  $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
  if ($userCount === 0) {
    $demoInsert = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $demoInsert->execute(['NOVA Demo', 'phase3-demo@nova.local', password_hash('Demo1234', PASSWORD_DEFAULT)]);
  }

  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS orders (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'user_id INT UNSIGNED NULL,' .
    'customer_name VARCHAR(120) NOT NULL,' .
    'phone VARCHAR(30) NOT NULL,' .
    'address VARCHAR(255) NOT NULL,' .
    'city VARCHAR(100) NOT NULL,' .
    'postal_code VARCHAR(20) NOT NULL,' .
    'payment_method VARCHAR(30) NOT NULL DEFAULT "cod",' .
    'total_amount DECIMAL(12,2) NOT NULL,' .
    'status VARCHAR(30) NOT NULL DEFAULT "placed",' .
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,' .
    'INDEX idx_orders_user (user_id),' .
    'CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS order_items (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'order_id INT UNSIGNED NOT NULL,' .
    'product_id INT UNSIGNED NOT NULL,' .
    'product_name VARCHAR(190) NOT NULL,' .
    'unit_price DECIMAL(12,2) NOT NULL,' .
    'quantity INT UNSIGNED NOT NULL,' .
    'CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,' .
    'INDEX idx_order_items_order (order_id)' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS products (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'name VARCHAR(190) NOT NULL,' .
    'category VARCHAR(80) NOT NULL,' .
    'price DECIMAL(12,2) NOT NULL,' .
    'original_price DECIMAL(12,2) NULL,' .
    'discount INT NULL,' .
    'rating DECIMAL(3,2) NOT NULL DEFAULT 0,' .
    'reviews INT UNSIGNED NOT NULL DEFAULT 0,' .
    'image TEXT NOT NULL,' .
    'secondary_image TEXT NULL,' .
    'colors_json JSON NULL,' .
    'sizes_json JSON NULL,' .
    'stock INT NOT NULL DEFAULT 0,' .
    'description TEXT NULL,' .
    'badge VARCHAR(40) NULL,' .
    'created_at DATE NULL,' .
    'featured TINYINT(1) NOT NULL DEFAULT 0,' .
    'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,' .
    'INDEX idx_products_category (category),' .
    'INDEX idx_products_featured (featured)' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  $productAutoInc = $pdo->query("SELECT EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'id'")->fetchColumn();
  if (strtolower((string)$productAutoInc) !== 'auto_increment') {
    $pdo->exec("ALTER TABLE products MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT");
  }

  $paymentColumn = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method'")->fetchColumn();
  if (!$paymentColumn) {
    $pdo->exec("ALTER TABLE orders ADD payment_method VARCHAR(30) NOT NULL DEFAULT 'cod' AFTER postal_code");
  }

  /* ── Site content CMS table (hero banner, footer, homepage copy) ── */
  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS site_content (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'section VARCHAR(50) NOT NULL UNIQUE,' .
    'html_content TEXT NOT NULL,' .
    'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  /* ── Seed default site content if empty ── */
  $siteContentCount = (int) $pdo->query('SELECT COUNT(*) FROM site_content')->fetchColumn();
  if ($siteContentCount === 0) {
    $seedContent = $pdo->prepare('INSERT INTO site_content (section, html_content) VALUES (?, ?)');
    $seedContent->execute(['hero_banner', '<div class="hero-overlay"></div><div class="hero-content"><span class="product-category-label--detail">NEW SEASON ARRIVAL</span><h1>Define Your Style</h1><p>Premium fashion for the modern individual.</p><a href="shop.php" class="btn btn-primary btn-large" style="margin-top:1.5rem;">Shop Now</a></div>']);
    $seedContent->execute(['footer_text', '© ' . date('Y') . ' NOVA. All rights reserved. | Crafted with precision.']);
    $seedContent->execute(['homepage_subtitle', '<p>Discover curated collections designed for those who refuse to blend in.</p>']);
  }

  /* ── Categories table for CMS management ── */
  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS categories (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'name VARCHAR(80) NOT NULL UNIQUE,' .
    'slug VARCHAR(80) NOT NULL UNIQUE,' .
    'description TEXT NULL,' .
    'sort_order INT NOT NULL DEFAULT 0,' .
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
  );

  /* ── Seed default categories if empty ── */
  $catCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
  if ($catCount === 0) {
    $seedCat = $pdo->prepare('INSERT INTO categories (name, slug, description, sort_order) VALUES (?, ?, ?, ?)');
    $defaultCategories = [
      ['Men',        'men',        'Menswear, tailoring, and casual essentials.',        1],
      ['Women',      'women',      'Womenswear, dresses, and everyday elegance.',         2],
      ['Shoes',      'shoes',      'Footwear for every occasion.',                        3],
      ['Watches',    'watches',    'Timepieces that make a statement.',                   4],
      ['Bags',       'bags',       'Bags and accessories to complete your look.',         5],
      ['Accessories','accessories','Scarves, belts, jewelry, and more.',                  6],
    ];
    foreach ($defaultCategories as $cat) {
      $seedCat->execute($cat);
    }
  }

  return $pdo;
}
