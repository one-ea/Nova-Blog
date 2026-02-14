/**
 * Flavor Theme — Navigation
 * Drawer + App Bar scroll + Back to Top + Search shortcut + Share
 */
(function () {
  'use strict';

  var drawer, scrim, menuBtn, closeBtn, appBar, backToTop;
  var searchOverlay, searchToggle, searchClose, searchInput;
  var drawerOpen = false;
  var searchOpen = false;

  // ─── Drawer ─────────────────────────────────────────

  function openDrawer() {
    if (!drawer) return;
    drawerOpen = true;
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    if (scrim) scrim.classList.add('open');
    if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    var firstLink = drawer.querySelector('a');
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

  // ─── Search Overlay ─────────────────────────────────

  function openSearch() {
    if (!searchOverlay) return;
    searchOpen = true;
    searchOverlay.classList.add('open');
    searchOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (searchInput) {
      searchInput.value = '';
      searchInput.focus();
    }
  }

  function closeSearch() {
    if (!searchOverlay || !searchOpen) return;
    searchOpen = false;
    searchOverlay.classList.remove('open');
    searchOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (searchToggle) searchToggle.focus();
  }

  // ─── App Bar Scroll Shadow ──────────────────────────

  function onScroll() {
    var scrollY = window.scrollY;
    if (appBar) appBar.classList.toggle('scrolled', scrollY > 8);
    if (backToTop) backToTop.classList.toggle('visible', scrollY > 400);
  }

  // ─── Share ──────────────────────────────────────────

  function initShare() {
    var shareBtn = document.querySelector('.post-share-btn');
    if (!shareBtn) return;

    shareBtn.addEventListener('click', function () {
      var titleEl = document.querySelector('.post-title');
      var data = {
        title: titleEl ? titleEl.textContent : document.title,
        url: window.location.href
      };

      if (navigator.share) {
        navigator.share(data).catch(function () {});
      } else {
        navigator.clipboard.writeText(data.url).then(function () {
          showSnackbar('Link copied!');
        }).catch(function () {
          var input = document.createElement('input');
          input.value = data.url;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
          showSnackbar('Link copied!');
        });
      }
    });
  }

  function showSnackbar(message) {
    var container = document.querySelector('.snackbar-container');
    if (!container) return;

    var snackbar = document.createElement('div');
    snackbar.className = 'md-snackbar';

    var msgSpan = document.createElement('span');
    msgSpan.className = 'md-snackbar__message';
    msgSpan.textContent = message;
    snackbar.appendChild(msgSpan);

    container.appendChild(snackbar);
    requestAnimationFrame(function () {
      snackbar.classList.add('md-snackbar--show');
    });
    setTimeout(function () {
      snackbar.classList.remove('md-snackbar--show');
      setTimeout(function () { snackbar.remove(); }, 300);
    }, 3000);
  }

  // ─── Init ───────────────────────────────────────────

  function init() {
    drawer = document.querySelector('.nav-drawer');
    scrim = document.querySelector('.drawer-scrim');
    menuBtn = document.querySelector('.app-bar__menu-btn');
    closeBtn = document.querySelector('.drawer-close-btn');
    appBar = document.querySelector('.app-bar');
    backToTop = document.querySelector('.back-to-top');
    searchOverlay = document.querySelector('.search-overlay');
    searchToggle = document.querySelector('.search-toggle-btn');
    searchClose = document.querySelector('.search-close-btn');
    searchInput = searchOverlay ? searchOverlay.querySelector('input[type="search"]') : null;

    // Drawer
    if (menuBtn) menuBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (scrim) scrim.addEventListener('click', closeDrawer);

    // Search
    if (searchToggle) searchToggle.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        if (searchOpen) closeSearch();
        else if (drawerOpen) closeDrawer();
      }
      if ((e.key === '/' || (e.key === 'k' && (e.ctrlKey || e.metaKey))) && !searchOpen) {
        var tag = document.activeElement.tagName;
        if (tag !== 'INPUT' && tag !== 'TEXTAREA' && !document.activeElement.isContentEditable) {
          e.preventDefault();
          openSearch();
        }
      }
    });

    // Close drawer on desktop resize
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 840 && drawerOpen) closeDrawer();
    }, { passive: true });

    // Scroll
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Back to top
    if (backToTop) {
      backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // Share
    initShare();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
