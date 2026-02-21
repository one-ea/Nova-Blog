/**
 * Flavor Theme - 搜索交互
 * 搜索栏 morph 展开、实时建议、键盘导航
 */

(function () {
  'use strict';

  /** 配置 */
  const DEBOUNCE_MS = 180;
  const API_ENDPOINT = '/wp-json/wp/v2/search';
  const MIN_QUERY_LENGTH = 2;

  /** DOM 引用 */
  let searchToggle = null;
  let searchBar = null;
  let searchInput = null;
  let suggestionsContainer = null;

  /** 状态 */
  let isOpen = false;
  let debounceTimer = null;
  let abortController = null;
  let selectedIndex = -1;
  let suggestions = [];

  // ─── 搜索栏展开 / 收起 ────────────────────────────

  function openSearch() {
    if (!searchBar || isOpen) return;
    isOpen = true;

    searchBar.classList.add('search-overlay--active');
    searchBar.setAttribute('aria-hidden', 'false');
    searchToggle?.setAttribute('aria-expanded', 'true');

    requestAnimationFrame(function () {
      searchInput?.focus();
    });
  }

  function closeSearch() {
    if (!isOpen) return;
    isOpen = false;

    searchBar?.classList.remove('search-overlay--active');
    searchBar?.setAttribute('aria-hidden', 'true');
    searchToggle?.setAttribute('aria-expanded', 'false');

    // 清理
    clearSuggestions();
    if (searchInput) searchInput.value = '';
    selectedIndex = -1;
    suggestions = [];

    searchToggle?.focus();
  }

  // ─── 搜索建议 ─────────────────────────────────────

  function fetchSuggestions(query) {
    if (query.length < MIN_QUERY_LENGTH) {
      clearSuggestions();
      return;
    }

    // 取消上一次请求
    if (abortController) {
      abortController.abort();
    }
    abortController = new AbortController();

    const url = API_ENDPOINT + '?search=' + encodeURIComponent(query) + '&per_page=8&_fields=id,title,url,type,subtype';

    const headers = (typeof flavorData !== 'undefined' && flavorData.nonce)
      ? { 'X-WP-Nonce': flavorData.nonce }
      : {};

    fetch(url, { signal: abortController.signal, headers: headers })
      .then(function (res) {
        if (!res.ok) throw new Error('Search request failed');
        return res.json();
      })
      .then(function (results) {
        suggestions = results;
        selectedIndex = -1;
        renderSuggestions(query);
      })
      .catch(function (err) {
        if (err.name !== 'AbortError') {
          clearSuggestions();
        }
      });
  }

  /**
   * 高亮匹配文字
   */
  function highlightMatch(text, query) {
    if (!query) return document.createTextNode(text);

    const fragment = document.createDocumentFragment();
    const lowerText = text.toLowerCase();
    const lowerQuery = query.toLowerCase();
    let lastIndex = 0;

    let pos = lowerText.indexOf(lowerQuery);
    while (pos !== -1) {
      // 匹配前的普通文本
      if (pos > lastIndex) {
        fragment.appendChild(document.createTextNode(text.slice(lastIndex, pos)));
      }
      // 匹配部分
      const mark = document.createElement('mark');
      mark.className = 'search-highlight';
      mark.textContent = text.slice(pos, pos + query.length);
      fragment.appendChild(mark);

      lastIndex = pos + query.length;
      pos = lowerText.indexOf(lowerQuery, lastIndex);
    }

    // 剩余文本
    if (lastIndex < text.length) {
      fragment.appendChild(document.createTextNode(text.slice(lastIndex)));
    }

    return fragment;
  }

  function renderSuggestions(query) {
    if (!suggestionsContainer) return;

    // 清空
    while (suggestionsContainer.firstChild) {
      suggestionsContainer.removeChild(suggestionsContainer.firstChild);
    }

    if (suggestions.length === 0) {
      const empty = document.createElement('li');
      empty.className = 'search-suggestion search-suggestion--empty';
      empty.setAttribute('role', 'option');
      empty.textContent = '没有找到相关结果';
      suggestionsContainer.appendChild(empty);
      suggestionsContainer.classList.add('search-suggestions--visible');
      return;
    }

    suggestions.forEach((item, index) => {
      const li = document.createElement('li');
      li.className = 'search-suggestion';
      li.setAttribute('role', 'option');
      li.setAttribute('data-index', String(index));
      li.setAttribute('data-url', item.url || '');

      // 类型标签
      if (item.subtype) {
        const badge = document.createElement('span');
        badge.className = 'search-suggestion__type';
        badge.textContent = item.subtype;
        li.appendChild(badge);
      }

      // 标题（带高亮）
      const titleSpan = document.createElement('span');
      titleSpan.className = 'search-suggestion__title';
      let titleText = item.title || '';
      // WP REST API 返回的 title 可能是 rendered 对象
      if (typeof titleText === 'object' && titleText.rendered) {
        titleText = titleText.rendered;
      }
      // 去除 HTML 标签
      const tempDiv = document.createElement('div');
      tempDiv.textContent = titleText;
      const cleanTitle = tempDiv.textContent;

      titleSpan.appendChild(highlightMatch(cleanTitle, query));
      li.appendChild(titleSpan);

      // 点击跳转
      li.addEventListener('click', function () {
        navigateTo(item.url);
      });

      suggestionsContainer.appendChild(li);
    });

    suggestionsContainer.classList.add('search-suggestions--visible');
  }

  function clearSuggestions() {
    if (!suggestionsContainer) return;
    while (suggestionsContainer.firstChild) {
      suggestionsContainer.removeChild(suggestionsContainer.firstChild);
    }
    suggestionsContainer.classList.remove('search-suggestions--visible');
    suggestions = [];
    selectedIndex = -1;
  }

  function navigateTo(url) {
    if (url) {
      window.location.href = url;
    }
  }

  // ─── 键盘导航 ─────────────────────────────────────

  function onInputKeyDown(e) {
    const items = suggestionsContainer ? suggestionsContainer.querySelectorAll('.search-suggestion:not(.search-suggestion--empty)') : [];

    switch (e.key) {
      case 'ArrowDown':
        e.preventDefault();
        if (items.length === 0) return;
        selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
        updateSelection(items);
        break;

      case 'ArrowUp':
        e.preventDefault();
        if (items.length === 0) return;
        selectedIndex = Math.max(selectedIndex - 1, 0);
        updateSelection(items);
        break;

      case 'Enter':
        e.preventDefault();
        if (selectedIndex >= 0 && selectedIndex < suggestions.length) {
          navigateTo(suggestions[selectedIndex].url);
        } else if (searchInput && searchInput.value.trim()) {
          // 回车执行搜索
          navigateTo('/?s=' + encodeURIComponent(searchInput.value.trim()));
        }
        break;

      case 'Escape':
        e.preventDefault();
        closeSearch();
        break;
    }
  }

  function updateSelection(items) {
    items.forEach(function (item, i) {
      item.classList.toggle('search-suggestion--selected', i === selectedIndex);
      item.setAttribute('aria-selected', i === selectedIndex ? 'true' : 'false');
    });

    // 滚动到可见
    if (items[selectedIndex]) {
      items[selectedIndex].scrollIntoView({ block: 'nearest' });
    }

    // 更新 aria-activedescendant
    if (searchInput && items[selectedIndex]) {
      searchInput.setAttribute('aria-activedescendant', items[selectedIndex].id || '');
    }
  }

  // ─── 防抖输入 ─────────────────────────────────────

  function onInput() {
    clearTimeout(debounceTimer);
    const query = searchInput ? searchInput.value.trim() : '';

    if (query.length < MIN_QUERY_LENGTH) {
      clearSuggestions();
      return;
    }

    debounceTimer = setTimeout(function () {
      fetchSuggestions(query);
    }, DEBOUNCE_MS);
  }

  // ─── 点击外部关闭 ─────────────────────────────────

  function onDocumentClick(e) {
    if (!isOpen) return;
    if (searchBar && searchBar.contains(e.target)) return;
    if (searchToggle && searchToggle.contains(e.target)) return;
    closeSearch();
  }

  function onDocumentKeyDown(e) {
    if (e.key === 'Escape' && isOpen) {
      closeSearch();
    }
  }

  // ─── 初始化 ───────────────────────────────────────

  function init() {
    searchToggle = document.querySelector('.search-toggle-btn');
    const searchToggleMobile = document.querySelector('.search-toggle-btn-mobile');
    searchBar = document.querySelector('.search-overlay');
    searchInput = searchBar ? searchBar.querySelector('.md-search-bar__input') : null;
    suggestionsContainer = searchBar ? searchBar.querySelector('.search-overlay__suggestions') : null;

    if (searchToggle) {
      searchToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        isOpen ? closeSearch() : openSearch();
      });
    }

    if (searchToggleMobile) {
      searchToggleMobile.addEventListener('click', function (e) {
        e.stopPropagation();
        isOpen ? closeSearch() : openSearch();
      });
    }

    // Close button inside overlay
    const backBtn = searchBar ? searchBar.querySelector('.search-overlay__back') : null;
    if (backBtn) {
      backBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeSearch();
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', onInput);
      searchInput.addEventListener('keydown', onInputKeyDown);

      // ARIA 属性
      searchInput.setAttribute('role', 'combobox');
      searchInput.setAttribute('aria-expanded', 'false');
      searchInput.setAttribute('aria-autocomplete', 'list');
      searchInput.setAttribute('autocomplete', 'off');
    }

    if (suggestionsContainer) {
      suggestionsContainer.setAttribute('role', 'listbox');
    }

    document.addEventListener('click', onDocumentClick);
    document.addEventListener('keydown', onDocumentKeyDown);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
