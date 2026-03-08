/**
 * Post Actions — Like & Share
 * Handles AJAX like button with animation and Web Share API with fallback dialog
 */
(function () {
  'use strict';

  /* ── Snackbar Helper ─────────────────────────── */

  function showSnackbar(message, duration) {
    duration = duration || 2000;
    var container = document.querySelector('.snackbar-container');
    if (!container) return;
    var snackbar = document.createElement('div');
    snackbar.className = 'snackbar snackbar--show';
    snackbar.textContent = message;
    container.appendChild(snackbar);
    setTimeout(function () {
      snackbar.classList.remove('snackbar--show');
      setTimeout(function () { snackbar.remove(); }, 300);
    }, duration);
  }

  /* ── Like Button ────────────────────────────────── */

  const likeBtn = document.querySelector('.post-like-btn');
  if (likeBtn) {
    const postId = likeBtn.closest('article')?.id?.replace('post-', '') || '';
    const storageKey = 'flavor_liked_' + postId;
    const countEl = likeBtn.querySelector('.like-count');
    const iconEl = likeBtn.querySelector('.like-icon');

    // Restore liked state from localStorage
    if (localStorage.getItem(storageKey)) {
      likeBtn.classList.add('is-liked');
      if (iconEl) iconEl.setAttribute('fill', 'var(--md-sys-color-error)');
    }

    likeBtn.addEventListener('click', function () {
      if (likeBtn.classList.contains('is-liked')) return; // Already liked

      likeBtn.classList.add('is-liked', 'like-animate');
      if (iconEl) iconEl.setAttribute('fill', 'var(--md-sys-color-error)');
      localStorage.setItem(storageKey, '1');
      showSnackbar('已点赞 ♥');

      // Remove animation class after it completes
      likeBtn.addEventListener('animationend', function handler() {
        likeBtn.classList.remove('like-animate');
        likeBtn.removeEventListener('animationend', handler);
      });

      // AJAX request
      var fd = new FormData();
      fd.append('action', 'flavor_like_post');
      fd.append('post_id', postId);
      fd.append('nonce', flavorPostActions.nonce);

      fetch(flavorPostActions.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success && countEl) {
            countEl.textContent = data.data.count;
          }
        })
        .catch(function () { /* silent fail, UI already updated */ });
    });
  }

  /* ── Comment Like (事件委托) ──────────────────────── */

  var commentList = document.querySelector('.comment-list');
  if (commentList) {
    // Restore liked state from localStorage
    commentList.querySelectorAll('.comment-like-btn').forEach(function (btn) {
      var cid = btn.dataset.commentId;
      if (localStorage.getItem('flavor_liked_comment_' + cid)) {
        btn.classList.add('is-liked');
        var icon = btn.querySelector('.comment-like-icon');
        if (icon) icon.setAttribute('fill', 'var(--md-sys-color-error)');
      }
    });

    // Event delegation for comment likes
    commentList.addEventListener('click', function (e) {
      var btn = e.target.closest('.comment-like-btn');
      if (!btn || btn.classList.contains('is-liked')) return;

      var cid = btn.dataset.commentId;
      var countEl = btn.querySelector('.comment-like-count');
      var iconEl = btn.querySelector('.comment-like-icon');

      btn.classList.add('is-liked', 'like-animate');
      if (iconEl) iconEl.setAttribute('fill', 'var(--md-sys-color-error)');
      localStorage.setItem('flavor_liked_comment_' + cid, '1');

      btn.addEventListener('animationend', function handler() {
        btn.classList.remove('like-animate');
        btn.removeEventListener('animationend', handler);
      });

      var fd = new FormData();
      fd.append('action', 'flavor_like_comment');
      fd.append('comment_id', cid);
      fd.append('nonce', flavorPostActions.nonce);

      fetch(flavorPostActions.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success && countEl) {
            countEl.textContent = data.data.count;
          }
        })
        .catch(function () { /* silent fail */ });
    });
  }

  /* ── Share Button ───────────────────────────────── */

  var shareBtn = document.querySelector('.post-share-btn');
  if (shareBtn) {
    shareBtn.addEventListener('click', function () {
      var title = document.title;
      var url = window.location.href;
      var text = document.querySelector('meta[name="description"]')?.content || '';

      // Web Share API (mobile & modern browsers)
      if (navigator.share) {
        navigator.share({ title: title, text: text, url: url }).catch(function () {});
        return;
      }

      // Fallback: show share dialog
      showShareDialog(title, url);
    });
  }

  function showShareDialog(title, url) {
    // Remove existing dialog
    var existing = document.getElementById('share-dialog');
    if (existing) existing.remove();

    var encoded = encodeURIComponent(url);
    var encodedTitle = encodeURIComponent(title);

    var dialog = document.createElement('div');
    dialog.id = 'share-dialog';
    dialog.className = 'share-dialog';
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-label', '\u5206\u4eab');
    dialog.innerHTML =
      '<div class="share-dialog__scrim"></div>' +
      '<div class="share-dialog__surface">' +
        '<h3 class="share-dialog__title">\u5206\u4eab\u6587\u7ae0</h3>' +
        '<div class="share-dialog__links">' +
          '<a href="https://twitter.com/intent/tweet?url=' + encoded + '&text=' + encodedTitle + '" target="_blank" rel="noopener" class="share-link">' +
            '<svg viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>' +
            '<span>X</span>' +
          '</a>' +
          '<a href="https://service.weibo.com/share/share.php?url=' + encoded + '&title=' + encodedTitle + '" target="_blank" rel="noopener" class="share-link">' +
            '<svg viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M10.098 20.323c-3.977.391-7.414-1.406-7.672-4.02-.259-2.609 2.759-5.047 6.74-5.441 3.979-.394 7.413 1.404 7.671 4.018.259 2.6-2.759 5.049-6.739 5.443zM9.05 17.219c-.384.616-1.208.884-1.829.602-.612-.279-.793-.991-.406-1.593.379-.595 1.176-.861 1.793-.601.622.263.82.972.442 1.592zm1.27-1.627c-.141.237-.449.353-.689.253-.236-.09-.313-.361-.177-.586.138-.227.436-.346.672-.24.239.09.315.36.194.573zm.176-2.719c-1.893-.493-4.033.45-4.857 2.118-.836 1.704-.026 3.591 1.886 4.21 1.983.642 4.318-.341 5.132-2.145.8-1.765-.145-3.658-2.161-4.183z"/><path fill="currentColor" d="M17.737 12.776c-.183-.479-.548-.84-.926-.84-.093 0-.186.023-.274.069-.293.15-.393.524-.217.854.09.17.14.362.14.561 0 .797-.673 1.451-1.499 1.451-.127 0-.253-.016-.374-.048-.337-.085-.598.082-.598.376 0 .175.118.346.3.415.25.097.519.148.791.148 1.37 0 2.489-1.085 2.489-2.414 0-.21-.032-.414-.09-.607.058-.017.114-.035.168-.056.383-.147.561-.473.397-.814a.638.638 0 00-.307-.095z"/><path fill="currentColor" d="M20.242 10.044c-.676-.824-1.726-1.199-2.741-.984-.34.072-.547.37-.463.667.084.297.398.487.739.416.556-.118 1.13.088 1.503.539.373.452.44 1.05.187 1.556-.138.275-.036.58.228.684.077.03.157.044.235.044.188 0 .373-.094.475-.26.477-.951.368-2.103-.163-2.662zM21.803 8.429c-1.14-1.389-2.916-2.028-4.625-1.664a.801.801 0 00-.622.898c.092.397.488.655.886.571 1.27-.27 2.59.204 3.44 1.237.85 1.034.997 2.418.406 3.564-.19.368-.034.818.348.999a.798.798 0 00.355.083c.28 0 .546-.145.681-.392.887-1.725.637-3.818-.869-5.296z"/></svg>' +
            '<span>\u5fae\u535a</span>' +
          '</a>' +
          '<button class="share-link share-copy-btn">' +
            '<svg viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>' +
            '<span>\u590d\u5236\u94fe\u63a5</span>' +
          '</button>' +
        '</div>' +
        '<button class="share-dialog__close md-button-text">\u5173\u95ed</button>' +
      '</div>';

    document.body.appendChild(dialog);

    // Force reflow then animate in
    dialog.offsetHeight;
    dialog.classList.add('is-open');

    // Close handlers
    var closeDialog = function () {
      dialog.classList.remove('is-open');
      dialog.addEventListener('transitionend', function handler() {
        dialog.remove();
        dialog.removeEventListener('transitionend', handler);
      });
    };

    dialog.querySelector('.share-dialog__scrim').addEventListener('click', closeDialog);
    dialog.querySelector('.share-dialog__close').addEventListener('click', closeDialog);

    // Copy link
    dialog.querySelector('.share-copy-btn').addEventListener('click', function () {
      navigator.clipboard.writeText(url).then(function () {
        showSnackbar('链接已复制');
        closeDialog();
      });
    });

    // ESC key
    document.addEventListener('keydown', function handler(e) {
      if (e.key === 'Escape') {
        closeDialog();
        document.removeEventListener('keydown', handler);
      }
    });
  }
})();
