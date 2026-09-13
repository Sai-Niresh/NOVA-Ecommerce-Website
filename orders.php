<?php
require_once __DIR__ . '/config/database.php';

session_start();
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
  $cleanItems = [];
  $total = 0;

  foreach ($items as $item) {
    $id = (int) ($item['id'] ?? 0);
    $itemName = trim((string) ($item['name'] ?? ''));
    $price = (float) ($item['price'] ?? 0);
    $quantity = max(1, (int) ($item['quantity'] ?? 1));
    if ($id <= 0 || $itemName === '' || $price < 0) continue;
    $cleanItems[] = [$id, $itemName, $price, $quantity];
    $total += $price * $quantity;
  }

  if (!$cleanItems || $total <= 0) {
    orderResponse(false, 'Your cart contains no valid products.');
  }

  $db->beginTransaction();
  $userId = isset($_SESSION['nova_user']['id']) ? (int) $_SESSION['nova_user']['id'] : null;
  $insertOrder = $db->prepare('INSERT INTO orders (user_id, customer_name, phone, address, city, postal_code, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
  $insertOrder->execute([$userId, $name, $phone, $address, $city, $postal, $paymentMethod, $total]);
  $orderId = (int) $db->lastInsertId();
  $insertItem = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)');
  foreach ($cleanItems as [$id, $itemName, $price, $quantity]) {
    $insertItem->execute([$orderId, $id, $itemName, $price, $quantity]);
  }
  $db->commit();
  orderResponse(true, 'Order placed successfully.', ['order_id' => $orderId, 'total' => $total]);
} catch (Throwable $error) {
  if (isset($db) && $db->inTransaction()) $db->rollBack();
  error_log('NOVA order error: ' . $error->getMessage());
  orderResponse(false, 'We could not place your order. Please try again.');
}