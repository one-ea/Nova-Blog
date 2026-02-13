/**
 * Flavor Theme - Dark Mode Toggle
 * 支持 light / dark / auto 三种模式循环切换
 * 使用 View Transition API 实现圆形 clip-path 扩散动画
 */

class ThemeToggle {
  static STORAGE_KEY = 'flavor-theme';
  static MODES = ['light', 'dark', 'auto'];

  static LABELS = {
    light: '浅色模式',
    dark: '深色模式',
    auto: '跟随系统'
  };

  constructor() {
    this.theme = localStorage.getItem(ThemeToggle.STORAGE_KEY) || 'auto';
    this.button = null;
    this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    this.init();
  }

  init() {
    // 立即应用主题，避免闪烁
    this.applyTheme();

    document.addEventListener('DOMContentLoaded', () => {
      this.button = document.querySelector('.theme-toggle-btn');
      if (this.button) {
        this.button.addEventListener('click', (e) => this.toggle(e));
        this.updateIcon();
      }
    });

    // 监听系统主题变化，auto 模式下实时响应
    this.mediaQuery.addEventListener('change', () => {
      if (this.theme === 'auto') {
        this.applyTheme();
      }
    });
  }

  /**
   * 获取实际生效的主题（将 auto 解析为 light 或 dark）
   */
  getEffectiveTheme() {
    if (this.theme === 'auto') {
      return this.mediaQuery.matches ? 'dark' : 'light';
    }
    return this.theme;
  }

  /**
   * 将当前主题应用到 document.documentElement
   */
  applyTheme() {
    const effective = this.getEffectiveTheme();
    document.documentElement.setAttribute('data-theme', effective);
    document.documentElement.setAttribute('data-theme-setting', this.theme);
    document.documentElement.style.colorScheme = effective;

    // 通知颜色引擎重新应用配色（light/dark 切换时颜色不同）
    document.dispatchEvent(new CustomEvent('themeChanged'));
  }

  /**
   * 创建 SVG 图标元素（安全 DOM 方式，避免 innerHTML）
   */
  _createSvgIcon(pathData) {
    const NS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(NS, 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('width', '24');
    svg.setAttribute('height', '24');
    svg.setAttribute('fill', 'currentColor');
    svg.setAttribute('aria-hidden', 'true');
    const path = document.createElementNS(NS, 'path');
    path.setAttribute('d', pathData);
    svg.appendChild(path);
    return svg;
  }

  /**
   * 获取当前模式对应的 SVG path data
   */
  _getIconPath() {
    const paths = {
      light: 'M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 00-1.41 0 .996.996 0 000 1.41l1.06 1.06c.39.39 1.03.39 1.41 0a.996.996 0 000-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 000-1.41.996.996 0 00-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z',
      dark: 'M12 3a9 9 0 109 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 01-4.4 2.26 5.403 5.403 0 01-3.14-9.8c-.44-.06-.9-.1-1.36-.1z',
      auto: 'M12 22C6.49 22 2 17.51 2 12S6.49 2 12 2s10 4.04 10 9c0 3.31-2.69 6-6 6h-1.77c-.28 0-.5.22-.5.5 0 .12.05.23.13.33.41.47.64 1.06.64 1.67A2.5 2.5 0 0112 22zm0-18c-4.41 0-8 3.59-8 8s3.59 8 8 8c.28 0 .5-.22.5-.5a.54.54 0 00-.14-.35c-.41-.46-.63-1.05-.63-1.65a2.5 2.5 0 012.5-2.5H16c2.21 0 4-1.79 4-4 0-3.86-3.59-7-8-7z'
    };
    return paths[this.theme];
  }

  /**
   * 循环切换 light -> dark -> auto
   * 带 View Transition clip-path 圆形扩散动画
   */
  toggle(event) {
    const currentIndex = ThemeToggle.MODES.indexOf(this.theme);
    this.theme = ThemeToggle.MODES[(currentIndex + 1) % ThemeToggle.MODES.length];
    localStorage.setItem(ThemeToggle.STORAGE_KEY, this.theme);

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!prefersReduced && document.startViewTransition) {
      const rect = this.button.getBoundingClientRect();
      const x = rect.left + rect.width / 2;
      const y = rect.top + rect.height / 2;
      const endRadius = Math.hypot(
        Math.max(x, window.innerWidth - x),
        Math.max(y, window.innerHeight - y)
      );

      const transition = document.startViewTransition(() => {
        this.applyTheme();
        this.updateIcon();
      });

      transition.ready.then(() => {
        document.documentElement.animate(
          {
            clipPath: [
              `circle(0px at ${x}px ${y}px)`,
              `circle(${endRadius}px at ${x}px ${y}px)`
            ]
          },
          {
            duration: 400,
            easing: 'ease-in-out',
            pseudoElement: '::view-transition-new(root)'
          }
        );
      }).catch(() => {
        // 动画失败静默处理，主题已切换
      });
    } else {
      this.applyTheme();
      this.updateIcon();
    }
  }

  /**
   * 更新切换按钮的 SVG 图标和 aria-label（安全 DOM 操作）
   */
  updateIcon() {
    if (!this.button) return;
    // 清空现有内容
    while (this.button.firstChild) {
      this.button.removeChild(this.button.firstChild);
    }
    this.button.appendChild(this._createSvgIcon(this._getIconPath()));
    this.button.setAttribute('aria-label', ThemeToggle.LABELS[this.theme]);
    this.button.setAttribute('title', ThemeToggle.LABELS[this.theme]);
  }
}

// 尽早实例化以避免主题闪烁
const themeToggle = new ThemeToggle();
