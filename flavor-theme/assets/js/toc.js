/**
 * Flavor Theme - 文章目录导航 (Table of Contents)
 * 自动扫描文章标题生成目录树，滚动高亮当前章节
 */
{
  'use strict';

  /** 配置 */
  const HEADING_SELECTOR = '.entry-content h2, .entry-content h3, .entry-content h4';
  const TOC_CONTAINER_SELECTOR = '.toc-container';
  const OBSERVER_ROOT_MARGIN = '-80px 0px -60% 0px';

  /** 状态 */
  let headings = [];
  let tocLinks = [];
  let observer = null;
  let tocContainer = null;
  let tocList = null;
  let tocToggle = null;
  let isCollapsed = false;

  // ─── 生成目录树 ───────────────────────────────────

  const buildToc = () => {
    tocContainer = document.querySelector(TOC_CONTAINER_SELECTOR);
    if (!tocContainer) return false;

    const rawHeadings = document.querySelectorAll(HEADING_SELECTOR);
    if (rawHeadings.length === 0) {
      tocContainer.style.display = 'none';
      return false;
    }

    // 确保每个标题有 id
    rawHeadings.forEach((heading, index) => {
      if (!heading.id) {
        heading.id = `toc-heading-${index}`;
      }
      headings.push({
        el: heading,
        id: heading.id,
        text: heading.textContent.trim(),
        level: parseInt(heading.tagName.charAt(1), 10),
      });
    });

    // 创建目录标题栏
    const tocHeader = document.createElement('div');
    tocHeader.className = 'toc-header';

    const tocTitle = document.createElement('span');
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

    const ol = document.createElement('ul');
    ol.className = 'toc-list';

    const minLevel = Math.min(...headings.map((h) => h.level));

    for (const heading of headings) {
      const indent = heading.level - minLevel;
      const li = document.createElement('li');
      li.className = `toc-item toc-item--level-${indent}`;
      li.style.paddingLeft = `${indent * 16}px`;

      const link = document.createElement('a');
      link.className = 'toc-link';
      link.href = `#${heading.id}`;
      link.textContent = heading.text;
      link.setAttribute('data-heading-id', heading.id);

      // 平滑滚动
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.getElementById(heading.id);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          history.pushState(null, '', `#${heading.id}`);
        }
      });

      li.appendChild(link);
      ol.appendChild(li);
      tocLinks.push(link);
    }

    tocList.appendChild(ol);
    tocContainer.appendChild(tocList);

    return true;
  };

  // ─── 折叠 / 展开（移动端） ────────────────────────

  const toggleCollapse = () => {
    isCollapsed = !isCollapsed;
    tocList?.classList.toggle('toc-nav--collapsed', isCollapsed);
    if (tocToggle) {
      tocToggle.textContent = isCollapsed ? '▸' : '▾';
      tocToggle.setAttribute('aria-expanded', String(!isCollapsed));
    }
  };

  // ─── Intersection Observer 滚动高亮 ──────────────

  const highlightTocItem = (activeId) => {
    for (const link of tocLinks) {
      const isActive = link.getAttribute('data-heading-id') === activeId;
      link.classList.toggle('toc-link--active', isActive);
      link.setAttribute('aria-current', isActive ? 'true' : 'false');
    }

    // 确保活跃项在目录容器中可见
    const activeLink = tocContainer?.querySelector('.toc-link--active');
    if (activeLink && tocList) {
      const linkRect = activeLink.getBoundingClientRect();
      const navRect = tocList.getBoundingClientRect();
      if (linkRect.top < navRect.top || linkRect.bottom > navRect.bottom) {
        activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
    }
  };

  const initObserver = () => {
    if (headings.length === 0) return;

    const visibleHeadings = new Map();

    observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        visibleHeadings.set(entry.target.id, entry.isIntersecting);
      }

      // 找到第一个可见的标题
      let activeId = null;
      for (const heading of headings) {
        if (visibleHeadings.get(heading.id)) {
          activeId = heading.id;
          break;
        }
      }

      // 如果没有可见标题，找最后一个已经滚过的
      if (!activeId) {
        for (let j = headings.length - 1; j >= 0; j--) {
          if (headings[j].el.getBoundingClientRect().top < window.innerHeight * 0.4) {
            activeId = headings[j].id;
            break;
          }
        }
      }

      if (activeId) highlightTocItem(activeId);
    }, {
      rootMargin: OBSERVER_ROOT_MARGIN,
      threshold: [0, 1],
    });

    for (const heading of headings) {
      observer.observe(heading.el);
    }
  };

  // ─── 清理 ─────────────────────────────────────────

  const destroy = () => {
    observer?.disconnect();
    observer = null;
    headings = [];
    tocLinks = [];
  };

  // ─── 初始化 ───────────────────────────────────────

  const init = () => {
    if (buildToc()) initObserver();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // 暴露 destroy 方法供 SPA 场景使用
  window.flavorToc = { destroy };
}
