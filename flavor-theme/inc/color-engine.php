<?php
/**
 * Flavor Theme — PHP Color Engine
 * 将 JS color-engine.js 的核心算法移植到 PHP，用于服务端生成 M3 颜色 tokens。
 * 消除页面加载时的颜色闪烁（FOUC）。
 */

/**
 * Hex 转 HSL
 *
 * @param string $hex 如 '#8a5e00'
 * @return array [h => 0-360, s => 0-100, l => 0-100]
 */
function flavor_hex_to_hsl($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;

    $max   = max($r, $g, $b);
    $min   = min($r, $g, $b);
    $delta = $max - $min;

    $h = 0;
    $s = 0;
    $l = ($max + $min) / 2;

    if ($delta != 0) {
        $s = $l > 0.5
            ? $delta / (2 - $max - $min)
            : $delta / ($max + $min);

        if ($max === $r) {
            $h = (($g - $b) / $delta + ($g < $b ? 6 : 0)) * 60;
        } elseif ($max === $g) {
            $h = (($b - $r) / $delta + 2) * 60;
        } else {
            $h = (($r - $g) / $delta + 4) * 60;
        }
    }

    return [
        'h' => round($h * 10) / 10,
        's' => round($s * 1000) / 10,
        'l' => round($l * 1000) / 10,
    ];
}

/**
 * HSL 转 Hex
 *
 * @param float $h 0-360
 * @param float $s 0-100
 * @param float $l 0-100
 * @return string 如 '#8a5e00'
 */
function flavor_hsl_to_hex($h, $s, $l) {
    $sn = $s / 100;
    $ln = $l / 100;

    $c = (1 - abs(2 * $ln - 1)) * $sn;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $ln - $c / 2;

    if ($h < 60) {
        $rn = $c; $gn = $x; $bn = 0;
    } elseif ($h < 120) {
        $rn = $x; $gn = $c; $bn = 0;
    } elseif ($h < 180) {
        $rn = 0; $gn = $c; $bn = $x;
    } elseif ($h < 240) {
        $rn = 0; $gn = $x; $bn = $c;
    } elseif ($h < 300) {
        $rn = $x; $gn = 0; $bn = $c;
    } else {
        $rn = $c; $gn = 0; $bn = $x;
    }

    $clamp = function ($v) {
        return max(0, min(255, round($v)));
    };

    return sprintf(
        '#%02x%02x%02x',
        $clamp(($rn + $m) * 255),
        $clamp(($gn + $m) * 255),
        $clamp(($bn + $m) * 255)
    );
}

/**
 * 生成 Tonal Palette
 *
 * @param float $hue 0-360
 * @param float $saturation 0-100
 * @return array [tone => hex]
 */
function flavor_generate_tonal_palette($hue, $saturation) {
    $tones = [0, 4, 6, 10, 12, 17, 20, 22, 24, 25, 30, 35, 40, 50, 60, 70, 80, 87, 90, 92, 94, 95, 96, 98, 99, 100];
    $palette = [];

    foreach ($tones as $tone) {
        $adjusted_sat = $saturation;

        if ($tone <= 10) {
            $adjusted_sat = $saturation * ($tone / 10);
        } elseif ($tone >= 90) {
            $adjusted_sat = $saturation * ((100 - $tone) / 10);
        }

        $adjusted_sat = max(0, min(100, $adjusted_sat));
        $palette[$tone] = flavor_hsl_to_hex($hue, $adjusted_sat, $tone);
    }

    return $palette;
}

/**
 * 从种子色生成完整 M3 配色方案
 *
 * @param string $seed_hex 种子色 hex
 * @return array ['light' => [...], 'dark' => [...]]
 */
function flavor_generate_scheme($seed_hex) {
    $hsl = flavor_hex_to_hsl($seed_hex);
    $h = $hsl['h'];
    $s = $hsl['s'];

    $primary   = flavor_generate_tonal_palette($h, $s);
    $secondary = flavor_generate_tonal_palette($h, $s * 0.33);
    $tertiary  = flavor_generate_tonal_palette(fmod($h + 60, 360), $s * 0.5);
    $error     = flavor_generate_tonal_palette(25, 84);
    $neutral   = flavor_generate_tonal_palette($h, $s * 0.04);
    $nv        = flavor_generate_tonal_palette($h, $s * 0.08); // neutral variant

    return [
        'light' => [
            'primary'                    => $primary[40],
            'on-primary'                 => $primary[100],
            'primary-container'          => $primary[90],
            'on-primary-container'       => $primary[10],
            'secondary'                  => $secondary[40],
            'on-secondary'               => $secondary[100],
            'secondary-container'        => $secondary[90],
            'on-secondary-container'     => $secondary[10],
            'tertiary'                   => $tertiary[40],
            'on-tertiary'                => $tertiary[100],
            'tertiary-container'         => $tertiary[90],
            'on-tertiary-container'      => $tertiary[10],
            'error'                      => $error[40],
            'on-error'                   => $error[100],
            'error-container'            => $error[90],
            'on-error-container'         => $error[10],
            'surface'                    => $neutral[99],
            'on-surface'                 => $neutral[10],
            'surface-variant'            => $nv[90],
            'on-surface-variant'         => $nv[30],
            'surface-container-lowest'   => $neutral[100],
            'surface-container-low'      => $neutral[96],
            'surface-container'          => $neutral[94],
            'surface-container-high'     => $neutral[92],
            'surface-container-highest'  => $neutral[90],
            'outline'                    => $nv[50],
            'outline-variant'            => $nv[80],
            'inverse-surface'            => $neutral[20],
            'inverse-on-surface'         => $neutral[95],
            'inverse-primary'            => $primary[80],
            'surface-tint'               => $primary[40],
        ],
        'dark' => [
            'primary'                    => $primary[80],
            'on-primary'                 => $primary[20],
            'primary-container'          => $primary[30],
            'on-primary-container'       => $primary[90],
            'secondary'                  => $secondary[80],
            'on-secondary'               => $secondary[20],
            'secondary-container'        => $secondary[30],
            'on-secondary-container'     => $secondary[90],
            'tertiary'                   => $tertiary[80],
            'on-tertiary'                => $tertiary[20],
            'tertiary-container'         => $tertiary[30],
            'on-tertiary-container'      => $tertiary[90],
            'error'                      => $error[80],
            'on-error'                   => $error[20],
            'error-container'            => $error[30],
            'on-error-container'         => $error[90],
            'surface'                    => $neutral[6],
            'on-surface'                 => $neutral[90],
            'surface-variant'            => $nv[30],
            'on-surface-variant'         => $nv[80],
            'surface-container-lowest'   => $neutral[4],
            'surface-container-low'      => $neutral[10],
            'surface-container'          => $neutral[12],
            'surface-container-high'     => $neutral[17],
            'surface-container-highest'  => $neutral[22],
            'outline'                    => $nv[60],
            'outline-variant'            => $nv[30],
            'inverse-surface'            => $neutral[90],
            'inverse-on-surface'         => $neutral[20],
            'inverse-primary'            => $primary[40],
            'surface-tint'               => $primary[80],
        ],
    ];
}

/**
 * 生成颜色覆盖 CSS（覆盖 tokens.css 中的默认紫色）
 * 包含 :root（light）、[data-theme="dark"]、@media auto dark 三个块。
 * 使用 WordPress transient 缓存结果。
 *
 * @param string $seed_hex 种子色
 * @return string CSS 字符串，如果种子色是默认值则返回空
 */
function flavor_generate_color_override_css($seed_hex) {
    $default_seed = '#6750A4';
    if (strcasecmp($seed_hex, $default_seed) === 0) {
        return ''; // 默认种子色无需覆盖
    }

    // 尝试从缓存读取
    $cache_key  = 'flavor_color_css_' . md5($seed_hex);
    $cached_css = get_transient($cache_key);
    if ($cached_css !== false) {
        return $cached_css;
    }

    $scheme = flavor_generate_scheme($seed_hex);

    // 生成 CSS 变量声明
    $build_vars = function ($colors) {
        $lines = [];
        foreach ($colors as $key => $value) {
            $lines[] = "  --md-sys-color-{$key}: {$value};";
        }
        return implode("\n", $lines);
    };

    $light_vars = $build_vars($scheme['light']);
    $dark_vars  = $build_vars($scheme['dark']);

    $css = <<<CSS
:root {
{$light_vars}
}
[data-theme="dark"] {
{$dark_vars}
}
@media (prefers-color-scheme: dark) {
  [data-theme="auto"] {
{$dark_vars}
  }
}
CSS;

    // 缓存 1 年（种子色变更时会刷新）
    set_transient($cache_key, $css, YEAR_IN_SECONDS);

    return $css;
}

/**
 * 种子色变更时清除颜色 CSS 缓存
 */
function flavor_clear_color_cache() {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_flavor_color_css_%' OR option_name LIKE '_transient_timeout_flavor_color_css_%'"
    );
}
add_action('customize_save_after', 'flavor_clear_color_cache');
