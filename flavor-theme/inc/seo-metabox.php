<?php
/**
 * Flavor Theme — SEO Meta Box
 * 文章/页面编辑器中的 SEO 自定义字段
 */

// 注册 Meta Box
function flavor_seo_add_meta_box() {
    $screens = ['post', 'page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'flavor_seo_meta_box',
            __('SEO 设置', 'flavor'),
            'flavor_seo_meta_box_html',
            $screen,
            'normal',
            'low'
        );
    }
}
add_action('add_meta_boxes', 'flavor_seo_add_meta_box');

// Meta Box HTML
function flavor_seo_meta_box_html($post) {
    wp_nonce_field('flavor_seo_meta_box', 'flavor_seo_nonce');

    $seo_title = get_post_meta($post->ID, '_flavor_seo_title', true);
    $seo_desc  = get_post_meta($post->ID, '_flavor_seo_desc', true);
    $seo_image = get_post_meta($post->ID, '_flavor_seo_image', true);

    // 自动生成的预览值
    $auto_title = $post->post_title . ' - ' . get_bloginfo('name');
    $auto_desc  = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 50, '...');
    ?>
    <style>
        .flavor-seo-field { margin-bottom: 16px; }
        .flavor-seo-field label { display: block; font-weight: 600; margin-bottom: 4px; }
        .flavor-seo-field input[type="text"],
        .flavor-seo-field textarea { width: 100%; }
        .flavor-seo-field .description { color: #666; font-size: 12px; margin-top: 4px; }
        .flavor-seo-field .char-count { float: right; font-size: 12px; color: #999; }
        .flavor-seo-preview { background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-top: 16px; }
        .flavor-seo-preview__title { color: #1a0dab; font-size: 18px; line-height: 1.3; margin-bottom: 4px; cursor: pointer; }
        .flavor-seo-preview__url { color: #006621; font-size: 13px; margin-bottom: 4px; }
        .flavor-seo-preview__desc { color: #545454; font-size: 13px; line-height: 1.4; }
        .flavor-seo-image-wrap { display: flex; align-items: flex-start; gap: 12px; }
        .flavor-seo-image-preview { max-width: 120px; max-height: 80px; border-radius: 4px; border: 1px solid #ddd; }
        .flavor-seo-image-preview[src=""] { display: none; }
    </style>

    <div class="flavor-seo-field">
        <label for="flavor_seo_title">
            <?php esc_html_e('SEO 标题', 'flavor'); ?>
            <span class="char-count"><span id="flavor-title-count"><?php echo mb_strlen($seo_title); ?></span>/60</span>
        </label>
        <input type="text" id="flavor_seo_title" name="flavor_seo_title"
               value="<?php echo esc_attr($seo_title); ?>"
               placeholder="<?php echo esc_attr($auto_title); ?>"
               maxlength="120">
        <p class="description"><?php esc_html_e('留空则自动使用「文章标题 - 站点名称」', 'flavor'); ?></p>
    </div>

    <div class="flavor-seo-field">
        <label for="flavor_seo_desc">
            <?php esc_html_e('SEO 描述', 'flavor'); ?>
            <span class="char-count"><span id="flavor-desc-count"><?php echo mb_strlen($seo_desc); ?></span>/160</span>
        </label>
        <textarea id="flavor_seo_desc" name="flavor_seo_desc" rows="3"
                  placeholder="<?php echo esc_attr(mb_substr($auto_desc, 0, 160)); ?>"
                  maxlength="300"><?php echo esc_textarea($seo_desc); ?></textarea>
        <p class="description"><?php esc_html_e('留空则自动从摘要或内容中截取', 'flavor'); ?></p>
    </div>

    <div class="flavor-seo-field">
        <label><?php esc_html_e('社交分享图片 (OG Image)', 'flavor'); ?></label>
        <div class="flavor-seo-image-wrap">
            <input type="hidden" id="flavor_seo_image" name="flavor_seo_image" value="<?php echo esc_attr($seo_image); ?>">
            <img id="flavor-seo-image-preview" class="flavor-seo-image-preview" src="<?php echo $seo_image ? esc_url($seo_image) : ''; ?>" alt="">
            <div>
                <button type="button" class="button" id="flavor-seo-image-btn"><?php esc_html_e('选择图片', 'flavor'); ?></button>
                <button type="button" class="button" id="flavor-seo-image-remove" <?php echo $seo_image ? '' : 'style="display:none"'; ?>><?php esc_html_e('移除', 'flavor'); ?></button>
            </div>
        </div>
        <p class="description"><?php esc_html_e('留空则自动使用特色图片，推荐尺寸 1200×630', 'flavor'); ?></p>
    </div>

    <!-- Google 搜索预览 -->
    <div class="flavor-seo-preview">
        <p style="margin:0 0 8px;font-weight:600;font-size:12px;color:#999;"><?php esc_html_e('Google 搜索预览', 'flavor'); ?></p>
        <div class="flavor-seo-preview__title" id="flavor-preview-title"><?php echo esc_html($seo_title ?: $auto_title); ?></div>
        <div class="flavor-seo-preview__url"><?php echo esc_html(get_permalink($post->ID)); ?></div>
        <div class="flavor-seo-preview__desc" id="flavor-preview-desc"><?php echo esc_html(mb_substr($seo_desc ?: $auto_desc, 0, 160)); ?></div>
    </div>

    <script>
    jQuery(function($) {
        var autoTitle = <?php echo wp_json_encode($auto_title); ?>;
        var autoDesc = <?php echo wp_json_encode(mb_substr($auto_desc, 0, 160)); ?>;

        // 字数统计 + 实时预览
        $('#flavor_seo_title').on('input', function() {
            var val = $(this).val();
            $('#flavor-title-count').text(val.length);
            $('#flavor-preview-title').text(val || autoTitle);
        });
        $('#flavor_seo_desc').on('input', function() {
            var val = $(this).val();
            $('#flavor-desc-count').text(val.length);
            $('#flavor-preview-desc').text((val || autoDesc).substring(0, 160));
        });

        // 媒体库选择器
        var frame;
        $('#flavor-seo-image-btn').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '<?php echo esc_js(__('选择 OG 图片', 'flavor')); ?>',
                button: { text: '<?php echo esc_js(__('使用此图片', 'flavor')); ?>' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $('#flavor_seo_image').val(url);
                $('#flavor-seo-image-preview').attr('src', url).show();
                $('#flavor-seo-image-remove').show();
            });
            frame.open();
        });
        $('#flavor-seo-image-remove').on('click', function() {
            $('#flavor_seo_image').val('');
            $('#flavor-seo-image-preview').attr('src', '').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}

// 保存 Meta Box 数据
function flavor_seo_save_meta_box($post_id) {
    if (!isset($_POST['flavor_seo_nonce'])) return;
    if (!wp_verify_nonce($_POST['flavor_seo_nonce'], 'flavor_seo_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'flavor_seo_title' => 'sanitize_text_field',
        'flavor_seo_desc'  => 'sanitize_textarea_field',
        'flavor_seo_image' => 'esc_url_raw',
    ];

    foreach ($fields as $key => $sanitize) {
        $value = isset($_POST[$key]) ? call_user_func($sanitize, $_POST[$key]) : '';
        $meta_key = '_' . $key;
        if ($value) {
            update_post_meta($post_id, $meta_key, $value);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
}
add_action('save_post', 'flavor_seo_save_meta_box');
