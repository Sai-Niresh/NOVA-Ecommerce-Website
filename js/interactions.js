/**
 * NOVA — interactions.js
 * Custom cursor, magnetic button, wishlist heart, micro-interactions
 */

'use strict';

/* ─── REDUCED MOTION ─────────────────────────────────────── */
const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isTouchDevice   = () => window.matchMedia('(hover: none)').matches;
/* Root fix: never let a failed GSAP CDN throw and kill later modules. */
const hasGsapGlobal   = typeof gsap !== 'undefined';

/* ─── 1. CUSTOM CURSOR ───────────────────────────────────── */
(function initCursor() {
  if (isReducedMotion || isTouchDevice()) return;

  const cursor   = document.getElementById('custom-cursor');
  const follower = document.getElementById('cursor-follower');
  if (!cursor || !follower) return;

  let mouseX = -100, mouseY = -100;
  let follX  = -100, follY  = -100;
  let rafId;

  // Show cursor on first move
  window.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
    if (!cursor.classList.contains('is-visible')) {
      cursor.classList.add('is-visible');
      follower.classList.add('is-visible');
    }
  }, { passive: true });

  // Smooth follower loop
  function loop() {
    follX += (mouseX - follX) * 0.1;
    follY += (mouseY - follY) * 0.1;
    cursor.style.transform   = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
    follower.style.transform = `translate(${follX}px, ${follY}px) translate(-50%, -50%)`;
    rafId = requestAnimationFrame(loop);
  }
  loop();

  // Hover state on interactive elements
  const interactives = 'a, button, [role="button"], input, select, textarea, .product-card, .category-card';

  document.addEventListener('mouseover', (e) => {
    if (e.target.closest(interactives)) {
      cursor.classList.add('is-hovered');
      follower.classList.add('is-hovered');
    }
  });

  document.addEventListener('mouseout', (e) => {
    if (e.target.closest(interactives)) {
      cursor.classList.remove('is-hovered');
      follower.classList.remove('is-hovered');
    }
  });

  // Hide on leave
  document.addEventListener('mouseleave', () => {
    cursor.classList.remove('is-visible');
    follower.classList.remove('is-visible');
  });
})();

/* ─── 2. MAGNETIC BUTTONS ────────────────────────────────── */
(function initMagnetic() {
  if (isReducedMotion || isTouchDevice() || !hasGsapGlobal) return;

  const magnetics = document.querySelectorAll('.magnetic-btn');

  magnetics.forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const rect    = btn.getBoundingClientRect();
      const centerX = rect.left + rect.width  / 2;
      const centerY = rect.top  + rect.height / 2;
      const deltaX  = (e.clientX - centerX) * 0.28;
      const deltaY  = (e.clientY - centerY) * 0.28;

      gsap.to(btn, {
        x: deltaX,
        y: deltaY,
        duration: 0.4,
        ease: 'power3.out',
      });
    });

    btn.addEventListener('mouseleave', () => {
      gsap.to(btn, {
        x: 0,
        y: 0,
        duration: 0.6,
        ease: 'elastic.out(1, 0.5)',
      });
    });
  });
})();

/* ─── 3. WISHLIST HEART TOGGLE ───────────────────────────── */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.product-wishlist-btn');
  if (!btn) return;

  const isActive = btn.classList.contains('is-active');
  const productId = btn.dataset.id;
  const icon = btn.querySelector('svg');

  if (!isActive) {
    btn.classList.add('is-active');
    Nova.wishlist.add(String(productId));
    if (icon) {
      icon.style.fill = '#e11d48';
      icon?.classList.add('wishlist-activated');
      setTimeout(() => icon?.classList.remove('wishlist-activated'), 500);
    }
    // Particle burst
    if (!isReducedMotion && hasGsapGlobal) spawnHeartParticles(btn);
  } else {
    btn.classList.remove('is-active');
    Nova.wishlist.delete(String(productId));
    if (icon) {
      icon.style.fill = '';
      icon?.classList.remove('wishlist-activated');
    }
  }

  // Update wishlist panel if open
  if (headerPanel?.classList.contains('is-visible') && headerPanelTitle?.textContent === 'Your Wishlist') {
    renderWishlistPanel();
  }
});

function spawnHeartParticles(btn) {
  const rect = btn.getBoundingClientRect();
  const cx   = rect.left + rect.width  / 2 + window.scrollX;
  const cy   = rect.top  + rect.height / 2 + window.scrollY;

  for (let i = 0; i < 6; i++) {
    const dot = document.createElement('span');
    dot.style.cssText = `
      position: absolute;
      top: ${cy}px;
      left: ${cx}px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #e11d48;
      pointer-events: none;
      z-index: 9999;
      transform: translate(-50%, -50%);
    `;
    document.body.appendChild(dot);

    const angle = (i / 6) * Math.PI * 2;
    const dist  = 24 + Math.random() * 16;
    const tx    = Math.cos(angle) * dist;
    const ty    = Math.sin(angle) * dist;

    gsap.to(dot, {
      x: tx, y: ty,
      opacity: 0,
      scale: 0,
      duration: 0.55,
      ease: 'power2.out',
      onComplete: () => dot.remove(),
    });
  }
}

/* ─── 4. CATEGORY CARD HOVER (GSAP enhanced) ─────────────── */
(function initCategoryHover() {
  if (isReducedMotion || !hasGsapGlobal) return;

  document.querySelectorAll('.category-card').forEach(card => {
    const img    = card.querySelector('.category-card-img');
    const info   = card.querySelector('.category-card-info');
    const arrow  = card.querySelector('.category-arrow');

    card.addEventListener('mouseenter', () => {
      gsap.to(img,   { scale: 1.07, duration: 0.6, ease: 'power2.out' });
      gsap.to(info,  { y: -4,       duration: 0.4, ease: 'power2.out' });
      gsap.to(arrow, { x: 3, y: -3, duration: 0.35, ease: 'power2.out' });
    });

    card.addEventListener('mouseleave', () => {
      gsap.to(img,   { scale: 1,    duration: 0.6, ease: 'power2.out' });
      gsap.to(info,  { y: 0,        duration: 0.4, ease: 'power2.out' });
      gsap.to(arrow, { x: 0, y: 0,  duration: 0.35, ease: 'power2.out' });
    });
  });
})();

/* ─── 5. PRODUCT CARD TILT (subtle, desktop only) ────────── */
(function initCardTilt() {
  if (isReducedMotion || isTouchDevice() || !hasGsapGlobal) return;

  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect   = card.getBoundingClientRect();
      const x      = (e.clientX - rect.left) / rect.width  - 0.5;
      const y      = (e.clientY - rect.top)  / rect.height - 0.5;

      gsap.to(card, {
        rotateY: x * 4,
        rotateX: -y * 4,
        transformPerspective: 800,
        duration: 0.4,
        ease: 'power1.out',
      });
    });

    card.addEventListener('mouseleave', () => {
      gsap.to(card, {
        rotateY: 0,
        rotateX: 0,
        duration: 0.5,
        ease: 'elastic.out(1, 0.6)',
      });
    });
  });
})();

/* ─── 6. CTA HERO BUTTON HOVER ───────────────────────────── */
(function initHeroBtn() {
  if (isReducedMotion || !hasGsapGlobal) return;

  const heroBtn = document.querySelector('.hero-cta-group .magnetic-btn');
  if (!heroBtn) return;

  heroBtn.addEventListener('mouseenter', () => {
    gsap.to(heroBtn, { scale: 1.03, duration: 0.3, ease: 'power2.out' });
  });

  heroBtn.addEventListener('mouseleave', () => {
    gsap.to(heroBtn, { scale: 1, duration: 0.4, ease: 'elastic.out(1,0.5)' });
  });
})();

/* ─── 7. SCROLL-INDICATOR INTERACTION ────────────────────── */
(function initScrollIndicator() {
  const si = document.querySelector('.hero-scroll-indicator');
  if (!si) return;

  si.addEventListener('click', () => {
    const categories = document.getElementById('categories');
    if (categories) categories.scrollIntoView({ behavior: 'smooth' });
  });

  si.style.cursor = 'pointer';
})();

/* ─── 8. SEARCH OVERLAY (placeholder interaction) ────────── */
document.getElementById('search-btn')?.addEventListener('click', () => {
  if (!hasGsapGlobal) return;
  // Brief visual pulse
  const btn = document.getElementById('search-btn');
  gsap.to(btn, { scale: 0.9, duration: 0.1, yoyo: true, repeat: 1, ease: 'power2.inOut' });
});

/* ─── 9. VIEW ALL HOVER ARROWS ───────────────────────────── */
document.querySelectorAll('.view-all-link').forEach(link => {
  const arrow = link.querySelector('.btn-arrow, svg');
  if (!arrow || isReducedMotion) return;
  link.addEventListener('mouseenter', () => {
    gsap.to(arrow, { x: 5, duration: 0.3, ease: 'power2.out' });
  });
  link.addEventListener('mouseleave', () => {
    gsap.to(arrow, { x: 0, duration: 0.3, ease: 'power2.out' });
  });
});
