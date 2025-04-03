<?php

define('iminimal_VERSION', 1.1);

// لیست فونت‌های قابل انتخاب
function iminimal_available_fonts() {
    return [
        'vazir' => 'Vazir',
        'vazircode' => 'Vazir Code',
        'gandom' => 'Gandom',
        'nahid' => 'Nahid',
        'parastoo' => 'Parastoo',
        'sahel' => 'Sahel',
        'shabnam' => 'Shabnam',
        'tanha' => 'Tanha',
    ];
}

// فعال‌سازی ویژگی‌های قالب
function iminimal_theme_setup() {
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');
	add_image_size('featured-thumb', 1200, 600, true);
	add_image_size('medium', 160, 180, true);
	add_theme_support('title-tag');
	add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);

	register_nav_menus([
		'primary' => __('Primary Menu', 'iminimal'),
	]);
}
add_action('after_setup_theme', 'iminimal_theme_setup');

// بارگذاری استایل‌ها بر اساس تنظیمات
function iminimal_enqueue_styles() {
	if (is_rtl()) {
		if (get_option('enable_custom_font', 0)) {
			wp_enqueue_style('iminimal-fonts', get_template_directory_uri() . '/css/rtl-fonts.css', [], iminimal_VERSION);
		}
	} else {
		wp_enqueue_style('iminimal-style', get_template_directory_uri() . '/style.css', [], iminimal_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'iminimal_enqueue_styles');

// افزودن کلاس فونت به body
add_filter('body_class', function($classes) {
	if (get_option('enable_custom_font', 0)) {
		$classes[] = 'enable-custom-font';
		$font = get_option('selected_font_family', 'Vazir');
		$classes[] = 'font-' . esc_attr($font);
	}
	return $classes;
});

// تنظیمات قالب در پیشخوان
function iminimal_add_theme_settings_page() {
	add_theme_page('تنظیمات قالب', 'تنظیمات قالب', 'manage_options', 'font-settings', 'iminimal_render_theme_settings_page');
}
add_action('admin_menu', 'iminimal_add_theme_settings_page');

// محتوای صفحه تنظیمات
function iminimal_render_theme_settings_page() {
	if (isset($_POST['submit'])) {
		check_admin_referer('iminimal_theme_settings_save', 'iminimal_theme_settings_nonce');
		update_option('enable_custom_font', isset($_POST['enable_custom_font']) ? 1 : 0);
		update_option('show_read_more', isset($_POST['show_read_more']) ? 1 : 0);
		update_option('selected_font_family', sanitize_text_field($_POST['selected_font_family']));
		update_option('show_header_box', isset($_POST['show_header_box']) ? 1 : 0);
		echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
	}

	$enable_custom_font = get_option('enable_custom_font', 0);
	$show_read_more     = get_option('show_read_more', 0);
	$show_header_box    = get_option('show_header_box', 1);
	$current_font       = get_option('selected_font_family', 'Vazir');
	$fonts              = iminimal_available_fonts();
	?>
	<div class="wrap">
		<h1>تنظیمات قالب</h1>
		<form method="post">
			<?php wp_nonce_field('iminimal_theme_settings_save', 'iminimal_theme_settings_nonce'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">فونت سفارشی</th>
					<td><input type="checkbox" name="enable_custom_font" <?php checked($enable_custom_font, 1); ?> /> فعال‌سازی فونت دلخواه</td>
				</tr>
				<tr>
					<th scope="row">انتخاب فونت</th>
					<td>
						<select name="selected_font_family">
							<?php foreach ($fonts as $key => $label): ?>
								<option value="<?php echo esc_attr($key); ?>" <?php selected($current_font, $key); ?>>
									<?php echo esc_html($label); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">نمایش باکس هدر</th>
					<td><input type="checkbox" name="show_header_box" <?php checked($show_header_box, 1); ?> /> نمایش هدر کلاسیک (آواتار و اطلاعات)</td>
				</tr>
				<tr>
					<th scope="row">لینک ادامه مطلب</th>
					<td><input type="checkbox" name="show_read_more" <?php checked($show_read_more, 1); ?> /> نمایش در لیست نوشته‌ها</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// ثبت ابزارک‌های فوتر
function iminimal_register_footer_widgets() {
	for ($i = 1; $i <= 3; $i++) {
		register_sidebar([
			'name' => "Footer $i",
			'id' => "footer-$i",
			'before_widget' => '<div class="widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]);
	}
	register_sidebar([
		'name' => 'Footer Bottom Widget',
		'id' => 'footer-bottom-widget',
		'before_widget' => '<div class="widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	]);
}
add_action('widgets_init', 'iminimal_register_footer_widgets');

// ساختار سفارشی دیدگاه‌ها
function iminimal_theme_comment($comment, $args, $depth) {
	$tag = ('div' === $args['style']) ? 'div' : 'li';
	$author_id = $comment->user_id;
	$is_admin = user_can($author_id, 'manage_options');
	$class = $is_admin ? 'admin-comment' : 'visitor-comment';

	?>
	<<?php echo tag_escape( $tag ); ?> <?php comment_class($class); ?> id="comment-<?php comment_ID(); ?>">
		<div class="comment-body">
			<div class="comment-avatar">
				<?php echo get_avatar($comment, $args['avatar_size']); ?>
			</div>
			<div class="comment-content">
				<div class="comment-author">
					<strong><?php echo get_comment_author_link(); ?></strong>
					<span class="comment-meta">
						<?php printf('%s در %s', get_comment_date(), get_comment_time()); ?>
						<?php edit_comment_link('ویرایش', ' · '); ?>
					</span>
				</div>
				<div class="comment-text"><?php comment_text(); ?></div>
				<div class="reply">
					<?php comment_reply_link(array_merge($args, [
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'],
						'reply_text' => 'پاسخ',
					])); ?>
				</div>
			</div>
		</div>
	<?php
}

// اسکریپت‌ها
function iminimal_enqueue_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('iminimal-theme-main', get_template_directory_uri() . '/js/theme.min.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'iminimal_enqueue_scripts');

// نمایش هشدار در صورت غیرفعال بودن افزونه آپدیت
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

add_action('admin_notices', function () {
	if (!is_plugin_active('iminimal-updater/iminimal-updater.php')) {
		echo '<div class="notice notice-error"><p>⚠️ برای دریافت بروزرسانی‌های قالب <strong>iMinimal</strong>، لطفاً افزونه <strong>iMinimal Updater</strong> را نصب و فعال کنید.</p></div>';
	}
});
