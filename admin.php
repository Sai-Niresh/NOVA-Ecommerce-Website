<?php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/products-data.php';

nova_session_start();

const NOVA_ORDER_STATUSES = ['placed', 'processing', 'shipped', 'delivered', 'cancelled'];

if (isset($_GET['logout'])) {
    unset($_SESSION['nova_admin']);
    header('Location: admin.php');
    exit;
}

$loginError = '';
$loginSuccess = '';
$adminMode = (string)($_POST['mode'] ?? ($_GET['mode'] ?? 'login'));
if (!in_array($adminMode, ['login', 'register'], true)) {
    $adminMode = 'login';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'login' || ($_POST['action'] ?? '') === 'register')) {
    $adminAct = (string)$_POST['action'];
    try {
        if (!nova_csrf_verify($_POST)) {
            $loginError = 'Session expired. Please try again.';
        } else {
            $db = novaDb();
            $email = strtolower(trim((string)($_POST['email'] ?? '')));
            $password = (string)($_POST['password'] ?? '');

            if ($adminAct === 'login') {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
                    $loginError = 'Enter your admin email and password.';
                } else {
                    $q = $db->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = ? LIMIT 1');
                    $q->execute([$email]);
                    $admin = $q->fetch();
                    if ($admin && password_verify($password, $admin['password_hash'])) {
                        session_regenerate_id(true);
                        $_SESSION['nova_admin'] = ['id' => (int)$admin['id'], 'email' => $admin['email']];
                        header('Location: admin.php');
                        exit;
                    }
                    $loginError = 'Incorrect admin email or password.';
                }
            } elseif ($adminAct === 'register') {
                $confirm = (string)($_POST['confirm_password'] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $loginError = 'Enter a valid admin email address.';
                } elseif (strlen($password) < 6) {
                    $loginError = 'Password must be at least 6 characters.';
                } elseif ($password !== $confirm) {
                    $loginError = 'Passwords do not match.';
                } else {
                    $check = $db->prepare('SELECT id FROM admin_users WHERE email = ? LIMIT 1');
                    $check->execute([$email]);
                    if ($check->fetch()) {
                        $loginError = 'An admin account with this email already exists.';
                    } else {
                        $insert = $db->prepare('INSERT INTO admin_users (email, password_hash) VALUES (?, ?)');
                        $insert->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
                        session_regenerate_id(true);
                        $_SESSION['nova_admin'] = ['id' => (int)$db->lastInsertId(), 'email' => $email];
                        header('Location: admin.php');
                        exit;
                    }
                }
            }
        }
    } catch (Throwable $e) {
        $loginError = 'DEBUG: ' . $e->getMessage();
    }
}

$isAdmin = !empty($_SESSION['nova_admin']);
$activeTab = 'dashboard';
$flash = null;
$metrics = ['revenue'=>0,'orders'=>0,'customers'=>0,'products'=>0,'lowStock'=>0];
$recentOrders = []; $allOrders = []; $adminProducts = []; $customers = [];
$siteContentSections = []; $categories = [];

if ($isAdmin) {
    $adminTabs = ['dashboard','orders','products','customers','site-content','categories'];
    $activeTab = in_array($_GET['tab'] ?? 'dashboard', $adminTabs, true) ? (string)($_GET['tab'] ?? 'dashboard') : 'dashboard';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string)($_POST['action'] ?? '');
        $flashMsg = ''; $flashType = 'success';
        try {
            if (!nova_csrf_verify($_POST)) { $flashMsg = 'Security token mismatch.'; $flashType = 'error'; }
            elseif ($action === 'update_status') {
                $oid = (int)($_POST['order_id'] ?? 0); $st = strtolower(trim((string)($_POST['status'] ?? '')));
                if ($oid > 0 && in_array($st, NOVA_ORDER_STATUSES, true)) {
                    $db = novaDb(); $s = $db->prepare('UPDATE orders SET status = ? WHERE id = ?'); $s->execute([$st, $oid]);
                    $flashMsg = $s->rowCount()>0 ? 'Order #' . $oid . ' marked as ' . ucfirst($st) . '.' : 'Already that status.';
                } else { $flashMsg = 'Invalid order request.'; $flashType = 'error'; }
            }
            elseif ($action === 'update_product') {
                $pid = (int)($_POST['product_id'] ?? 0); $price = (float)($_POST['price'] ?? 0); $stock = (int)($_POST['stock'] ?? -1); $feat = isset($_POST['featured']) ? 1 : 0;
                if ($pid>0 && $price>0 && $price<=10000000 && $stock>=0 && $stock<=1000000) {
                    $db = novaDb(); $s = $db->prepare('UPDATE products SET price=?, stock=?, featured=? WHERE id=?'); $s->execute([$price,$stock,$feat,$pid]);
                    $flashMsg = $s->rowCount()>0 ? 'Product #' . $pid . ' updated.' : 'No changes.';
                } else { $flashMsg = 'Invalid product request.'; $flashType = 'error'; }
            }
            elseif ($action === 'save_product') {
                $pid = (int)($_POST['product_id'] ?? 0);
                $pName = trim((string)($_POST['name'] ?? ''));
                $pCategory = trim((string)($_POST['category'] ?? ''));
                $pPrice = (float)($_POST['price'] ?? 0);
                $pOriginal = (float)($_POST['original_price'] ?? 0);
                $pRating = max(0, min(5, (float)($_POST['rating'] ?? 0)));
                $pReviews = max(0, (int)($_POST['reviews'] ?? 0));
                $pImage = trim((string)($_POST['image'] ?? ''));
                $pSecondary = trim((string)($_POST['secondary_image'] ?? ''));

                $errors = [];

                /* Optional file upload — overrides the URL/path field when provided */
                if (!empty($_FILES['image_file']['name']) && is_uploaded_file($_FILES['image_file']['tmp_name'] ?? '')) {
                    $up = $_FILES['image_file'];
                    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                    if ($up['error'] !== UPLOAD_ERR_OK) {
                        $errors[] = 'Image upload failed (code ' . $up['error'] . ').';
                    } elseif ($up['size'] > 4 * 1024 * 1024) {
                        $errors[] = 'Uploaded image exceeds the 4 MB limit.';
                    } else {
                        $info = @getimagesize($up['tmp_name']);
                        $ext = $info !== false ? ($allowed[$info['mime']] ?? null) : null;
                        if ($ext === null) {
                            $errors[] = 'Uploaded file is not a valid JPG, PNG, WebP or GIF image.';
                        } else {
                            $upDir = __DIR__ . '/uploads';
                            if (!is_dir($upDir)) { @mkdir($upDir, 0777, true); }
                            $fname = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                            if (move_uploaded_file($up['tmp_name'], $upDir . '/' . $fname)) {
                                $pImage = 'uploads/' . $fname;
                            } else {
                                $errors[] = 'Could not save the uploaded image — check folder permissions.';
                            }
                        }
                    }
                }
                $pStock = (int)($_POST['stock'] ?? 0);
                $pBadge = trim((string)($_POST['badge'] ?? ''));
                $pFeatured = isset($_POST['featured']) ? 1 : 0;
                $pDescription = trim((string)($_POST['description'] ?? ''));
                $colorsRaw = trim((string)($_POST['colors'] ?? ''));
                $sizesRaw = trim((string)($_POST['sizes'] ?? ''));

                $colorsJson = null; $sizesJson = null;
                if ($colorsRaw !== '') {
                    $decoded = json_decode($colorsRaw, true);
                    if (!is_array($decoded)) {
                        $decoded = array_filter(array_map('trim', explode(',', $colorsRaw)), fn($v) => $v !== '');
                    }
                    // Store the canonical [{ name, hex }] shape (shared normalizer,
                    // so the palette lives in one place) — renderers never see a bare string.
                    $canonicalColors = function_exists('nova_normalize_colors')
                        ? nova_normalize_colors($decoded)
                        : array_map(function ($v) {
                            return is_array($v)
                                ? ['name' => (string)($v['name'] ?? ''), 'hex' => (string)($v['hex'] ?? '#cccccc')]
                                : ['name' => (string)$v, 'hex' => '#cccccc'];
                          }, $decoded);
                    if (!empty($canonicalColors)) {
                        $colorsJson = json_encode($canonicalColors, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } else {
                        $errors[] = 'Colors must be valid JSON array or comma-separated list.';
                    }
                }
                if ($sizesRaw !== '') {
                    $decoded = json_decode($sizesRaw, true);
                    if (is_array($decoded)) {
                        $sizesJson = json_encode(array_values($decoded), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } else {
                        $parts = array_filter(array_map('trim', explode(',', $sizesRaw)), fn($v) => $v !== '');
                        if (!empty($parts)) {
                            $sizesJson = json_encode(array_values($parts), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        } else {
                            $errors[] = 'Sizes must be valid JSON array or comma-separated list.';
                        }
                    }
                }
                if ($pName === '') { $errors[] = 'Product name is required.'; }
                if ($pCategory === '') { $errors[] = 'Product category is required.'; }
                if ($pImage === '') {
                    /* Image optional — placeholder keeps product cards & dashboard intact */
                    $pImage = 'images/placeholder.svg';
                }
                if ($pPrice <= 0 || $pPrice > 10000000) { $errors[] = 'Enter a valid price between ₹0.01 and ₹10,000,000.'; }
                if (!isset($_POST['stock']) || trim((string)$_POST['stock']) === '' || (int)$_POST['stock'] < 0) {
                    $errors[] = 'Stock quantity must be a non-negative number.';
                }

                if ($errors) {
                    $flashMsg = implode(' ', $errors); $flashType = 'error';
                } else {
                    $pDiscount = ($pOriginal > 0 && $pOriginal > $pPrice) ? (int)round((($pOriginal - $pPrice) / $pOriginal) * 100) : null;
                    $db = novaDb();
                    if ($pid > 0) {
                        $s = $db->prepare('UPDATE products SET name=?, category=?, price=?, original_price=?, discount=?, rating=?, reviews=?, image=?, secondary_image=?, colors_json=?, sizes_json=?, stock=?, description=?, badge=?, featured=?, updated_at=CURRENT_TIMESTAMP WHERE id=?');
                        $s->execute([$pName, $pCategory, $pPrice, $pOriginal > 0 ? $pOriginal : null, $pDiscount, $pRating, $pReviews, $pImage, $pSecondary !== '' ? $pSecondary : null, $colorsJson, $sizesJson, $pStock, $pDescription !== '' ? $pDescription : null, $pBadge !== '' ? $pBadge : null, $pFeatured, $pid]);
                        $flashMsg = 'Product "' . $pName . '" (#' . $pid . ') updated successfully.';
                    } else {
                        $s = $db->prepare('INSERT INTO products (name, category, price, original_price, discount, rating, reviews, image, secondary_image, colors_json, sizes_json, stock, description, badge, featured, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURDATE(),CURRENT_TIMESTAMP)');
                        $s->execute([$pName, $pCategory, $pPrice, $pOriginal > 0 ? $pOriginal : null, $pDiscount, $pRating, $pReviews, $pImage, $pSecondary !== '' ? $pSecondary : null, $colorsJson, $sizesJson, $pStock, $pDescription !== '' ? $pDescription : null, $pBadge !== '' ? $pBadge : null, $pFeatured]);
                        $newId = (int)$db->lastInsertId();
                        $flashMsg = 'Product "' . $pName . '" (#' . $newId . ') added to catalog successfully.';
                    }
                }
            }
            elseif ($action === 'delete_product') {
                $pid = (int)($_POST['product_id'] ?? 0);
                if ($pid > 0) {
                    $db = novaDb(); $s = $db->prepare('DELETE FROM products WHERE id=?'); $s->execute([$pid]);
                    $flashMsg = $s->rowCount() > 0 ? 'Product #' . $pid . ' deleted successfully.' : 'Product not found.';
                } else { $flashMsg = 'Invalid product request.'; $flashType = 'error'; }
            }
            elseif ($action === 'delete_category') {
                $cid = (int)($_POST['category_id'] ?? 0);
                if ($cid > 0) {
                    $db = novaDb(); $u = $db->prepare('SELECT COUNT(*) FROM products WHERE category = (SELECT name FROM categories WHERE id=?)'); $u->execute([$cid]);
                    if ((int)$u->fetchColumn() > 0) { $flashMsg = 'Cannot delete: products use this category.'; $flashType='error'; }
                    else { $s = $db->prepare('DELETE FROM categories WHERE id=?'); $s->execute([$cid]); $flashMsg = $s->rowCount()>0 ? 'Category deleted.' : 'Not found.'; }
                } else { $flashMsg = 'Invalid.'; $flashType='error'; }
            }
            elseif ($action === 'save_category') {
                $cid2 = (isset($_POST['category_id']) && (int)($_POST['category_id'] ?? 0) > 0) ? (int)($_POST['category_id'] ?? 0) : null;
                $nm = trim((string)($_POST['name'] ?? '')); $sl = trim((string)($_POST['slug'] ?? ''));
                $ds = trim((string)($_POST['description'] ?? '')); $so = (int)($_POST['sort_order'] ?? 0);
                if ($nm==='') { $flashMsg='Name required.'; $flashType='error'; }
                elseif ($sl==='') { $flashMsg='Slug required.'; $flashType='error'; }
                else {
                    $db = novaDb();
                    if ($cid2) { $s=$db->prepare('UPDATE categories SET name=?,slug=?,description=?,sort_order=? WHERE id=?'); $s->execute([$nm,$sl,($ds!==''?$ds:null),$so,$cid2]); $flashMsg='Category "' . htmlspecialchars($nm) . '" updated.'; }
                    else { $s=$db->prepare('INSERT INTO categories (name,slug,description,sort_order) VALUES (?,?,?,?)'); $s->execute([$nm,$sl,($ds!==''?$ds:null),$so]); $flashMsg='Category "' . htmlspecialchars($nm) . '" added.'; }
                }
            }
            elseif ($action === 'save_site_content') {
                $sec = trim((string)($_POST['section'] ?? '')); $html = trim((string)($_POST['html_content'] ?? ''));
                if ($sec==='') { $flashMsg='Section required.'; $flashType='error'; }
                elseif ($html==='') { $flashMsg='Content required.'; $flashType='error'; }
                else { $db=novaDb(); $s=$db->prepare('INSERT INTO site_content (section,html_content) VALUES (?,?) ON DUPLICATE KEY UPDATE html_content=VALUES(html_content)'); $s->execute([$sec,$html]); $flashMsg='Content saved.'; }
            }
            else { $flashMsg='Unknown action.'; $flashType='error'; }
        } catch (Throwable $e) {
            error_log('Admin error: '.$e->getMessage());
            $flashMsg = 'Database error: ' . $e->getMessage();
            $flashType = 'error';
        }

        if ($flashMsg!=='') { $flash=['message'=>$flashMsg,'type'=>$flashType]; }
        if ($action === 'save_product' && $flashMsg!=='') {
            if ($flashType === 'success') {
                /* Saved — land on the dashboard where the new/edited product is visible */
                header('Location: admin.php?tab=dashboard&flash='.urlencode($flashMsg)); exit;
            }
            /* Validation/upload error — return to the form with the error message visible */
            $back = $pid > 0 ? ('edit='.$pid) : 'add=1';
            header('Location: admin.php?tab=products&'.$back.'&flash='.urlencode($flashMsg)); exit;
        }
        if ($flashType==='success' && $flashMsg!=='') {
            $qs='tab='.urlencode($activeTab).'&flash='.urlencode($flashMsg);
            header('Location: admin.php?'.$qs); exit;
        }
    }

    try {
        $db = novaDb();
        $metrics['revenue']=(float)$db->query('SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status!="cancelled"')->fetchColumn();
        $metrics['orders']=(int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $metrics['customers']=(int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $metrics['products']=(int)$db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $metrics['lowStock']=(int)$db->query('SELECT COUNT(*) FROM products WHERE stock<10')->fetchColumn();
        $recentOrders=$db->query('SELECT o.id,o.customer_name,o.total_amount,o.status,o.created_at,(SELECT COUNT(*) FROM order_items WHERE order_id=o.id) AS ic FROM orders o ORDER BY o.created_at DESC LIMIT 5')->fetchAll();
        $allOrders=$db->query('SELECT o.id,o.customer_name,o.phone,o.total_amount,o.status,o.created_at,(SELECT COUNT(*) FROM order_items WHERE order_id=o.id) AS ic FROM orders o ORDER BY o.created_at DESC')->fetchAll();
        $adminProducts=$db->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
        $customers=$db->query('SELECT u.id,u.name,u.email,u.created_at,COUNT(o.id) AS oc,COALESCE(SUM(o.total_amount),0) AS lv FROM users u LEFT JOIN orders o ON o.user_id=u.id GROUP BY u.id ORDER BY u.created_at DESC')->fetchAll();
        $siteContentSections=$db->query('SELECT section,html_content,updated_at FROM site_content ORDER BY section ASC')->fetchAll();
        $categories=$db->query('SELECT * FROM categories ORDER BY sort_order ASC')->fetchAll();
    } catch (Throwable $e) { error_log('Load error: '.$e->getMessage()); }
}

if (!$flash && isset($_GET['flash'])) { $flash=['message'=>(string)$_GET['flash'],'type'=>'success']; }

$editProduct=null;
$isAddingProduct=false;
if ($isAdmin && $activeTab==='products' && isset($_GET['edit'])) {
    if ($_GET['edit'] === 'new') {
        $isAddingProduct = true;
    } else {
        $eid = (int)$_GET['edit'];
        foreach ($adminProducts as $p) {
            if ((int)$p['id'] === $eid) {
                $editProduct = $p;
                break;
            }
        }
    }
}

function dColors($r){ $d=json_decode($r,true); return is_array($d)?$d:[]; }
function dSizes($r){ $d=json_decode($r,true); return is_array($d)?$d:[]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isAdmin ? 'NOVA Admin' : 'NOVA Admin Login' ?></title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .admin-body { max-width: 1100px; margin: 0 auto; padding: 2rem; font-family: inherit; color: #1a1a2e; }
    .admin-login { max-width: 400px; margin: 4rem auto; padding: 2.5rem; border: 1px solid #e0e0e0; border-radius: 12px; background: #fff; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
    .admin-login h1 { font-size: 1.5rem; margin: 0 0 0.25rem; }
    .admin-login .sub { color: #888; font-size: 0.875rem; margin-bottom: 2rem; }
    .admin-login label { display: block; font-size: 0.8125rem; font-weight: 600; margin: 1rem 0 0.35rem; color: #555; }
    .admin-login input[type="email"], .admin-login input[type="password"] { width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9375rem; box-sizing: border-box; }
    .admin-login .btn { margin-top: 1.5rem; width: 100%; }
    .admin-login .error { color: #c0392b; font-size: 0.875rem; margin-top: 1rem; background: #fdf2f2; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #f5c6c6; }
    .admin-nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #eee; margin-bottom: 2rem; flex-wrap: wrap; gap: 0.5rem; }
    .admin-nav .brand { font-size: 1.25rem; font-weight: 700; color: #1a1a2e; text-decoration: none; }
    .admin-nav .nav-links { display: flex; gap: 0.25rem; flex-wrap: wrap; }
    .admin-nav .nav-links a { padding: 0.4rem 0.85rem; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 500; color: #666; }
    .admin-nav .nav-links a.active { background: #1a1a2e; color: #fff; }
    .admin-nav .nav-links a:hover:not(.active) { background: #f5f5f5; }
    .admin-nav .user-info { font-size: 0.875rem; color: #888; }
    .admin-nav .user-info a { color: #c0392b; text-decoration: none; }
    .admin-section { margin-bottom: 2rem; }
    .admin-section-heading { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem; border-bottom: 2px solid #1a1a2e; padding-bottom: 0.75rem; }
    .admin-section-heading h2 { margin: 0; font-size: 1.25rem; }
    .admin-section-heading p { margin: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: #999; }
    .admin-section-heading a { color: #1a1a2e; text-decoration: none; font-size: 0.875rem; font-weight: 500; border: 1px solid #ddd; padding: 0.3rem 0.75rem; border-radius: 6px; }
    .admin-table-wrap { overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .admin-table th { text-align: left; padding: 0.625rem 0.75rem; border-bottom: 2px solid #eee; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #999; background: #fafafa; }
    .admin-table td { padding: 0.625rem 0.75rem; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
    .admin-table tr:hover td { background: #fafafa; }
    .admin-muted { color: #999; font-size: 0.8125rem; }
    .admin-low { color: #c0392b; font-weight: 600; }
    .admin-ok { color: #27ae60; font-weight: 600; }
    .admin-inline-form { display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; }
    .admin-inline-form input[type="number"] { width: 70px; padding: 0.3rem 0.5rem; border: 1px solid #ddd; border-radius: 6px; font-size: 0.8125rem; }
    .admin-mini-label { font-size: 0.8125rem; color: #888; display: flex; align-items: center; gap: 0.25rem; }
    .admin-featured-label { font-size: 0.8125rem; color: #888; display: flex; align-items: center; gap: 0.25rem; }
    .admin-save-btn { padding: 0.3rem 0.7rem; background: #1a1a2e; color: #fff; border: none; border-radius: 6px; font-size: 0.8125rem; cursor: pointer; font-weight: 500; }
    .admin-save-btn:hover { background: #2d2d44; }
    .admin-alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.875rem; }
    .admin-alert.success { background: #eafaf1; border: 1px solid #a9dfbf; color: #1e8449; }
    .admin-alert.error { background: #fdf2f2; border: 1px solid #f5c6c6; color: #c0392b; }
    .admin-card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .admin-metric-card { padding: 1.25rem; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; }
    .admin-metric-card .label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #999; margin-bottom: 0.5rem; }
    .admin-metric-card .value { font-size: 1.75rem; font-weight: 700; color: #1a1a2e; }
    .admin-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; padding: 0.55rem 1.15rem; background: #252837; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; border: 1px solid #3d4358; cursor: pointer; font-family: inherit; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.12); }
    .admin-btn:hover { background: #353b52; border-color: #515a77; color: #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.18); transform: translateY(-1px); }
    .admin-btn:active { transform: translateY(0); box-shadow: 0 1px 2px rgba(0,0,0,0.12); }
    .admin-btn.sm { padding: 0.38rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border-radius: 6px; }
    .admin-btn.primary, a.admin-btn[href*="edit=new"], button.admin-btn[form="product-edit-form"] { background: #1e3a8a; border: 1px solid #3b82f6; color: #ffffff; }
    .admin-btn.primary:hover, a.admin-btn[href*="edit=new"]:hover, button.admin-btn[form="product-edit-form"]:hover { background: #2563eb; border-color: #60a5fa; color: #ffffff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35); }
    .admin-btn.btn-edit, a.admin-btn[href*="edit="]:not([href*="edit=new"]) { background: #232738; border: 1px solid #4a5472; color: #f1f5f9; }
    .admin-btn.btn-edit:hover, a.admin-btn[href*="edit="]:not([href*="edit=new"]):hover { background: #374161; border-color: #6b7a9f; color: #ffffff; }
    .admin-btn.ghost { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .admin-btn.ghost:hover { background: #f8fafc; color: #0f172a; border-color: #94a3b8; }
    .admin-btn.danger { background: #991b1b; border: 1px solid #dc2626; color: #ffffff; }
    .admin-btn.danger:hover { background: #b91c1c; border-color: #ef4444; color: #ffffff; box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25); }
    .admin-form-section { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; }
    .admin-form-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 1.5rem; }
    .admin-form-card h3 { margin: 0 0 1rem; font-size: 1rem; }
    .admin-form-card label { display: block; font-size: 0.8125rem; font-weight: 600; margin: 0.75rem 0 0.3rem; color: #555; }
    .admin-form-card input[type="text"], .admin-form-card input[type="number"], .admin-form-card input[type="url"], .admin-form-card select, .admin-form-card textarea { width: 100%; padding: 0.5rem 0.65rem; border: 1px solid #ddd; border-radius: 6px; font-size: 0.875rem; font-family: inherit; box-sizing: border-box; }
    .admin-form-card textarea { min-height: 80px; resize: vertical; font-family: inherit; }
    .admin-form-card .form-hint { font-size: 0.75rem; color: #aaa; margin-top: 0.2rem; }
    .admin-form-actions { display: flex; gap: 0.5rem; margin-top: 1.25rem; }
    .admin-product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    .admin-product-card { border: 1px solid #e0e0e0; border-radius: 10px; padding: 1rem; background: #fff; }
    .admin-product-card .prod-name { font-weight: 600; margin-bottom: 0.25rem; }
    .admin-product-card .prod-meta { font-size: 0.8125rem; color: #888; margin-bottom: 0.75rem; }
    .admin-product-card .prod-actions { display: flex; gap: 0.35rem; flex-wrap: wrap; }
    .admin-product-thumb { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: 0.75rem; background: #f5f5f5; display: block; }
    .admin-image-preview { border: 1px dashed #ccc; border-radius: 8px; background: #fafafa; min-height: 80px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin: 0.4rem 0 0.25rem; }
    .admin-image-preview img { max-width: 100%; max-height: 160px; object-fit: contain; display: block; }
    .admin-image-preview.is-broken::after { content: 'Preview unavailable — check the image URL/path.'; font-size: 0.75rem; color: #c0392b; padding: 0.6rem; text-align: center; }
    .site-content-editor { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .site-content-editor h3 { margin: 0 0 0.5rem; font-size: 1rem; }
    .site-content-editor .section-desc { font-size: 0.8125rem; color: #888; margin-bottom: 1rem; }
    .html-preview { background: #fafafa; border: 1px solid #eee; border-radius: 6px; padding: 1rem; margin: 0.75rem 0; font-size: 0.875rem; color: #666; max-height: 200px; overflow: auto; }
    .category-list { list-style: none; padding: 0; margin: 0; }
    .category-list li { display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.75rem; border-bottom: 1px solid #f0f0f0; }
    .category-list li:last-child { border-bottom: none; }
    .category-list .cat-name { font-weight: 500; }
    .category-list .cat-slug { color: #aaa; font-size: 0.8125rem; }
    .category-actions { display: flex; gap: 0.35rem; flex-wrap: wrap; }
    @media (max-width: 768px) {
      .admin-form-section { grid-template-columns: 1fr; }
      .admin-body { padding: 1rem; }
      .admin-login { margin: 2rem auto; }
    }
  </style>
</head>
<body>

<?php if (!$isAdmin): ?>
  <div class="admin-login">
    <h1>NOVA Admin</h1>
    <p class="sub"><?= $adminMode === 'register' ? 'Create a new admin account' : 'Sign in to manage your store' ?></p>
    <?php if ($loginError): ?>
      <div class="error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>
    <?php if ($loginSuccess): ?>
      <div class="admin-alert success"><?= htmlspecialchars($loginSuccess) ?></div>
    <?php endif; ?>
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
      <a href="admin.php?mode=login" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 600; <?= $adminMode !== 'register' ? 'background: #1a1a2e; color: #fff;' : 'background: #f5f5f5; color: #666;' ?>">Sign In</a>
      <a href="admin.php?mode=register" style="flex: 1; text-align: center; padding: 0.5rem; border-radius: 6px; text-decoration: none; font-size: 0.875rem; font-weight: 600; <?= $adminMode === 'register' ? 'background: #1a1a2e; color: #fff;' : 'background: #f5f5f5; color: #666;' ?>">Sign Up</a>
    </div>
    <form method="post">
      <?= nova_csrf_field() ?>
      <input type="hidden" name="mode" value="<?= htmlspecialchars($adminMode) ?>">
      <input type="hidden" name="action" value="<?= $adminMode === 'register' ? 'register' : 'login' ?>">
      <label>Admin Email</label>
      <input type="email" name="email" required placeholder="admin@nova.local" value="<?= htmlspecialchars($_POST['email'] ?? ($adminMode === 'register' ? '' : 'admin@nova.local')) ?>">
      <label>Password</label>
      <input type="password" name="password" required placeholder="Minimum 6 characters">
      <?php if ($adminMode === 'register'): ?>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required placeholder="Re-enter password">
      <?php endif; ?>
      <button type="submit" class="admin-btn" style="width: 100%; margin-top: 1.5rem; justify-content: center;"><?= $adminMode === 'register' ? 'Create Admin Account' : 'Sign In to Dashboard' ?></button>
    </form>
    <div style="margin-top: 1.5rem; text-align: center;">
      <a href="index.php" style="color: #666; font-size: 0.875rem; text-decoration: underline;">&larr; Back to storefront</a>
    </div>
  </div>
<?php else: ?>
  <nav class="admin-nav">
    <a href="admin.php" class="brand">NOVA Admin</a>
    <div class="nav-links">
      <a href="admin.php?tab=dashboard" class="<?= $activeTab==='dashboard'?'active':'' ?>">Dashboard</a>
      <a href="admin.php?tab=orders" class="<?= $activeTab==='orders'?'active':'' ?>">Orders</a>
      <a href="admin.php?tab=products" class="<?= $activeTab==='products'?'active':'' ?>">Products</a>
      <a href="admin.php?tab=customers" class="<?= $activeTab==='customers'?'active':'' ?>">Customers</a>
      <a href="admin.php?tab=site-content" class="<?= $activeTab==='site-content'?'active':'' ?>">Site Content</a>
      <a href="admin.php?tab=categories" class="<?= $activeTab==='categories'?'active':'' ?>">Categories</a>
    </div>
    <div class="user-info"><?= htmlspecialchars($_SESSION['nova_admin']['email']) ?> &middot; <a href="admin.php?logout=1">Logout</a></div>
  </nav>
  <main class="admin-body">
    <?php if ($flash): ?>
      <div class="admin-alert <?= $flash['type'] === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($activeTab === 'dashboard'): ?>
    <section class="admin-section">
      <div class="admin-section-heading"><div><p>Overview</p><h2>Dashboard</h2></div><a href="admin.php?tab=orders">View all orders</a></div>
      <div class="admin-card-grid">
        <div class="admin-metric-card"><div class="label">Revenue</div><div class="value">&#8377;<?= number_format($metrics['revenue'],0) ?></div><div class="sub">All-time, excl. cancelled</div></div>
        <div class="admin-metric-card"><div class="label">Orders</div><div class="value"><?= $metrics['orders'] ?></div><div class="sub">Total orders placed</div></div>
        <div class="admin-metric-card"><div class="label">Customers</div><div class="value"><?= $metrics['customers'] ?></div><div class="sub">Registered users</div></div>
        <div class="admin-metric-card"><div class="label">Products</div><div class="value"><?= $metrics['products'] ?></div><div class="sub"><?= $metrics['lowStock']>0 ? $metrics['lowStock']. ' low stock' : 'All well stocked' ?></div></div>
      </div>
      <?php if ($recentOrders): ?>
      <div class="admin-section-heading"><p>Recent</p><h2>Recent Orders</h2></div>
      <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead><tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr><td>#<?= (int)$o['id'] ?></td><td><?= htmlspecialchars($o['customer_name']) ?></td><td><?= (int)$o['ic'] ?></td><td>&#8377;<?= number_format((float)$o['total_amount'],2) ?></td><td><span class="admin-tag"><?= htmlspecialchars(ucfirst($o['status'])) ?></span></td><td class="admin-muted"><?= htmlspecialchars(date('d M Y',strtotime($o['created_at']))) ?></td></tr>
        <?php endforeach; ?>
      </tbody></table></div>
      <?php else: ?><p class="admin-muted" style="padding:1rem 0;">No orders yet.</p><?php endif; ?>
      <div class="admin-section-heading"><div><p>Catalog</p><h2>Recently Added &amp; Updated Products</h2></div><a href="admin.php?tab=products">Manage all products</a></div>
      <div class="admin-product-grid">
        <?php $dashProducts = array_slice($adminProducts, 0, 6); ?>
        <?php foreach ($dashProducts as $p): ?>
        <div class="admin-product-card">
          <img class="admin-product-thumb" src="<?= htmlspecialchars(nova_product_image_url($p['image'] ?? '')) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.style.opacity='0.25';">
          <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="prod-meta"><?= htmlspecialchars($p['category']) ?> &middot; &#8377;<?= number_format((float)$p['price'],2) ?>
            <?php if (!empty($p['badge'])): ?><span class="admin-tag"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
          </div>
          <div class="prod-meta">Stock: <strong class="<?= (int)$p['stock']<10?'admin-low':'admin-ok' ?>"><?= (int)$p['stock'] ?></strong>
            <?php if (!empty($p['updated_at'])): ?><span class="admin-muted">&middot; updated <?= htmlspecialchars(date('d M, H:i', strtotime($p['updated_at']))) ?></span>
            <?php elseif (!empty($p['created_at'])): ?><span class="admin-muted">&middot; added <?= htmlspecialchars(date('d M', strtotime($p['created_at']))) ?></span><?php endif; ?>
          </div>
          <div class="prod-actions">
            <a href="admin.php?tab=products&edit=<?= (int)$p['id'] ?>" class="admin-btn sm">Edit</a>
            <a href="shop.php" class="admin-btn sm ghost">Store</a>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$adminProducts): ?><p class="admin-muted" style="grid-column:1/-1;padding:1rem 0;">No products yet.</p><?php endif; ?>
      </div>
    </section>
    <?php elseif ($activeTab === 'products'): ?>
    <?php
      $allCatNames = array_map(fn($c) => (string)$c['name'], $categories);
      foreach ($adminProducts as $ap) {
        if (!empty($ap['category']) && !in_array((string)$ap['category'], $allCatNames, true)) {
          $allCatNames[] = (string)$ap['category'];
        }
      }
      sort($allCatNames);
    ?>
    <section class="admin-section">
      <div class="admin-section-heading">
        <div>
          <p>Catalog</p>
          <h2>Products (<?= count($adminProducts) ?>)</h2>
        </div>
        <div style="display:flex; gap:0.5rem; align-items:center;">
          <a href="admin.php?tab=products&edit=new#product-form-section" class="admin-btn sm primary">+ Add Product</a>
          <a href="shop.php" class="admin-btn sm ghost" target="_blank">View Storefront &rarr;</a>
        </div>
      </div>

      <?php if ($editProduct || $isAddingProduct): ?>
      <!-- Add / Edit Product Form -->
      <div id="product-form-section" style="background:#ffffff; border:2px solid <?= $editProduct ? '#3b82f6' : '#1e3a8a' ?>; border-radius:12px; padding:1.75rem; margin-bottom:2.5rem; box-shadow:0 6px 20px rgba(0,0,0,0.06); scroll-margin-top:2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb; padding-bottom:1rem; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
          <div>
            <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:<?= $editProduct ? '#2563eb' : '#059669' ?>;">
              <?= $editProduct ? 'EDITING CATALOG ITEM' : 'NEW CATALOG ITEM' ?>
            </span>
            <h2 style="margin:0.25rem 0 0; font-size:1.35rem; color:#0f172a;">
              <?= $editProduct ? 'Edit Product #' . $editProduct['id'] . ': ' . htmlspecialchars($editProduct['name']) : '+ Add New Product' ?>
            </h2>
          </div>
          <div style="display:flex; gap:0.5rem;">
            <a href="admin.php?tab=products" class="admin-btn sm ghost">&times; Close Form</a>
          </div>
        </div>

        <form method="post" id="product-edit-form" enctype="multipart/form-data">
          <?= nova_csrf_field() ?>
          <input type="hidden" name="action" value="save_product">
          <?php if ($editProduct): ?><input type="hidden" name="product_id" value="<?= (int)$editProduct['id'] ?>"><?php endif; ?>

          <div class="admin-form-section">
            <div class="admin-form-card">
              <h3>Basic Info</h3>
              <label>Product Name *</label>
              <input type="text" name="name" required placeholder="e.g. Test Premium Jacket" value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : '' ?>">

              <label>Category *</label>
              <select name="category" required>
                <option value="">Select category...</option>
                <?php foreach ($allCatNames as $catName): ?>
                <option value="<?= htmlspecialchars($catName) ?>"<?= ($editProduct && strcasecmp($editProduct['category'], $catName) === 0) ? ' selected' : '' ?>><?= htmlspecialchars($catName) ?></option>
                <?php endforeach; ?>
              </select>

              <label>Price (INR ₹) *</label>
              <input type="number" name="price" min="0.01" step="0.01" required placeholder="e.g. 2499.00" value="<?= $editProduct ? number_format((float)$editProduct['price'], 2, '.', '') : '' ?>">

              <label>Original Price (INR ₹)</label>
              <input type="number" name="original_price" min="0" step="0.01" placeholder="e.g. 2999.00 (optional)" value="<?= $editProduct && $editProduct['original_price'] ? number_format((float)$editProduct['original_price'], 2, '.', '') : '' ?>">
              <p class="form-hint">Set original price to auto-calculate discount %.</p>

              <label>Rating (0.00 - 5.00)</label>
              <input type="number" name="rating" min="0" max="5" step="0.01" value="<?= $editProduct ? number_format((float)$editProduct['rating'], 2, '.', '') : '0.00' ?>">

              <label>Review Count</label>
              <input type="number" name="reviews" min="0" step="1" value="<?= $editProduct ? (int)$editProduct['reviews'] : '0' ?>">
            </div>

            <div class="admin-form-card">
              <h3>Images & Inventory</h3>
              <label>Primary Image URL or Path (optional)</label>
              <div class="admin-image-preview" id="preview-wrap-primary"><img id="preview-primary" alt="Primary image preview"<?= $editProduct ? ' src="' . htmlspecialchars(nova_product_image_url($editProduct['image'] ?? '')) . '"' : ' src="images/placeholder.svg"' ?>></div>
              <input type="text" name="image" id="input-image" value="<?= $editProduct ? htmlspecialchars($editProduct['image']) : '' ?>" placeholder="https://images.unsplash.com/... or images/jacket.jpg">
              <p class="form-hint">Optional — leave empty to auto-use the placeholder, or upload a file below instead. Preview updates as you type.</p>

              <label>…or Upload an Image File</label>
              <input type="file" name="image_file" id="input-image-file" accept="image/png, image/jpeg, image/webp, image/gif">
              <p class="form-hint">Uploading a file overrides the URL/path above. JPG, PNG, WebP or GIF, max 4&nbsp;MB. Saved to <code>uploads/</code>.</p>

              <label>Secondary Image URL (Hover)</label>
              <div class="admin-image-preview" id="preview-wrap-secondary"><img id="preview-secondary" alt="Secondary image preview"<?= $editProduct && $editProduct['secondary_image'] ? ' src="' . htmlspecialchars(nova_product_image_url($editProduct['secondary_image'])) . '"' : '' ?>></div>
              <input type="text" name="secondary_image" id="input-secondary-image" value="<?= $editProduct && $editProduct['secondary_image'] ? htmlspecialchars($editProduct['secondary_image']) : '' ?>" placeholder="https://images.unsplash.com/... (optional)">

              <label>Stock Quantity *</label>
              <input type="number" name="stock" min="0" step="1" required placeholder="e.g. 10" value="<?= $editProduct ? (int)$editProduct['stock'] : '10' ?>">

              <label>Badge (optional)</label>
              <input type="text" name="badge" placeholder="e.g. New, Sale, Trending, Limited" value="<?= $editProduct && $editProduct['badge'] ? htmlspecialchars($editProduct['badge']) : '' ?>">

              <label style="margin-top:1rem; cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
                <input type="checkbox" name="featured" value="1"<?= $editProduct && !empty($editProduct['featured']) ? ' checked' : '' ?>>
                <strong>Featured on Homepage</strong>
              </label>
            </div>

            <div class="admin-form-card">
              <h3>Description</h3>
              <textarea name="description" placeholder="Describe this product in detail..."><?= $editProduct && $editProduct['description'] ? htmlspecialchars($editProduct['description']) : '' ?></textarea>
            </div>

            <div class="admin-form-card">
              <h3>Variants</h3>
              <label>Colors</label>
              <input type="text" name="colors" value="<?= $editProduct ? htmlspecialchars(json_encode(dColors($editProduct['colors_json']), JSON_UNESCAPED_SLASHES)) : '' ?>" placeholder='Black, White, Blue OR ["#000000","#ffffff"]'>
              <p class="form-hint">Comma-separated names or JSON array: Black, White, Olive</p>

              <label>Sizes</label>
              <input type="text" name="sizes" value="<?= $editProduct ? htmlspecialchars(json_encode(dSizes($editProduct['sizes_json']), JSON_UNESCAPED_SLASHES)) : '' ?>" placeholder='S, M, L, XL OR ["S","M","L"]'>
              <p class="form-hint">Comma-separated or JSON array: S, M, L, XL</p>
            </div>
          </div>

          <div class="admin-form-actions" style="margin-top:1.5rem; display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
            <button type="submit" class="admin-btn primary"><?= $editProduct ? 'Save Changes' : '+ Add Product' ?></button>
            <a href="admin.php?tab=products" class="admin-btn ghost">Cancel</a>
          </div>
        </form>

        <?php if ($editProduct): ?>
        <form method="post" style="margin-top:0.75rem;" onsubmit="return confirm('Permanently delete this product?');">
          <?= nova_csrf_field() ?>
          <input type="hidden" name="action" value="delete_product">
          <input type="hidden" name="product_id" value="<?= (int)$editProduct['id'] ?>">
          <button type="submit" class="admin-btn danger">Delete Product</button>
        </form>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Products Grid -->
      <div class="admin-product-grid">
        <?php foreach ($adminProducts as $p): ?>
        <div class="admin-product-card">
          <img class="admin-product-thumb" src="<?= htmlspecialchars(nova_product_image_url($p['image'] ?? '')) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='https://placehold.co/400x300?text=No+Image';">
          <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="prod-meta">
            <strong><?= htmlspecialchars($p['category']) ?></strong> &middot; &#8377;<?= number_format((float)$p['price'], 2) ?>
            <?php if (!empty($p['original_price']) && $p['original_price'] > $p['price']): ?>
              <span class="admin-muted" style="text-decoration:line-through;">&#8377;<?= number_format((float)$p['original_price'], 2) ?></span>
            <?php endif; ?>
            <?php if (!empty($p['badge'])): ?><span class="admin-tag"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
            <?php if (!empty($p['featured'])): ?><span class="admin-tag" style="background:#1e3a8a;color:#fff;">Featured</span><?php endif; ?>
          </div>
          <div class="prod-meta">
            Stock: <strong class="<?= (int)$p['stock'] < 10 ? 'admin-low' : 'admin-ok' ?>"><?= (int)$p['stock'] ?></strong>
            &middot; <?= (int)$p['reviews'] ?> reviews &middot; &#9733;<?= number_format((float)$p['rating'], 1) ?>
          </div>
          <div class="prod-actions" style="margin-top:0.75rem;">
            <a href="admin.php?tab=products&edit=<?= (int)$p['id'] ?>#product-form-section" class="admin-btn sm btn-edit">Edit</a>
            <a href="product.php?id=<?= (int)$p['id'] ?>" class="admin-btn sm ghost" target="_blank" title="View product on customer website">View</a>
            <form method="post" style="display:inline;" onsubmit="return confirm('Delete product <?= htmlspecialchars(addslashes($p['name'])) ?> (#<?= (int)$p['id'] ?>)?');">
              <?= nova_csrf_field() ?>
              <input type="hidden" name="action" value="delete_product">
              <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="admin-btn sm danger">Delete</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$adminProducts): ?><p class="admin-muted" style="grid-column:1/-1;padding:1rem 0;">No products yet. Click "+ Add Product" above to create one.</p><?php endif; ?>
      </div>
    </section>
    <script>
      (function () {
        function wirePreview(inputId, imgId, wrapId) {
          var input = document.getElementById(inputId);
          var img = document.getElementById(imgId);
          var wrap = document.getElementById(wrapId);
          if (!input || !img || !wrap) return;
          function sync() {
            var v = input.value.trim();
            if (v) { img.src = v; } else { img.removeAttribute('src'); wrap.classList.remove('is-broken'); }
          }
          img.addEventListener('load', function () { wrap.classList.remove('is-broken'); });
          img.addEventListener('error', function () { if (img.getAttribute('src')) { wrap.classList.add('is-broken'); } });
          input.addEventListener('input', sync);
          sync();
        }
        wirePreview('input-image', 'preview-primary', 'preview-wrap-primary');
        wirePreview('input-secondary-image', 'preview-secondary', 'preview-wrap-secondary');
        /* Local file preview for uploads */
        var fileInput = document.getElementById('input-image-file');
        if (fileInput) {
          fileInput.addEventListener('change', function () {
            var img = document.getElementById('preview-primary');
            var wrap = document.getElementById('preview-wrap-primary');
            if (!fileInput.files || !fileInput.files[0] || !img || !wrap) return;
            var url = URL.createObjectURL(fileInput.files[0]);
            img.src = url;
            wrap.classList.remove('is-broken');
          });
        }
      })();
    </script>

    <?php elseif ($activeTab === 'orders'): ?>
    <section class="admin-section">
      <div class="admin-section-heading"><div><p>Management</p><h2>All Orders (<?= count($allOrders) ?>)</h2></div><a href="admin.php?tab=dashboard">Dashboard</a></div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($allOrders as $o): ?>
            <tr>
              <td>#<?= (int)$o['id'] ?></td>
              <td><?= htmlspecialchars($o['customer_name']) ?></td>
              <td><?= htmlspecialchars($o['phone'] ?? '-') ?></td>
              <td>&#8377;<?= number_format((float)$o['total_amount'], 2) ?></td>
              <td><span class="admin-tag"><?= htmlspecialchars(ucfirst($o['status'])) ?></span></td>
              <td class="admin-muted"><?= htmlspecialchars(date('d M Y, H:i', strtotime($o['created_at']))) ?></td>
              <td>
                <form method="post" class="admin-inline-form">
                  <?= nova_csrf_field() ?>
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                  <select name="status" style="padding: 0.25rem 0.5rem; border-radius: 4px; border: 1px solid #ddd; font-size: 0.8125rem;">
                    <?php foreach (NOVA_ORDER_STATUSES as $st): ?>
                    <option value="<?= $st ?>"<?= $o['status'] === $st ? ' selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="admin-save-btn">Update</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$allOrders): ?><tr><td colspan="7" class="admin-muted">No orders found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <?php elseif ($activeTab === 'customers'): ?>
    <section class="admin-section">
      <div class="admin-section-heading"><div><p>Audience</p><h2>Registered Customers (<?= count($customers) ?>)</h2></div></div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Orders</th><th>Lifetime Value</th><th>Joined</th></tr></thead>
          <tbody>
            <?php foreach ($customers as $c): ?>
            <tr>
              <td>#<?= (int)$c['id'] ?></td>
              <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td><?= (int)$c['oc'] ?></td>
              <td>&#8377;<?= number_format((float)$c['lv'], 2) ?></td>
              <td class="admin-muted"><?= htmlspecialchars(date('d M Y', strtotime($c['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$customers): ?><tr><td colspan="6" class="admin-muted">No registered customers yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <?php elseif ($activeTab === 'site-content'): ?>
    <section class="admin-section">
      <div class="admin-section-heading"><div><p>CMS</p><h2>Site Content Sections</h2></div></div>
      <?php foreach ($siteContentSections as $sc): ?>
      <div class="site-content-editor">
        <h3>Section: <?= htmlspecialchars($sc['section']) ?></h3>
        <p class="section-desc">Last updated: <?= htmlspecialchars($sc['updated_at'] ?? 'N/A') ?></p>
        <form method="post">
          <?= nova_csrf_field() ?>
          <input type="hidden" name="action" value="save_site_content">
          <input type="hidden" name="section" value="<?= htmlspecialchars($sc['section']) ?>">
          <textarea name="html_content" style="width: 100%; min-height: 120px; font-family: monospace; font-size: 0.85rem; padding: 0.5rem;"><?= htmlspecialchars($sc['html_content']) ?></textarea>
          <div style="margin-top: 0.5rem;"><button type="submit" class="admin-btn sm">Save Section</button></div>
        </form>
      </div>
      <?php endforeach; ?>
      <?php if (!$siteContentSections): ?>
      <div class="site-content-editor">
        <h3>Add New Content Section</h3>
        <form method="post">
          <?= nova_csrf_field() ?>
          <input type="hidden" name="action" value="save_site_content">
          <label style="display:block;margin-bottom:0.5rem;">Section Name (e.g. hero_tagline, promo_banner)</label>
          <input type="text" name="section" required style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem;">
          <label style="display:block;margin-bottom:0.5rem;">HTML Content</label>
          <textarea name="html_content" required style="width: 100%; min-height: 120px; font-family: monospace; font-size: 0.85rem; padding: 0.5rem;"></textarea>
          <div style="margin-top: 0.5rem;"><button type="submit" class="admin-btn sm">Create Section</button></div>
        </form>
      </div>
      <?php endif; ?>
    </section>

    <?php elseif ($activeTab === 'categories'): ?>
    <section class="admin-section">
      <div class="admin-section-heading"><div><p>Taxonomy</p><h2>Categories (<?= count($categories) ?>)</h2></div></div>
      <div class="admin-form-section">
        <div class="admin-form-card">
          <h3>Existing Categories</h3>
          <ul class="category-list">
            <?php foreach ($categories as $cat): ?>
            <li>
              <div>
                <span class="cat-name"><?= htmlspecialchars($cat['name']) ?></span>
                <span class="cat-slug">/<?= htmlspecialchars($cat['slug']) ?></span>
              </div>
              <div class="category-actions">
                <form method="post" onsubmit="return confirm('Delete category?');" style="display:inline;">
                  <?= nova_csrf_field() ?>
                  <input type="hidden" name="action" value="delete_category">
                  <input type="hidden" name="category_id" value="<?= (int)$cat['id'] ?>">
                  <button type="submit" class="admin-btn sm danger">Delete</button>
                </form>
              </div>
            </li>
            <?php endforeach; ?>
            <?php if (!$categories): ?><li class="admin-muted">No custom categories defined.</li><?php endif; ?>
          </ul>
        </div>
        <div class="admin-form-card">
          <h3>Add Category</h3>
          <form method="post">
            <?= nova_csrf_field() ?>
            <input type="hidden" name="action" value="save_category">
            <label>Category Name *</label>
            <input type="text" name="name" required placeholder="e.g. Outerwear">
            <label>Slug *</label>
            <input type="text" name="slug" required placeholder="e.g. outerwear">
            <label>Description</label>
            <textarea name="description" placeholder="Brief category description..."></textarea>
            <label>Sort Order</label>
            <input type="number" name="sort_order" value="0" min="0">
            <div style="margin-top: 1rem;"><button type="submit" class="admin-btn sm">Add Category</button></div>
          </form>
        </div>
      </div>
    </section>
    <?php endif; ?>
  </main>
<?php endif; ?>
</body>
</html>


