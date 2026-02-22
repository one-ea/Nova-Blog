/**
 * Code Block Copy Button
 * Injects a copy button into every <pre> block on single post pages.
 * Uses navigator.clipboard API with fallback.
 */
(function () {
  'use strict';

  var SVG_COPY = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>';
  var SVG_CHECK = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';

  var blocks = document.querySelectorAll('pre');
  if (!blocks.length) return;

  blocks.forEach(function (pre) {
    var btn = document.createElement('button');
    btn.className = 'copy-code-btn';
    btn.setAttribute('aria-label', '复制代码');
    btn.innerHTML = SVG_COPY;

    btn.addEventListener('click', function () {
      var code = pre.querySelector('code');
      var text = (code || pre).textContent;

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onSuccess, onFail);
      } else {
        // Fallback for older browsers / non-HTTPS
        try {
          var ta = document.createElement('textarea');
          ta.value = text;
          ta.style.cssText = 'position:fixed;left:-9999px';
          document.body.appendChild(ta);
          ta.select();
          document.execCommand('copy');
          document.body.removeChild(ta);
          onSuccess();
        } catch (e) {
          onFail();
        }
      }

      function onSuccess() {
        btn.classList.add('copied');
        btn.innerHTML = SVG_CHECK;
        setTimeout(function () {
          btn.classList.remove('copied');
          btn.innerHTML = SVG_COPY;
        }, 2000);
      }

      function onFail() {
        btn.setAttribute('aria-label', '复制失败');
        setTimeout(function () {
          btn.setAttribute('aria-label', '复制代码');
        }, 2000);
      }
    });

    pre.appendChild(btn);
  });
})();
