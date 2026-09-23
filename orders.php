<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/bootstrap.php';

nova_session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  try {
    $db = novaDb();
    $userId = isset($_SESSION['nova_user']['id']) ? (int) $_SESSION['nova_user']['id'] : 0;
    if (!$userId) orderResponse(false, 'Sign in to view your order history.');
    $query = $db->prepare('SELECT id, city, total_amount, payment_method, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $query->execute([$userId]);
    orderResponse(true, 'Orders loaded.', ['orders' => $query->fetchAll()]);
  } catch (Throwable $error) {
    orderResponse(false, 'Unable to load your orders.');
  }
}

$payload = json_decode(file_get_contents('php://input'), true);
$payload = is_array($payload) ? $payload : [];
$items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
$customer = is_array($payload['customer'] ?? null) ? $payload['customer'] : [];

function orderResponse(bool $success, string $message, array $extra = []): never {
  http_response_code($success ? 200 : 400);
  echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
  exit;
}

if (!$items || !$customer) {
  orderResponse(false, 'Complete your delivery details and add at least one product.');
}

$name = trim((string) ($customer['name'] ?? ''));
$phone = trim((string) ($customer['phone'] ?? ''));
$address = trim((string) ($customer['address'] ?? ''));
$city = trim((string) ($customer['city'] ?? ''));
$postal = trim((string) ($customer['postal'] ?? ''));
$paymentMethod = strtolower(trim((string) ($payload['payment_method'] ?? 'cod')));

if ($name === '' || $phone === '' || $address === '' || $city === '' || $postal === '') {
  orderResponse(false, 'Complete every delivery field before placing your order.');
}
if (!in_array($paymentMethod, ['upi', 'card', 'cod'], true)) {
  orderResponse(false, 'Choose a valid payment method.');
}

try {
  $db = novaDb();
  require_once __DIR__ . '/includes/products-data.php';

  $cleanItems = [];
  $total = 0;

  /* SECURITY: never trust client prices — re-price every item from the
     database and validate stock inside the same transaction. */
  foreach ($items as $item) {
    $id = (int) ($item['id'] ?? 0);
    $quantity = max(1, min(99, (int) ($item['quantity'] ?? 1)));
    if ($id <= 0) continue;

    $product = getProductById($id);
    if (!$product || (int) $product['stock'] < $quantity) continue;

    $unitPrice = (float) $product['price'];
    $cleanItems[] = [$id, $product['name'], $unitPrice, $quantity];
    $total += $unitPrice * $quantity;
  }

  if (!$cleanItems || $total <= 0) {
    orderResponse(false, 'Your cart contains no available products.');
  }

  $db->beginTransaction();
  $userId = isset($_SESSION['nova_user']['id']) ? (int) $_SESSION['nova_user']['id'] : null;
  $insertOrder = $db->prepare('INSERT INTO orders (user_id, customer_name, phone, address, city, postal_code, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
  $insertOrder->execute([$userId, $name, $phone, $address, $city, $postal, $paymentMethod, $total]);
  $orderId = (int) $db->lastInsertId();
  $insertItem = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)');
  $updateStock = $db->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
  foreach ($cleanItems as [$id, $itemName, $price, $quantity]) {
    $insertItem->execute([$orderId, $id, $itemName, $price, $quantity]);
    $updateStock->execute([$quantity, $id, $quantity]);
    if ($updateStock->rowCount() === 0) {
      throw new RuntimeException('Stock changed during checkout for product #' . $id);
    }
  }
  $db->commit();
  orderResponse(true, 'Order placed successfully.', ['order_id' => $orderId, 'total' => $total]);
} catch (Throwable $error) {
  if (isset($db) && $db->inTransaction()) $db->rollBack();
  error_log('NOVA order error: ' . $error->getMessage());
  orderResponse(false, 'We could not place your order. Please try again.');
}