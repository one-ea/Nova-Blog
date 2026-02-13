/**
 * Flavor Theme - Dynamic Color Engine
 * 基于 Material Design 3 的 HCT 色彩空间，从种子色生成完整配色方案
 */

class ColorEngine {
    /**
     * 从 hex 种子色生成完整的 M3 配色方案并应用到 CSS 变量
     * @param {string} seedHex - 种子色 hex 值，如 '#6750A4'
     */
    static applyTheme(seedHex) {
        const hsl = ColorEngine.hexToHsl(seedHex);
        const scheme = ColorEngine.generateScheme(hsl);
        ColorEngine.applyScheme(scheme);
    }

    // === 颜色转换工具 ===

    /**
     * Hex 转 RGB
     * @param {string} hex - 如 '#FF5500' 或 '#F50'
     * @returns {{r: number, g: number, b: number}} 0-255
     */
    static hexToRgb(hex) {
        let h = hex.replace('#', '');
        if (h.length === 3) {
            h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
        }
        const num = parseInt(h, 16);
        return {
            r: (num >> 16) & 255,
            g: (num >> 8) & 255,
            b: num & 255,
        };
    }

    /**
     * RGB 转 Hex
     * @param {number} r - 0-255
     * @param {number} g - 0-255
     * @param {number} b - 0-255
     * @returns {string} 如 '#ff5500'
     */
    static rgbToHex(r, g, b) {
        const clamp = (v) => Math.max(0, Math.min(255, Math.round(v)));
        return (
            '#' +
            [clamp(r), clamp(g), clamp(b)]
                .map((v) => v.toString(16).padStart(2, '0'))
                .join('')
        );
    }

    /**
     * Hex 转 HSL
     * @param {string} hex
     * @returns {{h: number, s: number, l: number}} h: 0-360, s: 0-100, l: 0-100
     */
    static hexToHsl(hex) {
        const { r, g, b } = ColorEngine.hexToRgb(hex);
        const rn = r / 255;
        const gn = g / 255;
        const bn = b / 255;

        const max = Math.max(rn, gn, bn);
        const min = Math.min(rn, gn, bn);
        const delta = max - min;

        let h = 0;
        let s = 0;
        const l = (max + min) / 2;

        if (delta !== 0) {
            s = l > 0.5 ? delta / (2 - max - min) : delta / (max + min);

            if (max === rn) {
                h = ((gn - bn) / delta + (gn < bn ? 6 : 0)) * 60;
            } else if (max === gn) {
                h = ((bn - rn) / delta + 2) * 60;
            } else {
                h = ((rn - gn) / delta + 4) * 60;
            }
        }

        return {
            h: Math.round(h * 10) / 10,
            s: Math.round(s * 1000) / 10,
            l: Math.round(l * 1000) / 10,
        };
    }

    /**
     * HSL 转 Hex
     * @param {number} h - 0-360
     * @param {number} s - 0-100
     * @param {number} l - 0-100
     * @returns {string} hex
     */
    static hslToHex(h, s, l) {
        const sn = s / 100;
        const ln = l / 100;

        const c = (1 - Math.abs(2 * ln - 1)) * sn;
        const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
        const m = ln - c / 2;

        let rn, gn, bn;

        if (h < 60) {
            rn = c; gn = x; bn = 0;
        } else if (h < 120) {
            rn = x; gn = c; bn = 0;
        } else if (h < 180) {
            rn = 0; gn = c; bn = x;
        } else if (h < 240) {
            rn = 0; gn = x; bn = c;
        } else if (h < 300) {
            rn = x; gn = 0; bn = c;
        } else {
            rn = c; gn = 0; bn = x;
        }

        return ColorEngine.rgbToHex(
            (rn + m) * 255,
            (gn + m) * 255,
            (bn + m) * 255
        );
    }

    /**
     * 生成 Tonal Palette
     * 给定 hue 和 saturation，生成从 0(黑) 到 100(白) 的色调阶梯
     *
     * @param {number} hue - 0-360
     * @param {number} saturation - 0-100
     * @returns {Object} { tone_value: hex_color }
     */
    static generateTonalPalette(hue, saturation) {
        const tones = [
            0, 4, 6, 10, 12, 17, 20, 22, 24, 25, 30, 35, 40, 50, 60, 70, 80,
            87, 90, 92, 94, 95, 96, 98, 99, 100,
        ];

        const palette = {};

        for (const tone of tones) {
            // tone 直接映射到 lightness（0 = 黑，100 = 白）
            const lightness = tone;

            // 在极端 tone 值时降低饱和度（接近纯黑/纯白时饱和度自然降低）
            // 使用抛物线衰减：中间 tone 保持原饱和度，两端趋近 0
            let adjustedSat = saturation;

            if (tone <= 10) {
                // 暗端：线性衰减
                adjustedSat = saturation * (tone / 10);
            } else if (tone >= 90) {
                // 亮端：线性衰减
                adjustedSat = saturation * ((100 - tone) / 10);
            }

            // 确保饱和度在合理范围
            adjustedSat = Math.max(0, Math.min(100, adjustedSat));

            palette[tone] = ColorEngine.hslToHex(hue, adjustedSat, lightness);
        }

        return palette;
    }

    /**
     * 从种子色 HSL 生成完整的 M3 配色方案
     * @param {{h: number, s: number, l: number}} seedHsl
     * @returns {{light: Object, dark: Object}}
     */
    static generateScheme(seedHsl) {
        const { h, s } = seedHsl;

        // Primary palette: 使用种子色的 hue
        const primaryPalette = ColorEngine.generateTonalPalette(h, s);

        // Secondary palette: 同 hue，降低饱和度到 1/3
        const secondaryPalette = ColorEngine.generateTonalPalette(h, s * 0.33);

        // Tertiary palette: hue 偏移 60°，保持饱和度的一半
        const tertiaryHue = (h + 60) % 360;
        const tertiaryPalette = ColorEngine.generateTonalPalette(tertiaryHue, s * 0.5);

        // Error palette: 固定红色 hue=25, 高饱和度
        const errorPalette = ColorEngine.generateTonalPalette(25, 84);

        // Neutral palette: 同 hue，极低饱和度
        const neutralPalette = ColorEngine.generateTonalPalette(h, s * 0.04);

        // Neutral Variant palette: 同 hue，低饱和度
        const neutralVariantPalette = ColorEngine.generateTonalPalette(h, s * 0.08);

        return {
            light: {
                'primary': primaryPalette[40],
                'on-primary': primaryPalette[100],
                'primary-container': primaryPalette[90],
                'on-primary-container': primaryPalette[10],
                'secondary': secondaryPalette[40],
                'on-secondary': secondaryPalette[100],
                'secondary-container': secondaryPalette[90],
                'on-secondary-container': secondaryPalette[10],
                'tertiary': tertiaryPalette[40],
                'on-tertiary': tertiaryPalette[100],
                'tertiary-container': tertiaryPalette[90],
                'on-tertiary-container': tertiaryPalette[10],
                'error': errorPalette[40],
                'on-error': errorPalette[100],
                'error-container': errorPalette[90],
                'on-error-container': errorPalette[10],
                'surface': neutralPalette[99],
                'on-surface': neutralPalette[10],
                'surface-variant': neutralVariantPalette[90],
                'on-surface-variant': neutralVariantPalette[30],
                'surface-container-lowest': neutralPalette[100],
                'surface-container-low': neutralPalette[96],
                'surface-container': neutralPalette[94],
                'surface-container-high': neutralPalette[92],
                'surface-container-highest': neutralPalette[90],
                'outline': neutralVariantPalette[50],
                'outline-variant': neutralVariantPalette[80],
                'inverse-surface': neutralPalette[20],
                'inverse-on-surface': neutralPalette[95],
                'inverse-primary': primaryPalette[80],
                'surface-tint': primaryPalette[40],
            },
            dark: {
                'primary': primaryPalette[80],
                'on-primary': primaryPalette[20],
                'primary-container': primaryPalette[30],
                'on-primary-container': primaryPalette[90],
                'secondary': secondaryPalette[80],
                'on-secondary': secondaryPalette[20],
                'secondary-container': secondaryPalette[30],
                'on-secondary-container': secondaryPalette[90],
                'tertiary': tertiaryPalette[80],
                'on-tertiary': tertiaryPalette[20],
                'tertiary-container': tertiaryPalette[30],
                'on-tertiary-container': tertiaryPalette[90],
                'error': errorPalette[80],
                'on-error': errorPalette[20],
                'error-container': errorPalette[30],
                'on-error-container': errorPalette[90],
                'surface': neutralPalette[6],
                'on-surface': neutralPalette[90],
                'surface-variant': neutralVariantPalette[30],
                'on-surface-variant': neutralVariantPalette[80],
                'surface-container-lowest': neutralPalette[4],
                'surface-container-low': neutralPalette[10],
                'surface-container': neutralPalette[12],
                'surface-container-high': neutralPalette[17],
                'surface-container-highest': neutralPalette[22],
                'outline': neutralVariantPalette[60],
                'outline-variant': neutralVariantPalette[30],
                'inverse-surface': neutralPalette[90],
                'inverse-on-surface': neutralPalette[20],
                'inverse-primary': primaryPalette[40],
                'surface-tint': primaryPalette[80],
            },
        };
    }

    /**
     * 将配色方案应用到 CSS 变量
     * @param {{light: Object, dark: Object}} scheme
     */
    static applyScheme(scheme) {
        const root = document.documentElement;
        const dataTheme = root.getAttribute('data-theme');
        const isDark =
            dataTheme === 'dark' ||
            (dataTheme === 'auto' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches);

        const colors = isDark ? scheme.dark : scheme.light;

        Object.entries(colors).forEach(([key, value]) => {
            root.style.setProperty(`--md-sys-color-${key}`, value);
        });

        // 保存方案到 sessionStorage 以便快速恢复
        sessionStorage.setItem('flavor-color-scheme', JSON.stringify(scheme));
    }

    /**
     * 从 sessionStorage 恢复配色方案
     */
    static restoreScheme() {
        const saved = sessionStorage.getItem('flavor-color-scheme');
        if (saved) {
            try {
                const scheme = JSON.parse(saved);
                ColorEngine.applyScheme(scheme);
            } catch (e) {
                /* ignore malformed data */
            }
        }
    }
}

// 页面加载时恢复配色
ColorEngine.restoreScheme();

// 监听主题切换事件，重新应用配色
document.addEventListener('themeChanged', () => {
    ColorEngine.restoreScheme();
});

// 暴露到全局
window.ColorEngine = ColorEngine;

// 初始化：如果有自定义种子色（非默认值），应用动态配色
document.addEventListener('DOMContentLoaded', () => {
    if (typeof flavorColorConfig !== 'undefined' && flavorColorConfig.seedColor) {
        const defaultSeed = '#6750A4';
        // 只有非默认种子色时才启用动态颜色（默认色已在 tokens.css 中定义）
        if (flavorColorConfig.seedColor.toLowerCase() !== defaultSeed.toLowerCase()) {
            ColorEngine.applyTheme(flavorColorConfig.seedColor);
        }
    }
});
