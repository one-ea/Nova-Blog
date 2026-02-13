/**
 * Flavor Theme — Navigation
 * Drawer (mobile) + App Bar scroll shadow + Back to Top FAB
 */
(function () {
  'use strict';

  let drawer, scrim, menuBtn, closeBtn, appBar, backToTop;
  let drawerOpen = false;

  // ─── Drawer ─────────────────────────────────────────

  function openDrawer() {
    if (!drawer) return;
    drawerOpen = true;
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    if (scrim) scrim.classList.add('open');
    if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    // Focus first link
    const firstLink = drawer.querySelector('a');
    if (firstLink) firstLink.focus();
  }

  function closeDrawer() {
    if (!drawer || !drawerOpen) return;
    drawerOpen = false;
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    if (scrim) scrim.classList.remove('open');
    if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    if (menuBtn) menuBtn.focus();
  }

  // ─── App Bar Scroll Shadow ──────────────────────────

  function onScroll() {
    const scrollY = window.scrollY;

    // App bar shadow
    if (appBar) {
      appBar.classList.toggle('scrolled', scrollY > 8);
    }

    // Back to top visibility
    if (backToTop) {
      backToTop.classList.toggle('visible', scrollY > 400);
    }
  }

  // ─── Init ───────────────────────────────────────────

  function init() {
    drawer = document.querySelector('.nav-drawer');
    scrim = document.querySelector('.drawer-scrim');
    menuBtn = document.querySelector('.app-bar__menu-btn');
    closeBtn = document.querySelector('.drawer-close-btn');
    appBar = document.querySelector('.app-bar');
    backToTop = document.querySelector('.back-to-top');

    // Drawer open/close
    if (menuBtn) menuBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (scrim) scrim.addEventListener('click', closeDrawer);

    // ESC to close
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawerOpen) closeDrawer();
    });

    // Close drawer on desktop resize
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 840 && drawerOpen) closeDrawer();
    }, { passive: true });

    // Scroll events
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // Initial state

    // Back to top click
    if (backToTop) {
      backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
