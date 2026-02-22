/**
 * AJAX Comments
 * Submits comment form via fetch, inserts new comment without page reload
 */
(function () {
  'use strict';

  var form = document.querySelector('.comment-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var submitBtn = form.querySelector('[type="submit"]');
    var originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '\u63d0\u4ea4\u4e2d\u2026';

    var fd = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: fd,
      redirect: 'follow',
      credentials: 'same-origin',
    })
      .then(function (response) {
        if (response.redirected || response.ok) {
          // WordPress redirects to the post URL with #comment-{id} on success
          return response.text().then(function (html) {
            return { ok: true, html: html, url: response.url };
          });
        }
        // Error response (duplicate, too fast, etc.)
        return response.text().then(function (html) {
          return { ok: false, html: html };
        });
      })
      .then(function (result) {
        if (result.ok) {
          // Parse the returned page to extract the new comment
          var parser = new DOMParser();
          var doc = parser.parseFromString(result.html, 'text/html');

          // Find comment list in returned page
          var newList = doc.querySelector('.comment-list');
          var currentList = document.querySelector('.comment-list');
          var commentsArea = document.querySelector('.comments-area');

          if (newList) {
            if (currentList) {
              // Replace existing comment list with updated one
              currentList.innerHTML = newList.innerHTML;
            } else if (commentsArea) {
              // First comment — create the list
              var title = commentsArea.querySelector('.comments-title');
              var ol = document.createElement('ol');
              ol.className = 'comment-list';
              ol.innerHTML = newList.innerHTML;
              if (title) {
                title.after(ol);
              } else {
                commentsArea.prepend(ol);
              }
            }
          }

          // Update comments title count
          var newTitle = doc.querySelector('.comments-title');
          var curTitle = document.querySelector('.comments-title');
          if (newTitle && curTitle) {
            curTitle.innerHTML = newTitle.innerHTML;
          }

          // Clear the form
          var textarea = form.querySelector('textarea');
          if (textarea) textarea.value = '';

          // Scroll to newest comment
          var hash = result.url.split('#')[1];
          if (hash) {
            var newComment = document.getElementById(hash);
            if (newComment) {
              newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
              newComment.classList.add('comment-highlight');
              setTimeout(function () {
                newComment.classList.remove('comment-highlight');
              }, 3000);
            }
          }

          showSnackbar('\u8bc4\u8bba\u53d1\u8868\u6210\u529f');
        } else {
          // Parse error message from WordPress error page
          var errParser = new DOMParser();
          var errDoc = errParser.parseFromString(result.html, 'text/html');
          var errP = errDoc.querySelector('p');
          var errMsg = errP ? errP.textContent : '\u8bc4\u8bba\u63d0\u4ea4\u5931\u8d25\uff0c\u8bf7\u91cd\u8bd5';
          showSnackbar(errMsg);
        }
      })
      .catch(function () {
        showSnackbar('\u7f51\u7edc\u9519\u8bef\uff0c\u8bf7\u7a0d\u540e\u91cd\u8bd5');
      })
      .finally(function () {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
  });

  function showSnackbar(msg) {
    var container = document.querySelector('.snackbar-container');
    if (!container) return;
    var snackbar = document.createElement('div');
    snackbar.className = 'snackbar snackbar--show';
    snackbar.textContent = msg;
    container.appendChild(snackbar);
    setTimeout(function () {
      snackbar.classList.remove('snackbar--show');
      setTimeout(function () { snackbar.remove(); }, 300);
    }, 3000);
  }
})();
