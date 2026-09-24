<?php
/**
 * NOVA E-Commerce — Products Data
 * Comprehensive product catalog for Phase 2
 */

if (!defined('NOVA_PRODUCTS_DATA_LOADED')) {
  define('NOVA_PRODUCTS_DATA_LOADED', true);

  $NOVA_PRODUCTS = [
    // ── MEN ────────────────────────────────────────────────
    [
      'id'             => 1,
      'name'           => 'Essential Oversized Tee',
      'category'       => 'Men',
      'price'          => 1299,
      'original_price' => 1999,
      'discount'       => 35,
      'image'          => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black', 'hex' => '#0a0a0a'],
        ['name' => 'White', 'hex' => '#ffffff'],
        ['name' => 'Olive', 'hex' => '#4a5d4e'],
      ],
      'sizes'          => ['S', 'M', 'L', 'XL', 'XXL'],
      'stock'          => 45,
      'description'    => 'Crafted from 240 GSM organic combed cotton, the Essential Oversized Tee offers a relaxed silhouette with dropped shoulders and a durable ribbed neckline. Designed for modern everyday luxury.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-10',
      'featured'       => true,
    ],
    [
      'id'             => 2,
      'name'           => 'Structured Tailored Blazer',
      'category'       => 'Men',
      'price'          => 7499,
      'original_price' => 9999,
      'discount'       => 25,
      'image'          => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Charcoal', 'hex' => '#333333'],
        ['name' => 'Navy', 'hex' => '#1b263b'],
      ],
      'sizes'          => ['M', 'L', 'XL'],
      'stock'          => 18,
      'description'    => 'A sharp single-breasted blazer constructed from premium wool blend fabric. Features narrow notch lapels, dual back vents, and soft shoulder padding for effortless formal polish.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-25',
      'featured'       => true,
    ],
    [
      'id'             => 3,
      'name'           => 'Minimalist Wool Trench Coat',
      'category'       => 'Men',
      'price'          => 11999,
      'original_price' => 14999,
      'discount'       => 20,
      'image'          => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Beige', 'hex' => '#d4c5b9'],
        ['name' => 'Black', 'hex' => '#0a0a0a'],
      ],
      'sizes'          => ['S', 'M', 'L', 'XL'],
      'stock'          => 12,
      'description'    => 'Timeless double-breasted trench coat tailored from water-repellent wool blend fabric. Finished with horn buttons, a belt with a matte buckle, and a satin lining.',
      'badge'          => 'Limited',
      'created_at'     => '2026-09-01',
      'featured'       => true,
    ],
    [
      'id'             => 4,
      'name'           => 'Raw Denim Slim Tapered Jeans',
      'category'       => 'Men',
      'price'          => 3499,
      'original_price' => 4499,
      'discount'       => 22,
      'image'          => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1542272604-780c36856542?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Indigo', 'hex' => '#1a2a3a'],
        ['name' => 'Washed Black', 'hex' => '#2b2b2b'],
      ],
      'sizes'          => ['S', 'M', 'L', 'XL'],
      'stock'          => 30,
      'description'    => '14oz Japanese selvedge denim woven with a touch of stretch for mobility. Cut in a modern slim tapered fit that ages uniquely with every wear.',
      'badge'          => null,
      'created_at'     => '2026-07-18',
      'featured'       => false,
    ],
    [
      'id'             => 5,
      'name'           => 'Heavyweight Fleece Hoodie',
      'category'       => 'Men',
      'price'          => 2999,
      'original_price' => 3999,
      'discount'       => 25,
      'image'          => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black', 'hex' => '#0a0a0a'],
        ['name' => 'Heather Grey', 'hex' => '#999999'],
        ['name' => 'Beige', 'hex' => '#c2b280'],
      ],
      'sizes'          => ['S', 'M', 'L', 'XL', 'XXL'],
      'stock'          => 50,
      'description'    => 'Ultra-soft 450 GSM French terry hoodie with a double-layered hood, pouch pocket, and seamless cuff ribbing. Built to maintain shape wash after wash.',
      'badge'          => 'New',
      'created_at'     => '2026-09-05',
      'featured'       => false,
    ],

    // ── WOMEN ──────────────────────────────────────────────
    [
      'id'             => 6,
      'name'           => 'Silk Blend Wrap Midi Dress',
      'category'       => 'Women',
      'price'          => 5499,
      'original_price' => 7499,
      'discount'       => 27,
      'image'          => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Emerald Green', 'hex' => '#0f5257'],
        ['name' => 'Midnight Black', 'hex' => '#0a0a0a'],
        ['name' => 'Blush Pink', 'hex' => '#e8c5c8'],
      ],
      'sizes'          => ['XS', 'S', 'M', 'L'],
      'stock'          => 22,
      'description'    => 'Elegantly draped silk blend wrap dress featuring a graceful V-neckline, waist tie belt, and flutter sleeves. Perfect for evening soirées and formal gatherings.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-20',
      'featured'       => true,
    ],
    [
      'id'             => 7,
      'name'           => 'Oversized Cashmere Knit Sweater',
      'category'       => 'Women',
      'price'          => 6999,
      'original_price' => 8999,
      'discount'       => 22,
      'image'          => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1584273143981-41c073dfe8f8?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Cream', 'hex' => '#f5f2eb'],
        ['name' => 'Camel', 'hex' => '#c19a6b'],
        ['name' => 'Soft Grey', 'hex' => '#d3d3d3'],
      ],
      'sizes'          => ['S', 'M', 'L'],
      'stock'          => 15,
      'description'    => 'Sumptuously soft 100% grade-A cashmere sweater knit with a wide rib texture, dropped shoulders, and a cozy crew collar.',
      'badge'          => 'New',
      'created_at'     => '2026-09-02',
      'featured'       => true,
    ],
    [
      'id'             => 8,
      'name'           => 'High-Waisted Tailored Trousers',
      'category'       => 'Women',
      'price'          => 3999,
      'original_price' => 4999,
      'discount'       => 20,
      'image'          => 'https://images.unsplash.com/photo-1601925260368-9a770e320c7c?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black', 'hex' => '#0a0a0a'],
        ['name' => 'Beige', 'hex' => '#d5c4a1'],
        ['name' => 'Olive', 'hex' => '#556b2f'],
      ],
      'sizes'          => ['XS', 'S', 'M', 'L', 'XL'],
      'stock'          => 28,
      'description'    => 'Fluid wide-leg trousers designed with front pleats, a high rise waistband, and concealed hook closure for a lengthened, polished silhouette.',
      'badge'          => null,
      'created_at'     => '2026-07-28',
      'featured'       => false,
    ],
    [
      'id'             => 9,
      'name'           => 'Linen Button-Down Boyfriend Shirt',
      'category'       => 'Women',
      'price'          => 2499,
      'original_price' => 3299,
      'discount'       => 24,
      'image'          => 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Pure White', 'hex' => '#ffffff'],
        ['name' => 'Sky Blue', 'hex' => '#87ceeb'],
      ],
      'sizes'          => ['S', 'M', 'L'],
      'stock'          => 35,
      'description'    => 'Pre-washed French flax linen shirt styled in a relaxed boyfriend fit with shell buttons and a crisp point collar.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-15',
      'featured'       => false,
    ],
    [
      'id'             => 10,
      'name'           => 'Sculpted Leather Biker Jacket',
      'category'       => 'Women',
      'price'          => 12999,
      'original_price' => 16999,
      'discount'       => 23,
      'image'          => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1520591799316-6b30425429aa?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black', 'hex' => '#0a0a0a'],
        ['name' => 'Dark Cognac', 'hex' => '#5c2c16'],
      ],
      'sizes'          => ['XS', 'S', 'M', 'L'],
      'stock'          => 8,
      'description'    => 'Handcrafted from butter-soft lambskin leather with silver hardware, asymmetric front zip, and quilted shoulder panels.',
      'badge'          => 'Limited',
      'created_at'     => '2026-09-04',
      'featured'       => true,
    ],

    // ── SHOES ──────────────────────────────────────────────
    [
      'id'             => 11,
      'name'           => 'Aero Runner Minimalist Sneakers',
      'category'       => 'Shoes',
      'price'          => 4999,
      'original_price' => 6999,
      'discount'       => 28,
      'image'          => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Red / White', 'hex' => '#dc2626'],
        ['name' => 'Triple White', 'hex' => '#ffffff'],
        ['name' => 'Matte Black', 'hex' => '#111111'],
      ],
      'sizes'          => ['UK 7', 'UK 8', 'UK 9', 'UK 10', 'UK 11'],
      'stock'          => 40,
      'description'    => 'Engineered mesh upper combined with lightweight responsive EVA cushioning. Designed for city commuting and sleek athletic styling.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-01',
      'featured'       => true,
    ],
    [
      'id'             => 12,
      'name'           => 'Italian Leather Chelsea Boots',
      'category'       => 'Shoes',
      'price'          => 8999,
      'original_price' => 11999,
      'discount'       => 25,
      'image'          => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Tan Leather', 'hex' => '#a0522d'],
        ['name' => 'Deep Black', 'hex' => '#0a0a0a'],
      ],
      'sizes'          => ['UK 7', 'UK 8', 'UK 9', 'UK 10'],
      'stock'          => 16,
      'description'    => 'Hand-finished Tuscan calfskin Chelsea boots with elasticated side gussets, pull tabs, and durable stacked leather soles with rubber inserts.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-18',
      'featured'       => true,
    ],
    [
      'id'             => 13,
      'name'           => 'Heritage Penny Loafers',
      'category'       => 'Shoes',
      'price'          => 6499,
      'original_price' => 8499,
      'discount'       => 23,
      'image'          => 'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Dark Mahogany', 'hex' => '#4a2c11'],
        ['name' => 'Black Waxed', 'hex' => '#0a0a0a'],
      ],
      'sizes'          => ['UK 7', 'UK 8', 'UK 9', 'UK 10'],
      'stock'          => 20,
      'description'    => 'Goodyear welted penny loafers crafted from full-grain burnished leather with cushioned leather insoles.',
      'badge'          => null,
      'created_at'     => '2026-07-20',
      'featured'       => false,
    ],
    [
      'id'             => 14,
      'name'           => 'Retro Leather Court Trainers',
      'category'       => 'Shoes',
      'price'          => 4299,
      'original_price' => 5499,
      'discount'       => 21,
      'image'          => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Off-White', 'hex' => '#f4f1ea'],
        ['name' => 'White / Navy', 'hex' => '#1e3a8a'],
      ],
      'sizes'          => ['UK 6', 'UK 7', 'UK 8', 'UK 9', 'UK 10'],
      'stock'          => 32,
      'description'    => 'Classic 80s court silhouette crafted with soft leather overlays, padded ankle collar, and vulcanized rubber sole.',
      'badge'          => 'New',
      'created_at'     => '2026-09-06',
      'featured'       => false,
    ],
    [
      'id'             => 15,
      'name'           => 'Velvet Evening Tuxedo Slip-Ons',
      'category'       => 'Shoes',
      'price'          => 7999,
      'original_price' => 9999,
      'discount'       => 20,
      'image'          => 'https://images.unsplash.com/photo-1562183241-b937e95585b6?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black Velvet', 'hex' => '#0a0a0a'],
        ['name' => 'Midnight Blue', 'hex' => '#0d1b2a'],
      ],
      'sizes'          => ['UK 7', 'UK 8', 'UK 9', 'UK 10'],
      'stock'          => 10,
      'description'    => 'Luxurious plush velvet loafers featuring quilted satin linings and hand-grosgrain trim for formal black-tie events.',
      'badge'          => 'Limited',
      'created_at'     => '2026-09-03',
      'featured'       => false,
    ],

    // ── WATCHES ────────────────────────────────────────────
    [
      'id'             => 16,
      'name'           => 'Minimal Chronograph Matte Watch',
      'category'       => 'Watches',
      'price'          => 8499,
      'original_price' => 11999,
      'discount'       => 29,
      'image'          => 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Matte Black', 'hex' => '#181818'],
        ['name' => 'Silver / White', 'hex' => '#e0e0e0'],
      ],
      'sizes'          => ['40mm', '42mm'],
      'stock'          => 25,
      'description'    => 'Sleek Japanese quartz chronograph movement enclosed in a 316L surgical stainless steel case with anti-reflective sapphire glass.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-12',
      'featured'       => true,
    ],
    [
      'id'             => 17,
      'name'           => 'Atelier Automatic Open-Heart Watch',
      'category'       => 'Watches',
      'price'          => 14999,
      'original_price' => 18999,
      'discount'       => 21,
      'image'          => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1539185441755-769473a23570?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Brushed Silver', 'hex' => '#c0c0c0'],
        ['name' => 'Rose Gold Accent', 'hex' => '#b76e79'],
      ],
      'sizes'          => ['41mm'],
      'stock'          => 14,
      'description'    => 'Self-winding mechanical automatic timepiece with 42-hour power reserve, skeletonized open-heart window, and exhibition case back.',
      'badge'          => 'Limited',
      'created_at'     => '2026-08-28',
      'featured'       => true,
    ],
    [
      'id'             => 18,
      'name'           => 'Vintage Heritage Leather Watch',
      'category'       => 'Watches',
      'price'          => 6299,
      'original_price' => 7999,
      'discount'       => 21,
      'image'          => 'https://images.unsplash.com/photo-1508057198894-247b23fe5ade?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Saddle Brown', 'hex' => '#8b4513'],
        ['name' => 'Dark Cognac', 'hex' => '#5c2c16'],
      ],
      'sizes'          => ['38mm', '40mm'],
      'stock'          => 22,
      'description'    => 'Inspired by 1950s Horology with a sunray dial, domed crystal face, and hand-stitched Horween leather strap.',
      'badge'          => null,
      'created_at'     => '2026-07-15',
      'featured'       => false,
    ],
    [
      'id'             => 19,
      'name'           => 'Obsidian Diver 200m Automatic',
      'category'       => 'Watches',
      'price'          => 12499,
      'original_price' => 15999,
      'discount'       => 22,
      'image'          => 'https://images.unsplash.com/photo-1547996160-81dfa63595aa?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black / Black', 'hex' => '#0a0a0a'],
        ['name' => 'Pepsi Bezel', 'hex' => '#1e3a8a'],
      ],
      'sizes'          => ['42mm'],
      'stock'          => 11,
      'description'    => 'Professional 200-meter water-resistant diver watch with 120-click ceramic unidirectional rotating bezel and Super-LumiNova indices.',
      'badge'          => 'New',
      'created_at'     => '2026-09-07',
      'featured'       => false,
    ],
    [
      'id'             => 20,
      'name'           => 'Celestial Gold Minimalist Dress Watch',
      'category'       => 'Watches',
      'price'          => 9999,
      'original_price' => 12999,
      'discount'       => 23,
      'image'          => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Champagne Gold', 'hex' => '#c8a96e'],
        ['name' => 'Rose Gold', 'hex' => '#b76e79'],
      ],
      'sizes'          => ['36mm', '40mm'],
      'stock'          => 17,
      'description'    => 'Ultra-slim 6.5mm profile dress watch featuring an 18K gold-plated case, mesh bracelet, and clean unadorned dial face.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-30',
      'featured'       => true,
    ],

    // ── BAGS ───────────────────────────────────────────────
    [
      'id'             => 21,
      'name'           => 'Full-Grain Leather Crossbody Bag',
      'category'       => 'Bags',
      'price'          => 3799,
      'original_price' => 4999,
      'discount'       => 24,
      'image'          => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1544816155-12387ec4a497?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Chestnut Brown', 'hex' => '#7b3f00'],
        ['name' => 'Jet Black', 'hex' => '#0a0a0a'],
      ],
      'sizes'          => ['One Size'],
      'stock'          => 35,
      'description'    => 'Compact everyday crossbody bag with brass magnetic snap closure, adjustable leather strap, and internal zippered organizer pockets.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-05',
      'featured'       => true,
    ],
    [
      'id'             => 22,
      'name'           => 'Heavy Canvas & Leather Tote Bag',
      'category'       => 'Bags',
      'price'          => 2499,
      'original_price' => 3499,
      'discount'       => 28,
      'image'          => 'https://images.unsplash.com/photo-1544816155-12387ec4a497?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Ecru / Tan', 'hex' => '#f5f0eb'],
        ['name' => 'Black Canvas', 'hex' => '#1c1c1c'],
      ],
      'sizes'          => ['One Size'],
      'stock'          => 40,
      'description'    => '20oz heavy cotton duck canvas tote trimmed with vegetable-tanned leather handles and reinforced copper rivets.',
      'badge'          => null,
      'created_at'     => '2026-07-25',
      'featured'       => false,
    ],
    [
      'id'             => 23,
      'name'           => 'Executive Commuter Leather Backpack',
      'category'       => 'Bags',
      'price'          => 7999,
      'original_price' => 9999,
      'discount'       => 20,
      'image'          => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Sleek Black', 'hex' => '#0a0a0a'],
        ['name' => 'Espresso', 'hex' => '#3d2314'],
      ],
      'sizes'          => ['15-inch Laptop'],
      'stock'          => 18,
      'description'    => 'Architectural leather backpack featuring a padded 16" laptop sleeve, waterproof YKK zips, and breathable mesh back padding.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-22',
      'featured'       => true,
    ],
    [
      'id'             => 24,
      'name'           => 'Structured Mini Shoulder Bag',
      'category'       => 'Bags',
      'price'          => 3299,
      'original_price' => 4299,
      'discount'       => 23,
      'image'          => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Beige Nude', 'hex' => '#e3d5ca'],
        ['name' => 'Black', 'hex' => '#0a0a0a'],
        ['name' => 'Olive', 'hex' => '#556b2f'],
      ],
      'sizes'          => ['One Size'],
      'stock'          => 24,
      'description'    => 'Nineties-inspired baguette shoulder bag rendered in smooth vegan leather with gold turn-lock closure.',
      'badge'          => 'New',
      'created_at'     => '2026-09-08',
      'featured'       => false,
    ],
    [
      'id'             => 25,
      'name'           => 'Weekender Leather Duffle Bag',
      'category'       => 'Bags',
      'price'          => 9999,
      'original_price' => 12999,
      'discount'       => 23,
      'image'          => 'https://images.unsplash.com/photo-1585123334904-845d60c6a760?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Dark Cognac', 'hex' => '#5c2c16'],
        ['name' => 'Midnight Navy', 'hex' => '#0f172a'],
      ],
      'sizes'          => ['45 Litres'],
      'stock'          => 12,
      'description'    => 'Spacious travel duffle bag crafted from supple cowhide leather with a separate ventilated shoe compartment and detachable padded shoulder strap.',
      'badge'          => 'Limited',
      'created_at'     => '2026-08-29',
      'featured'       => false,
    ],

    // ── ACCESSORIES ────────────────────────────────────────
    [
      'id'             => 26,
      'name'           => 'Polarized Acetate Sunglasses',
      'category'       => 'Accessories',
      'price'          => 2999,
      'original_price' => 3999,
      'discount'       => 25,
      'image'          => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black Tortoise', 'hex' => '#2b1b17'],
        ['name' => 'Honey Amber', 'hex' => '#d4a373'],
      ],
      'sizes'          => ['One Size'],
      'stock'          => 30,
      'description'    => 'Hand-polished Italian Mazzucchelli acetate frames paired with Category 3 UV400 polarized lenses for superior optical clarity.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-08',
      'featured'       => true,
    ],
    [
      'id'             => 27,
      'name'           => 'Pure Cashmere Fringe Scarf',
      'category'       => 'Accessories',
      'price'          => 3499,
      'original_price' => 4499,
      'discount'       => 22,
      'image'          => 'https://images.unsplash.com/photo-1520903920243-00d872a2d1c9?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Charcoal Grey', 'hex' => '#36454f'],
        ['name' => 'Camel', 'hex' => '#c19a6b'],
        ['name' => 'Oatmeal', 'hex' => '#e3dac9'],
      ],
      'sizes'          => ['180 x 30 cm'],
      'stock'          => 25,
      'description'    => 'Woven from 100% Mongolian cashmere fibers with twisted fringe detailing. Featherlight insulation against chill breezes.',
      'badge'          => 'Trending',
      'created_at'     => '2026-08-27',
      'featured'       => false,
    ],
    [
      'id'             => 28,
      'name'           => 'Full-Grain Calfskin Reversible Belt',
      'category'       => 'Accessories',
      'price'          => 1899,
      'original_price' => 2499,
      'discount'       => 24,
      'image'          => 'https://images.unsplash.com/photo-1554188248-986adbb73ad1?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Black / Tan Reversible', 'hex' => '#0a0a0a'],
      ],
      'sizes'          => ['32', '34', '36', '38'],
      'stock'          => 40,
      'description'    => 'Dual-sided full grain leather belt featuring a twistable brushed nickel pin buckle. Easily flips between Black and Brown.',
      'badge'          => null,
      'created_at'     => '2026-07-30',
      'featured'       => false,
    ],
    [
      'id'             => 29,
      'name'           => 'Sculptural 18K Gold Hoop Earrings',
      'category'       => 'Accessories',
      'price'          => 2799,
      'original_price' => 3699,
      'discount'       => 24,
      'image'          => 'https://images.unsplash.com/photo-1630019852942-f89202989a59?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => '18K Yellow Gold', 'hex' => '#ffd700'],
        ['name' => 'Silver Rhodium', 'hex' => '#e5e4e2'],
      ],
      'sizes'          => ['25mm'],
      'stock'          => 20,
      'description'    => 'Hollow lightweight organic curved hoop earrings plated in heavy 18K gold over sterling silver with hypoallergenic latch closures.',
      'badge'          => 'New',
      'created_at'     => '2026-09-09',
      'featured'       => true,
    ],
    [
      'id'             => 30,
      'name'           => 'Minimalist RFID Leather Cardholder',
      'category'       => 'Accessories',
      'price'          => 1499,
      'original_price' => 1999,
      'discount'       => 25,
      'image'          => 'https://images.unsplash.com/photo-1606503153255-59d8b8b82176?auto=format&fit=crop&w=800&q=80',
      'secondary_image'=> 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?auto=format&fit=crop&w=800&q=80',
      'colors'         => [
        ['name' => 'Matte Black', 'hex' => '#0a0a0a'],
        ['name' => 'Saddle Tan', 'hex' => '#a0522d'],
        ['name' => 'Forest Green', 'hex' => '#228b22'],
      ],
      'sizes'          => ['One Size'],
      'stock'          => 50,
      'description'    => 'Slimline card wallet with 6 card slots and a central bill compartment, equipped with RFID blocking shielding.',
      'badge'          => 'Sale',
      'created_at'     => '2026-08-14',
      'featured'       => false,
    ],
  ];

  /**
   * Helper Functions
   */

  /**
   * Canonicalizes any stored/posted colors payload into the [{ name, hex }]
   * shape that every card, swatch and quick-view renderer expects.
   *
   * Accepts objects ([{"name":"Black","hex":"#0a0a0a"}]), bare hex strings
   * (["#ffffff"]), plain names (["black"]), comma lists ("Black, White")
   * or a raw JSON string. Never returns strings, so renderers can safely
   * read $color['hex'] / $color['name'].
   */
  function nova_normalize_colors($raw): array {
    if (is_string($raw)) {
      $raw = trim($raw);
      if ($raw === '') return [];
      $decoded = json_decode($raw, true);
      $raw = is_array($decoded)
        ? $decoded
        : array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== '');
    }
    if (!is_array($raw)) return [];

    $normalized = [];
    foreach ($raw as $item) {
      if (is_array($item)) {
        $name = trim((string) ($item['name'] ?? ($item['label'] ?? ($item['hex'] ?? ''))));
        $hex  = trim((string) ($item['hex'] ?? ($item['value'] ?? '')));
      } else {
        $name = trim((string) $item);
        $hex  = '';
      }
      if ($name === '' && $hex === '') continue;

      // A bare "#fff" / "ffffff" entry is its own color name.
      if ($hex === '' && preg_match('/^#?[0-9a-fA-F]{3}$|^#?[0-9a-fA-F]{6}$/', $name)) {
        $hex = strpos($name, '#') === 0 ? $name : '#' . $name;
      }
      if ($hex === '') $hex = nova_color_name_to_hex($name);
      if ($name === '') $name = $hex;

      $normalized[] = ['name' => $name, 'hex' => $hex];
    }
    return $normalized;
  }

  /**
   * Resolves a human color name to a concrete hex value.
   * Unknown names get a stable color derived from the name, so a swatch
   * is always renderable instead of throwing a fatal error.
   */
  function nova_color_name_to_hex(string $name): string {
    static $known = [
      'black' => '#0a0a0a', 'white' => '#ffffff', 'off-white' => '#f4f1ea', 'grey' => '#999999',
      'gray' => '#999999', 'silver' => '#c0c0c0', 'charcoal' => '#333333', 'red' => '#dc2626',
      'maroon' => '#800000', 'blue' => '#1e3a8a', 'navy' => '#1b263b', 'sky blue' => '#87ceeb',
      'green' => '#228b22', 'olive' => '#556b2f', 'forest green' => '#228b22', 'yellow' => '#ffd700',
      'gold' => '#c8a96e', 'orange' => '#ea580c', 'brown' => '#7b3f00', 'tan' => '#a0522d',
      'beige' => '#d4c5b9', 'cream' => '#f5f2eb', 'pink' => '#e8c5c8', 'purple' => '#6b21a8',
      'violet' => '#6d28d9', 'indigo' => '#1a2a3a', 'camel' => '#c19a6b', 'cognac' => '#5c2c16',
      'teal' => '#0f5257', 'khaki' => '#c3b091',
    ];
    $key = strtolower(trim($name));
    if (isset($known[$key])) return $known[$key];

    static $palette = ['#0a0a0a', '#444444', '#8b4513', '#1e3a8a', '#228b22', '#0f5257', '#b76e79', '#c8a96e', '#6b21a8', '#dc2626'];
    return $palette[abs(crc32($key)) % count($palette)];
  }

  /**
   * Build a browser src from a products.image / secondary_image value.
   * Keeps remote http(s) and data: URIs. Turns Windows absolute paths,
   * file:// URLs, and localhost URLs into web-root-relative paths
   * (uploads/... or images/...) so the same DB row works on XAMPP and Vercel.
   */
  function nova_product_image_url(?string $src): string {
    $src = trim((string) $src);
    if ($src === '') {
      return 'images/placeholder.svg';
    }
    if (stripos($src, 'data:') === 0) {
      return $src;
    }

    $path = $src;
    if (preg_match('#^(https?:)?//#i', $src)) {
      $parts = parse_url($src);
      $host = strtolower((string) ($parts['host'] ?? ''));
      if (!in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
        return $src;
      }
      $path = (string) ($parts['path'] ?? '');
    }

    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^file:/+#i', '', $path) ?? $path;
    $path = preg_replace('#^[A-Za-z]:/#', '', $path) ?? $path;

    if (preg_match('#/(uploads|images)/(.+)$#i', '/' . ltrim($path, '/'), $m)) {
      return strtolower($m[1]) . '/' . $m[2];
    }

    $path = ltrim($path, '/');
    $path = preg_replace('#^(?:htdocs/)?NOVA-Ecommerce/#i', '', $path) ?? $path;
    return $path !== '' ? $path : 'images/placeholder.svg';
  }

  function getDatabaseProducts(): ?array {
    global $NOVA_PRODUCTS;
    static $loaded = false;
    static $databaseProducts = null;
    if ($loaded) return $databaseProducts;
    $loaded = true;

    try {
      require_once __DIR__ . '/../config/database.php';
      $db = novaDb();
      $count = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
      if ($count === 0) {
        $insert = $db->prepare('INSERT INTO products (id, name, category, price, original_price, discount, image, secondary_image, colors_json, sizes_json, stock, description, badge, created_at, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($NOVA_PRODUCTS as $product) {
          $insert->execute([
            $product['id'], $product['name'], $product['category'], $product['price'], $product['original_price'] ?? null,
            $product['discount'] ?? null, $product['image'],
            $product['secondary_image'] ?? null, json_encode(nova_normalize_colors($product['colors'] ?? [])), json_encode($product['sizes'] ?? []),
            $product['stock'] ?? 0, $product['description'] ?? null, $product['badge'] ?? null, $product['created_at'] ?? null,
            !empty($product['featured']) ? 1 : 0,
          ]);
        }
      }
      $rows = $db->query('SELECT p.id, p.name, p.category, p.price, p.original_price, p.discount, '
        . 'COALESCE((SELECT AVG(pr.rating) FROM product_reviews pr WHERE pr.product_id = p.id), 0) AS rating, '
        . '(SELECT COUNT(*) FROM product_reviews pr WHERE pr.product_id = p.id) AS reviews, '
        . 'p.image, p.secondary_image, p.colors_json, p.sizes_json, p.stock, p.description, p.badge, p.created_at, p.featured '
        . 'FROM products p ORDER BY p.id ASC')->fetchAll();
      $databaseProducts = array_map(function (array $row): array {
        $row['id'] = (int) $row['id'];
        $row['price'] = (float) $row['price'];
        $row['original_price'] = $row['original_price'] !== null ? (float) $row['original_price'] : null;
        $row['discount'] = $row['discount'] !== null ? (int) $row['discount'] : null;
        $row['rating'] = (float) $row['rating'];
        $row['reviews'] = (int) $row['reviews'];
        $row['stock'] = (int) $row['stock'];
        $row['featured'] = (bool) $row['featured'];
        $row['image'] = nova_product_image_url($row['image'] ?? '');
        $row['secondary_image'] = ($row['secondary_image'] !== null && trim((string) $row['secondary_image']) !== '')
          ? nova_product_image_url($row['secondary_image'])
          : null;
        $row['colors'] = nova_normalize_colors($row['colors_json'] ?? '');
        $row['sizes'] = json_decode($row['sizes_json'] ?: '[]', true) ?: [];
        unset($row['colors_json'], $row['sizes_json']);
        return $row;
      }, $rows);
      return $databaseProducts;
    } catch (Throwable $error) {
      return null;
    }
  }

  function getAllProducts(): array {
    global $NOVA_PRODUCTS;
    $dbProducts = getDatabaseProducts();
    if (!empty($dbProducts)) return $dbProducts;
    return array_map(function (array $product): array {
      $product['rating'] = 0;
      $product['reviews'] = 0;
      return $product;
    }, $NOVA_PRODUCTS);
  }

  function getProductById(int $id): ?array {
    foreach (getAllProducts() as $product) {
      if ((int)$product['id'] === $id) {
        return $product;
      }
    }
    return null;
  }

  function getProductsByCategory(string $category): array {
    $products = getAllProducts();
    if (strtolower($category) === 'all' || empty($category)) {
      return $products;
    }
    return array_values(array_filter($products, function($p) use ($category) {
      return strcasecmp($p['category'], $category) === 0;
    }));
  }

  function getFeaturedProducts(int $limit = 8): array {
    $all = getAllProducts();
    $featured = array_values(array_filter($all, function($p) {
      return !empty($p['featured']);
    }));
    if (empty($featured)) {
      return array_slice($all, 0, $limit);
    }
    return array_slice($featured, 0, $limit);
  }

  function getNewArrivals(int $limit = 4): array {
    $all = getAllProducts();
    usort($all, function($a, $b) {
      $tA = !empty($a['created_at']) ? strtotime($a['created_at']) : 0;
      $tB = !empty($b['created_at']) ? strtotime($b['created_at']) : 0;
      if ($tA === $tB) {
        return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
      }
      return $tB <=> $tA;
    });
    return array_slice($all, 0, $limit);
  }
}



















