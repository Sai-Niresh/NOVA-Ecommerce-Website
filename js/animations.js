/**
 * NOVA — animations.js
 * GSAP page-load timeline, ScrollTrigger reveals, Three.js hero accent
 * Runs after DOM is ready (deferred script)
 */

'use strict';

/* ─── GSAP PLUGIN REGISTRATION ───────────────────────────── */
gsap.registerPlugin(ScrollTrigger);

/* ─── REDUCED MOTION CHECK ───────────────────────────────── */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ─── 1. PAGE LOADER → HERO ENTRANCE ─────────────────────── */
function runPageLoad() {
  const loader    = document.getElementById('page-loader');
  const loaderLogo= document.getElementById('loader-logo');
  const loaderBar = document.getElementById('loader-bar');

  if (!loader) { runHeroEntrance(); return; }

  const tl = gsap.timeline({
    onComplete: () => {
      loader.style.pointerEvents = 'none';
      loader.classList.add('is-complete');
      runHeroEntrance();
    }
  });

  // Loader logo fades in
  tl.to(loaderLogo, { opacity: 1, duration: 0.5, ease: 'power2.out' })
  // Bar fills
    .to(loaderBar, { width: '100%', duration: 0.9, ease: 'power3.inOut' }, 0.2)
  // Short pause
    .to({}, { duration: 0.2 })
  // Loader exits upward
    .to(loader, {
      yPercent: -100,
      duration: 0.8,
      ease: 'power3.inOut',
    });
}

function runHeroEntrance() {
  if (prefersReducedMotion) {
    // Just make everything visible
    gsap.set([
      '.hero-label', '.hero-heading', '.hero-subtext',
      '.hero-cta-group', '.hero-scroll-indicator'
    ], { opacity: 1, y: 0 });
    return;
  }

  const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

  heroTl
    .fromTo('.hero-image',
      { scale: 1.08 },
      { scale: 1, duration: 2.2, ease: 'power2.out' }
    )
    .fromTo('.hero-label',
      { opacity: 0, y: 16 },
      { opacity: 1, y: 0, duration: 0.7 },
      '-=1.6'
    )
    .fromTo('.hero-heading',
      { opacity: 0, y: 40 },
      { opacity: 1, y: 0, duration: 0.9 },
      '-=0.5'
    )
    .fromTo('.hero-subtext',
      { opacity: 0, y: 24 },
      { opacity: 1, y: 0, duration: 0.7 },
      '-=0.4'
    )
    .fromTo('.hero-cta-group',
      { opacity: 0, y: 20 },
      { opacity: 1, y: 0, duration: 0.6 },
      '-=0.3'
    )
    .fromTo('.hero-scroll-indicator',
      { opacity: 0 },
      { opacity: 1, duration: 0.8 },
      '-=0.2'
    );
}

/* ─── 2. SECTION REVEAL (ScrollTrigger) ──────────────────── */
function setupScrollReveals() {
  if (prefersReducedMotion) return;

  // Generic section headings
  gsap.utils.toArray('.section-heading, .section-eyebrow').forEach(el => {
    gsap.fromTo(el,
      { opacity: 0, y: 30 },
      {
        opacity: 1, y: 0,
        duration: 0.8,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 88%',
          toggleActions: 'play none none none',
        }
      }
    );
  });

  // Category cards — stagger
  const categoryCards = gsap.utils.toArray('.category-card');
  if (categoryCards.length) {
    gsap.fromTo(categoryCards,
      { opacity: 0, y: 50, scale: 0.96 },
      {
        opacity: 1, y: 0, scale: 1,
        duration: 0.7,
        ease: 'power3.out',
        stagger: { each: 0.08, from: 'start' },
        scrollTrigger: {
          trigger: '.categories-grid',
          start: 'top 85%',
          toggleActions: 'play none none none',
        }
      }
    );
  }

  // Product cards — stagger per grid
  gsap.utils.toArray('.products-grid').forEach(grid => {
    const cards = grid.querySelectorAll('.product-card');
    gsap.fromTo(cards,
      { opacity: 0, y: 40 },
      {
        opacity: 1, y: 0,
        duration: 0.65,
        ease: 'power3.out',
        stagger: 0.1,
        scrollTrigger: {
          trigger: grid,
          start: 'top 87%',
          toggleActions: 'play none none none',
        }
      }
    );
  });

  // Promo banner content
  gsap.fromTo('.promo-content',
    { opacity: 0, x: -50 },
    {
      opacity: 1, x: 0,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.promo-section',
        start: 'top 80%',
        toggleActions: 'play none none none',
      }
    }
  );

  // Brand story — image from left, content from right
  gsap.fromTo('.brand-story-image-wrapper',
    { opacity: 0, x: -50 },
    {
      opacity: 1, x: 0,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.brand-story-grid',
        start: 'top 82%',
        toggleActions: 'play none none none',
      }
    }
  );

  gsap.fromTo('.brand-story-content',
    { opacity: 0, x: 40 },
    {
      opacity: 1, x: 0,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.brand-story-grid',
        start: 'top 82%',
        toggleActions: 'play none none none',
      }
    }
  );

  // Pillar items
  gsap.fromTo('.pillar-item',
    { opacity: 0, y: 20 },
    {
      opacity: 1, y: 0,
      duration: 0.6,
      stagger: 0.12,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: '.brand-story-pillars',
        start: 'top 88%',
      }
    }
  );

  // Newsletter
  gsap.fromTo('.newsletter-inner > *',
    { opacity: 0, y: 30 },
    {
      opacity: 1, y: 0,
      duration: 0.7,
      stagger: 0.15,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: '.newsletter-section',
        start: 'top 85%',
      }
    }
  );

  // Footer columns
  gsap.fromTo('.footer-col',
    { opacity: 0, y: 25 },
    {
      opacity: 1, y: 0,
      duration: 0.6,
      stagger: 0.1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: '.footer-grid',
        start: 'top 90%',
      }
    }
  );
}

/* ─── 3. PROMO PARALLAX ──────────────────────────────────── */
function setupPromoParallax() {
  if (prefersReducedMotion) return;
  const promoImg = document.querySelector('.promo-image');
  if (!promoImg) return;

  gsap.to(promoImg, {
    yPercent: -18,
    ease: 'none',
    scrollTrigger: {
      trigger: '.promo-section',
      start: 'top bottom',
      end: 'bottom top',
      scrub: true,
    }
  });
}

/* ─── 4. HERO PARALLAX ───────────────────────────────────── */
function setupHeroParallax() {
  if (prefersReducedMotion) return;
  const heroImg = document.querySelector('.hero-image');
  if (!heroImg) return;

  gsap.to(heroImg, {
    yPercent: 15,
    ease: 'none',
    scrollTrigger: {
      trigger: '.hero-section',
      start: 'top top',
      end: 'bottom top',
      scrub: true,
    }
  });
}

/* ─── 5. THREE.JS HERO ACCENT ────────────────────────────── */
function initThreeHero() {
  const canvas = document.getElementById('hero-canvas');
  if (!canvas || typeof THREE === 'undefined') return;

  const W = canvas.offsetWidth  || window.innerWidth;
  const H = canvas.offsetHeight || window.innerHeight;

  /* Renderer */
  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setSize(W, H);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setClearColor(0x000000, 0);

  /* Scene & Camera */
  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, W / H, 0.1, 100);
  camera.position.z = 4;

  /* Particle field */
  const count   = 600;
  const geo     = new THREE.BufferGeometry();
  const pos     = new Float32Array(count * 3);
  const spread  = 6;

  for (let i = 0; i < count * 3; i += 3) {
    pos[i]   = (Math.random() - 0.5) * spread * 2;
    pos[i+1] = (Math.random() - 0.5) * spread * 1.6;
    pos[i+2] = (Math.random() - 0.5) * spread;
  }
  geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));

  const mat = new THREE.PointsMaterial({
    color: 0xffffff,
    size: 0.022,
    transparent: true,
    opacity: 0.35,
    sizeAttenuation: true,
  });

  const particles = new THREE.Points(geo, mat);
  scene.add(particles);

  /* Floating torus wireframe (accent) */
  const torusGeo  = new THREE.TorusGeometry(0.8, 0.22, 10, 40);
  const torusMat  = new THREE.MeshBasicMaterial({
    color: 0xc8a96e,
    wireframe: true,
    transparent: true,
    opacity: 0.12,
  });
  const torus = new THREE.Mesh(torusGeo, torusMat);
  torus.position.set(3.5, 0.5, 0);
  scene.add(torus);

  /* Animation loop */
  let rafId;
  let stopped = false;

  function animate(t = 0) {
    if (stopped) return;
    rafId = requestAnimationFrame(animate);
    const time = t * 0.001;
    particles.rotation.y = time * 0.04;
    particles.rotation.x = time * 0.02;
    torus.rotation.x = time * 0.25;
    torus.rotation.y = time * 0.18;
    torus.position.y = 0.5 + Math.sin(time * 0.6) * 0.12;
    renderer.render(scene, camera);
  }

  animate();

  /* Resize */
  function onResize() {
    const nW = canvas.offsetWidth;
    const nH = canvas.offsetHeight;
    camera.aspect = nW / nH;
    camera.updateProjectionMatrix();
    renderer.setSize(nW, nH);
  }
  window.addEventListener('resize', onResize, { passive: true });

  /* Stop when hero is out of view (performance) */
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => { stopped = !e.isIntersecting; if (!stopped) animate(); });
    }, { threshold: 0 });
    io.observe(canvas);
  }
}

/* ─── INIT ───────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  runPageLoad();
  setupScrollReveals();
  setupPromoParallax();
  setupHeroParallax();
  initThreeHero();

  // Refresh ScrollTrigger after any layout shift
  window.addEventListener('load', () => ScrollTrigger.refresh());
});
