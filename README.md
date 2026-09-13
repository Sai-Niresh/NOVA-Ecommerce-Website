# NOVA — Premium Fashion E-Commerce

> Phase 1: Professional Frontend Foundation

## Project Overview

**NOVA** is a premium fashion and lifestyle e-commerce platform targeting modern online shoppers. This repository contains the Phase 1 frontend — a fully responsive, animated, and accessible homepage built to a commercial quality standard.

---

## Technology Stack

| Layer | Technology |
|---|---|
| Markup | HTML5 / PHP 8+ (includes) |
| Styling | CSS3 + Tailwind CSS Play CDN |
| Animations | GSAP 3.12 + ScrollTrigger |
| 3D Accent | Three.js r128 |
| Icons | Lucide Icons |
| Fonts | Plus Jakarta Sans (Google Fonts) |
| Server | XAMPP (Apache + PHP) |

---

## Project Structure

```
NOVA/
├── assets/
│   ├── images/         # Local image assets (future)
│   ├── icons/          # Custom SVG icons (future)
│   └── fonts/          # Local font files (future)
├── css/
│   ├── style.css       # Design tokens, base, components
│   └── animations.css  # Keyframes, GSAP helpers
├── js/
│   ├── main.js         # Navigation, mobile drawer, header scroll
│   ├── animations.js   # GSAP timelines, ScrollTrigger, Three.js
│   └── interactions.js # Cursor, magnetic btn, wishlist, newsletter
├── includes/
│   ├── header.php      # Sticky header + mobile nav
│   └── footer.php      # Full footer
├── config/
│   └── constants.php   # Brand-level constants
├── index.php           # Homepage
└── README.md
```

---

## Running Locally

### Prerequisites
- XAMPP installed (Apache + PHP 8.0+)

### Steps

1. Place the `Nova -Ecommerce` folder in your XAMPP `htdocs` directory:
   ```
   C:/xampp/htdocs/nova/
   ```
2. Start XAMPP → Start **Apache**
3. Open your browser:
   ```
   http://localhost/nova/
   ```

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
- **H1**: 72px / 700 weight
- **H2**: 48px / 700 weight
- **H3**: 24px / 600 weight
- **Body**: 16px / 400 weight

### Breakpoints
| Name | Width |
|---|---|
| Mobile S | 375px |
| Mobile M | 425px |
| Tablet | 768px |
| Laptop | 1024px |
| Desktop | 1440px |
| Wide | 1920px |

---

## Phase Roadmap

| Phase | Scope | Status |
|---|---|---|
| **Phase 1** | Frontend Foundation + Homepage | ✅ Complete |
| Phase 2 | Auth, Product Catalog, MySQL | 🔜 Planned |
| Phase 3 | Cart, Checkout, Orders | 🔜 Planned |
| Phase 4 | Admin Dashboard | 🔜 Planned |

---

## License

Development / Portfolio use. All imagery sourced from Unsplash (free to use).
