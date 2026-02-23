/**
 * Scroll Enhancements
 * - Reading progress bar (single post pages)
 * - Scroll-in reveal animations (IntersectionObserver)
 * - Auto-applies .scroll-in to key elements (zero template changes)
 * - Reads flavorConfig for animation toggle control
 */
(function () {
  'use strict';

  var config = window.flavorConfig || {};
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // --- Reading Progress Bar (functional UI, unaffected by animation toggles) ---
  var progressContainer = document.querySelector('.reading-progress');
  if (progressContainer) {
    var progressBar = progressContainer.querySelector('.reading-progress__bar');
    if (progressBar) {
      var ticking = false;
      window.addEventListener('scroll', function () {
        if (!ticking) {
          window.requestAnimationFrame(function () {
            var scrollTop = window.scrollY;
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var progress = docHeight > 0 ? Math.min(scrollTop / docHeight, 1) : 0;
            progressBar.style.transform = 'scaleX(' + progress + ')';
            ticking = false;
          });
          ticking = true;
        }
      }, { passive: true });
    }
  }

  // --- Scroll-in Reveal Animation ---
  // Guard 1: system prefers-reduced-motion
  if (prefersReducedMotion) return;
  // Guard 2: global animation toggle
  if (config.enableAnimations === false) return;
  // Guard 3: scroll animation sub-toggle
  if (config.enableScrollAnimations === false) return;

  // Auto-apply .scroll-in to key content elements
  var autoSelectors = [
    '.post-card',
    '.hero-section',
    '.author-card',
    '.related-posts',
    '.featured-post'
  ];
  autoSelectors.forEach(function (sel) {
    document.querySelectorAll(sel).forEach(function (el) {
      el.classList.add('scroll-in');
    });
  });

  var scrollElements = document.querySelectorAll('.scroll-in');
  if (!scrollElements.length) return;

  if (!('IntersectionObserver' in window)) {
    scrollElements.forEach(function (el) {
      el.classList.add('scroll-in--visible');
    });
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('scroll-in--visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
  );

  scrollElements.forEach(function (el) {
    observer.observe(el);
  });
})();
