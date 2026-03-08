/**
 * Flavor Theme — Navigation
 * Drawer + App Bar scroll + Back to Top + Search shortcut + Share
 */
{
  'use strict';

  let drawer, scrim, menuBtn, closeBtn, appBar, backToTop;
  let searchOverlay, searchToggle, searchClose, searchInput;
  let drawerOpen = false;
  let searchOpen = false;

  // ─── Drawer ─────────────────────────────────────────

  const openDrawer = () => {
    if (!drawer) return;
    drawerOpen = true;
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');
    scrim?.classList.add('open');
    menuBtn?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    drawer.querySelector('a')?.focus();
  };

  const closeDrawer = () => {
    if (!drawer || !drawerOpen) return;
    drawerOpen = false;
    drawer.classList.remove('open');
    drawer.setAttribute('aria-hidden', 'true');
    scrim?.classList.remove('open');
    menuBtn?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    menuBtn?.focus();
  };

  // ─── Search Overlay ─────────────────────────────────

  const openSearch = () => {
    if (!searchOverlay) return;
    searchOpen = true;
    searchOverlay.classList.add('open');
    searchOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (searchInput) {
      searchInput.value = '';
      searchInput.focus();
    }
  };

  const closeSearch = () => {
    if (!searchOverlay || !searchOpen) return;
    searchOpen = false;
    searchOverlay.classList.remove('open');
    searchOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    searchToggle?.focus();
  };

  // ─── App Bar Scroll Shadow ──────────────────────────

  const onScroll = () => {
    const scrollY = window.scrollY;
    appBar?.classList.toggle('scrolled', scrollY > 8);
    backToTop?.classList.toggle('visible', scrollY > 400);
  };

  // ─── Share ──────────────────────────────────────────

  const showSnackbar = (message) => {
    const container = document.querySelector('.snackbar-container');
    if (!container) return;

    const snackbar = document.createElement('div');
    snackbar.className = 'md-snackbar';

    const msgSpan = document.createElement('span');
    msgSpan.className = 'md-snackbar__message';
    msgSpan.textContent = message;
    snackbar.appendChild(msgSpan);

    container.appendChild(snackbar);
    requestAnimationFrame(() => snackbar.classList.add('md-snackbar--show'));
    setTimeout(() => {
      snackbar.classList.remove('md-snackbar--show');
      setTimeout(() => snackbar.remove(), 300);
    }, 3000);
  };

  const initShare = () => {
    const shareBtn = document.querySelector('.post-share-btn');
    if (!shareBtn) return;

    shareBtn.addEventListener('click', async () => {
      const titleEl = document.querySelector('.post-title');
      const data = {
        title: titleEl?.textContent ?? document.title,
        url: window.location.href,
      };

      if (navigator.share) {
        try { await navigator.share(data); } catch { /* 用户取消 */ }
      } else if (navigator.clipboard) {
        try {
          await navigator.clipboard.writeText(data.url);
          showSnackbar('链接已复制！');
        } catch {
          showSnackbar('复制失败');
        }
      }
    });
  };

  // ─── Init ───────────────────────────────────────────

  const init = () => {
    drawer = document.querySelector('.nav-drawer');
    scrim = document.querySelector('.drawer-scrim');
    menuBtn = document.querySelector('.app-bar__menu-btn');
    closeBtn = document.querySelector('.drawer-close-btn');
    appBar = document.querySelector('.app-bar');
    backToTop = document.querySelector('.back-to-top');
    searchOverlay = document.querySelector('.search-overlay');
    searchToggle = document.querySelector('.search-toggle-btn');
    searchClose = document.querySelector('.search-close-btn');
    searchInput = searchOverlay?.querySelector('input[type="search"]') ?? null;

    // Drawer
    menuBtn?.addEventListener('click', openDrawer);
    closeBtn?.addEventListener('click', closeDrawer);
    scrim?.addEventListener('click', closeDrawer);

    // Search
    searchToggle?.addEventListener('click', openSearch);
    searchClose?.addEventListener('click', closeSearch);

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        if (searchOpen) closeSearch();
        else if (drawerOpen) closeDrawer();
      }
      if ((e.key === '/' || (e.key === 'k' && (e.ctrlKey || e.metaKey))) && !searchOpen) {
        const tag = document.activeElement?.tagName;
        if (tag !== 'INPUT' && tag !== 'TEXTAREA' && !document.activeElement?.isContentEditable) {
          e.preventDefault();
          openSearch();
        }
      }
    });

    // Close drawer on desktop resize
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 840 && drawerOpen) closeDrawer();
    }, { passive: true });

    // Scroll
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Back to top
    backToTop?.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Share
    initShare();

    // Bottom navigation (mobile)
    document.getElementById('bottom-nav-search')?.addEventListener('click', openSearch);
    document.getElementById('bottom-nav-menu')?.addEventListener('click', openDrawer);
    document.getElementById('bottom-nav-top')?.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
}
