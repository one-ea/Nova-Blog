/**
 * Flavor Theme - 文章目录导航 (Table of Contents)
 * 自动扫描文章标题生成目录树，滚动高亮当前章节
 */

(function () {
  'use strict';

  /** 配置 */
  var HEADING_SELECTOR = '.entry-content h2, .entry-content h3, .entry-content h4';
  var TOC_CONTAINER_SELECTOR = '.toc-container';
  var OBSERVER_ROOT_MARGIN = '-80px 0px -60% 0px';

  /** 状态 */
  var headings = [];
  var tocLinks = [];
  var observer = null;
  var tocContainer = null;
  var tocList = null;
  var tocToggle = null;
  var isCollapsed = false;

  // ─── 生成目录树 ───────────────────────────────────

  function buildToc() {
    tocContainer = document.querySelector(TOC_CONTAINER_SELECTOR);
    if (!tocContainer) return false;

    var rawHeadings = document.querySelectorAll(HEADING_SELECTOR);
    if (rawHeadings.length === 0) {
      tocContainer.style.display = 'none';
      return false;
    }

    // 确保每个标题有 id
    rawHeadings.forEach(function (heading, index) {
      if (!heading.id) {
        heading.id = 'toc-heading-' + index;
      }
      headings.push({
        el: heading,
        id: heading.id,
        text: heading.textContent.trim(),
        level: parseInt(heading.tagName.charAt(1), 10)
      });
    });

    // 创建目录标题栏
    var tocHeader = document.createElement('div');
    tocHeader.className = 'toc-header';

    var tocTitle = document.createElement('span');
    tocTitle.className = 'toc-title';
    tocTitle.textContent = '目录';
    tocHeader.appendChild(tocTitle);

    // 移动端折叠按钮
    tocToggle = document.createElement('button');
    tocToggle.className = 'toc-toggle';
    tocToggle.setAttribute('aria-label', '展开/收起目录');
    tocToggle.setAttribute('aria-expanded', 'true');
    tocToggle.textContent = '▾';
    tocToggle.addEventListener('click', toggleCollapse);
    tocHeader.appendChild(tocToggle);

    tocContainer.appendChild(tocHeader);

    // 创建列表
    tocList = document.createElement('nav');
    tocList.className = 'toc-nav';
    tocList.setAttribute('aria-label', '文章目录');

    var ol = document.createElement('ol');
    ol.className = 'toc-list';

    // 找到最小层级作为基准
    var minLevel = Math.min.apply(null, headings.map(function (h) { return h.level; }));

    headings.forEach(function (heading) {
      var li = document.createElement('li');
      var indent = heading.level - minLevel;
      li.className = 'toc-item toc-item--level-' + indent;
      li.style.paddingLeft = (indent * 16) + 'px';

      var link = document.createElement('a');
      link.className = 'toc-link';
      link.href = '#' + heading.id;
      link.textContent = heading.text;
      link.setAttribute('data-heading-id', heading.id);

      // 平滑滚动
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var target = document.getElementById(heading.id);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          // 更新 URL hash（不触发跳转）
          history.pushState(null, '', '#' + heading.id);
        }
      });

      li.appendChild(link);
      ol.appendChild(li);
      tocLinks.push(link);
    });

    tocList.appendChild(ol);
    tocContainer.appendChild(tocList);

    return true;
  }

  // ─── 折叠 / 展开（移动端） ────────────────────────

  function toggleCollapse() {
    isCollapsed = !isCollapsed;
    if (tocList) {
      tocList.classList.toggle('toc-nav--collapsed', isCollapsed);
    }
    if (tocToggle) {
      tocToggle.textContent = isCollapsed ? '▸' : '▾';
      tocToggle.setAttribute('aria-expanded', String(!isCollapsed));
    }
  }

  // ─── Intersection Observer 滚动高亮 ──────────────

  function initObserver() {
    if (headings.length === 0) return;

    // 记录每个标题的可见状态
    var visibleHeadings = new Map();

    observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        visibleHeadings.set(entry.target.id, entry.isIntersecting);
      });

      // 找到第一个可见的标题
      var activeId = null;
      for (var i = 0; i < headings.length; i++) {
        if (visibleHeadings.get(headings[i].id)) {
          activeId = headings[i].id;
          break;
        }
      }

      // 如果没有可见标题，找最后一个已经滚过的
      if (!activeId) {
        for (var j = headings.length - 1; j >= 0; j--) {
          var rect = headings[j].el.getBoundingClientRect();
          if (rect.top < window.innerHeight * 0.4) {
            activeId = headings[j].id;
            break;
          }
        }
      }

      if (activeId) {
        highlightTocItem(activeId);
      }
    }, {
      rootMargin: OBSERVER_ROOT_MARGIN,
      threshold: [0, 1]
    });

    headings.forEach(function (heading) {
      observer.observe(heading.el);
    });
  }

  function highlightTocItem(activeId) {
    tocLinks.forEach(function (link) {
      var isActive = link.getAttribute('data-heading-id') === activeId;
      link.classList.toggle('toc-link--active', isActive);
      link.setAttribute('aria-current', isActive ? 'true' : 'false');
    });

    // 确保活跃项在目录容器中可见
    var activeLink = tocContainer.querySelector('.toc-link--active');
    if (activeLink && tocList) {
      var linkRect = activeLink.getBoundingClientRect();
      var navRect = tocList.getBoundingClientRect();
      if (linkRect.top < navRect.top || linkRect.bottom > navRect.bottom) {
        activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
    }
  }

  // ─── 清理 ─────────────────────────────────────────

  function destroy() {
    if (observer) {
      observer.disconnect();
      observer = null;
    }
    headings = [];
    tocLinks = [];
  }

  // ─── 初始化 ───────────────────────────────────────

  function init() {
    var built = buildToc();
    if (built) {
      initObserver();
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // 暴露 destroy 方法供 SPA 场景使用
  window.flavorToc = { destroy: destroy };
})();
