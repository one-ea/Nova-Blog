/**
 * Flavor Theme - Material Ripple 波纹效果
 * 自动为所有 .md-ripple 元素添加点击波纹
 * 使用事件委托挂在 document 上，性能友好
 */

(function () {
  'use strict';

  /**
   * 注入波纹所需的 CSS keyframes（仅注入一次）
   */
  function injectStyles() {
    if (document.getElementById('md-ripple-styles')) return;
    const style = document.createElement('style');
    style.id = 'md-ripple-styles';
    style.textContent = [
      '.md-ripple { position: relative; overflow: hidden; -webkit-tap-highlight-color: transparent; }',
      '.md-ripple__wave {',
      '  position: absolute;',
      '  border-radius: 50%;',
      '  background: var(--md-ripple-color, currentColor);',
      '  opacity: 0.16;',
      '  pointer-events: none;',
      '  transform: scale(0);',
      '  animation: md-ripple-expand 500ms cubic-bezier(0.4, 0, 0.2, 1) forwards;',
      '}',
      '.md-ripple__wave--fade {',
      '  animation: md-ripple-fade 300ms cubic-bezier(0.4, 0, 0.2, 1) forwards;',
      '}',
      '@keyframes md-ripple-expand {',
      '  to { transform: scale(1); opacity: 0.12; }',
      '}',
      '@keyframes md-ripple-fade {',
      '  to { opacity: 0; }',
      '}'
    ].join('\n');
    document.head.appendChild(style);
  }

  /**
   * 找到事件目标最近的 .md-ripple 祖先
   */
  function findRippleHost(target) {
    return target.closest('.md-ripple');
  }

  /**
   * 在指定元素的点击位置创建波纹
   */
  function createRipple(host, pointerX, pointerY) {
    const rect = host.getBoundingClientRect();

    // 点击位置相对于元素
    const x = pointerX - rect.left;
    const y = pointerY - rect.top;

    // 波纹半径需要覆盖整个元素：取点击位置到四个角的最大距离
    const radius = Math.hypot(
      Math.max(x, rect.width - x),
      Math.max(y, rect.height - y)
    );

    const diameter = radius * 2;

    const wave = document.createElement('span');
    wave.className = 'md-ripple__wave';
    wave.style.width = diameter + 'px';
    wave.style.height = diameter + 'px';
    wave.style.left = (x - radius) + 'px';
    wave.style.top = (y - radius) + 'px';

    host.appendChild(wave);

    return wave;
  }

  /**
   * 淡出并移除波纹元素
   */
  function fadeOutRipple(wave) {
    if (!wave || wave.classList.contains('md-ripple__wave--fade')) return;
    wave.classList.add('md-ripple__wave--fade');
    wave.addEventListener('animationend', function () {
      if (wave.parentNode) {
        wave.parentNode.removeChild(wave);
      }
    }, { once: true });
  }

  // 跟踪当前活跃的波纹，以便在 pointerup 时淡出
  let activeWave = null;

  function onPointerDown(e) {
    // 仅响应主按钮（鼠标左键 / 触摸）
    if (e.button && e.button !== 0) return;

    const host = findRippleHost(e.target);
    if (!host) return;

    // 尊重 prefers-reduced-motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    activeWave = createRipple(host, e.clientX, e.clientY);
  }

  function onPointerUp() {
    if (activeWave) {
      // 确保扩散动画至少播放 150ms 后再淡出
      const wave = activeWave;
      activeWave = null;
      const minDelay = 150;
      const elapsed = wave.getAnimations?.()[0]?.currentTime || minDelay;
      const remaining = Math.max(0, minDelay - elapsed);
      setTimeout(function () {
        fadeOutRipple(wave);
      }, remaining);
    }
  }

  function onPointerCancel() {
    if (activeWave) {
      fadeOutRipple(activeWave);
      activeWave = null;
    }
  }

  /**
   * 初始化：注入样式 + 绑定事件委托
   */
  function init() {
    injectStyles();
    document.addEventListener('pointerdown', onPointerDown, { passive: true });
    document.addEventListener('pointerup', onPointerUp, { passive: true });
    document.addEventListener('pointerleave', onPointerCancel, { passive: true });
    document.addEventListener('pointercancel', onPointerCancel, { passive: true });
  }

  // DOM ready 后初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
