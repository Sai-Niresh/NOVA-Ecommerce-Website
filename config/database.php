<?php
/**
 * NOVA local database connection.
 * Update these values for a non-default MySQL installation.
 */

define('NOVA_DB_HOST', getenv('NOVA_DB_HOST') ?: '127.0.0.1');
define('NOVA_DB_NAME', getenv('NOVA_DB_NAME') ?: 'nova_db');
define('NOVA_DB_USER', getenv('NOVA_DB_USER') ?: 'root');
define('NOVA_DB_PASSWORD', getenv('NOVA_DB_PASSWORD') ?: '');

function novaDb(): PDO {
  static $pdo = null;

  if ($pdo instanceof PDO) {
    return $pdo;
  }

  $serverDsn = 'mysql:host=' . NOVA_DB_HOST . ';charset=utf8mb4';
  $server = new PDO($serverDsn, NOVA_DB_USER, NOVA_DB_PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);

  $database = str_replace('`', '``', NOVA_DB_NAME);
  $server->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

  $pdo = new PDO(
    'mysql:host=' . NOVA_DB_HOST . ';dbname=' . NOVA_DB_NAME . ';charset=utf8mb4',
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
    'id INT UNSIGNED PRIMARY KEY,' .
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

  $paymentColumn = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method'")->fetchColumn();
  if (!$paymentColumn) {
    $pdo->exec("ALTER TABLE orders ADD payment_method VARCHAR(30) NOT NULL DEFAULT 'cod' AFTER postal_code");
  }

  return $pdo;
}
