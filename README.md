# NOVA — Premium Fashion E-Commerce

> Complete PHP / MySQL e-commerce website — storefront, checkout, order management, and admin dashboard.

## Project Overview

**NOVA** is a full-featured fashion & lifestyle e-commerce platform: homepage, shop with search/filters, product detail pages, cart & wishlist, guest/user checkout, order tracking, and a secure admin dashboard — built with plain PHP 8 + MySQL (no frameworks, no build step).

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8+ (PDO, prepared statements) |
| Database | MySQL / MariaDB (auto-bootstrapped schema) |
| Frontend | HTML5 + CSS3 (custom design system) |
| Animations | GSAP 3.12.5 + ScrollTrigger, Three.js r128 (hero particles) |
| Icons | Inline SVG |
| Fonts | Plus Jakarta Sans (Google Fonts) |
| Server | XAMPP (Apache) or PHP built-in server |

---

## Project Structure

```
NOVA-Ecommerce/
├── index.php            # Homepage (hero, categories, featured, promo, brand story)
├── shop.php             # Shop: search, filters (category/price/size/color), sort, load-more
├── category.php         # Category listing
├── product.php          # Product detail: gallery, variants, related products
├── cart.php             # Cart page (localStorage-backed cart)
├── checkout.php         # Delivery details + payment method
├── orders.php           # JSON API: place order / fetch order history
├── orders-page.php      # Customer order history page
├── auth.php             # JSON API: register / login / reset / logout
├── admin.php            # Admin dashboard: metrics, orders, products, customers
├── config/
│   ├── database.php     # PDO connection + self-bootstrapping schema
│   ├── bootstrap.php    # Hardened session + CSRF helpers
│   └── constants.php    # Brand constants
├── includes/
│   ├── header.php       # Header, nav, modals, cursor/progress elements
│   ├── footer.php       # Footer
│   ├── product-card.php # Shared product card renderer
│   └── products-data.php# Catalog source of truth + DB sync
├── css/                 # style.css (design system) + animations.css
├── js/                  # main, animations, interactions, shop, product, cart, checkout
├── .htaccess            # Security headers, compression, caching (Apache)
└── robots.txt
```

---

## Running Locally

### Prerequisites
- XAMPP (PHP 8.0+ and MySQL running). The database `nova_db` is created automatically on first request, seeded with 30 products and an admin account.

### Option A — Apache
1. Place the folder in `C:/xampp/htdocs/NOVA-Ecommerce`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/NOVA-Ecommerce/`.

### Option B — PHP built-in server
```
C:\xampp\php\php.exe -S localhost:8000 -t C:\xampp\htdocs\NOVA-Ecommerce
```
Then open `http://localhost:8000/`.

### Admin dashboard
`http://localhost/NOVA-Ecommerce/admin.php` — demo credentials: **admin@nova.local / admin123** (override via `NOVA_ADMIN_EMAIL` / `NOVA_ADMIN_PASSWORD` env vars). Database credentials can be overridden with `NOVA_DB_HOST`, `NOVA_DB_NAME`, `NOVA_DB_USER`, `NOVA_DB_PASSWORD`.

---

## Security

- All SQL access uses **prepared statements** (PDO, native prepares).
- **CSRF tokens** on all admin forms (login, order status, product updates).
- **Hardened sessions**: HttpOnly, SameSite=Lax, Secure over HTTPS; ID regeneration on login/register.
- **Server-side pricing**: checkout re-prices every item from the database and validates stock inside a transaction — client cart values are never trusted.
- **Stock integrity**: quantities are decremented atomically with a `stock >= qty` guard.
- **Login throttling**: per-account/per-session lockout after 5 failed attempts (10-minute window).
- **Password reset** requires verifying the current password.
- Passwords stored with `password_hash()` (bcrypt).

---

## Design System

### Colors
| Token | Value | Usage |
|---|---|---|
| `--nova-black` | `#0a0a0a` | Primary text, buttons |
| `--nova-white` | `#fafafa` | Backgrounds |
| `--nova-accent` | `#c8a96e` | Gold accent (minimal) |
| `--nova-gray-200` | `#e4e4e4` | Borders |
| `--nova-gray-600` | `#606060` | Muted text |

### Typography
- **Font**: Plus Jakarta Sans
- **H1**: 72px / 700 weight · **H2**: 48px / 700 · **H3**: 24px / 600 · **Body**: 16px / 400

### Breakpoints
Mobile S 375px · Tablet 768px · Laptop 1024px · Desktop 1440px · Wide 1920px

---

## Feature Status

| Phase | Scope | Status |
|---|---|---|
| 1 | Design System + Homepage | ✅ Complete |
| 2 | Shop + Products + Search + Filters | ✅ Complete |
| 3 | Product Details | ✅ Complete |
| 4 | Login + Registration + Authentication | ✅ Complete |
| 5 | Cart + Wishlist | ✅ Complete |
| 6 | Checkout + Payment + Orders | ✅ Complete |
| 7 | Admin Dashboard | ✅ Complete |
| 8 | MySQL + Backend Integration | ✅ Complete |
| 9 | Advanced Animation + 3D + UX Polish | ✅ Complete |
| 10 | Testing + Security + Performance + Deployment | ✅ Complete |

---

## License

Development / Portfolio use. All imagery sourced from Unsplash (free to use).
