<?php
//Register theme support
function zinc_theme_support() {
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
}
add_action('after_setup_theme', 'zinc_theme_support');

// Adobe Fonts kit (New Hero) – used on the front end and in the block editor
define('ZINC_FONTS_URL', 'https://use.typekit.net/oce0aca.css');

//Register stylesheets
function zinc_register_styles() {
	$theme_version = wp_get_theme()->get('Version');
	wp_enqueue_style('adobe-fonts', ZINC_FONTS_URL, array(), null);
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', array(), $theme_version, false);
	wp_enqueue_style( 'slick-theme-css', get_template_directory_uri() . '/assets/css/slick-theme.css', array(), $theme_version );
	wp_enqueue_style('slick-css', get_template_directory_uri() . '/assets/css/slick.css', array(), $theme_version, false);
	wp_enqueue_style('main-css', get_template_directory_uri() . '/assets/css/main.css', array(), $theme_version);
	wp_enqueue_style('style-css', get_template_directory_uri() . '/assets/css/style.css', array(), $theme_version, false);
}
add_action('wp_enqueue_scripts', 'zinc_register_styles');

// Load the fonts inside the block editor too, so block previews match the site
function zinc_editor_fonts() {
	if (is_admin()) {
		wp_enqueue_style('adobe-fonts', ZINC_FONTS_URL, array(), null);
	}
}
add_action('enqueue_block_assets', 'zinc_editor_fonts');

// Block editor screen tweaks (e.g. ACF block fields in the right-hand panel)
function zinc_editor_admin_styles() {
	$path = '/assets/css/editor-admin.css';
	wp_enqueue_style('zinc-editor-admin', get_template_directory_uri() . $path, array(), filemtime(get_template_directory() . $path));
}
add_action('enqueue_block_editor_assets', 'zinc_editor_admin_styles');

// Connect to the font server early
function zinc_font_preconnect($urls, $relation_type) {
	if ($relation_type === 'preconnect') {
		$urls[] = array('href' => 'https://use.typekit.net', 'crossorigin');
		$urls[] = array('href' => 'https://p.typekit.net', 'crossorigin');
	}
	return $urls;
}
add_filter('wp_resource_hints', 'zinc_font_preconnect', 10, 2);


//Register scripts
function zinc_register_scripts() {
	$theme_version = wp_get_theme()->get('Version');
	wp_enqueue_script( 'boot', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), $theme_version, false);
	wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, false);
	wp_enqueue_script('slick-js', get_template_directory_uri() . '/assets/js/slick.min.js', array('jquery'), $theme_version, false);
	wp_enqueue_script( 'slick-slider', get_template_directory_uri() . '/assets/js/slick.js');
}
add_action('wp_enqueue_scripts', 'zinc_register_scripts');

//Register Menus
function zinc_menus() {
	$locations = array(
		'header'				=> __('Header Menu',	'zinc'),
		'header_secondary'		=> __('Header Secondary',	'zinc'),
		'header_mobile'			=> __('Header - Mobile',	'zinc'),
		'footer_nav_1'			=> __('Footer Nav 1',	'zinc'),
		'footer_nav_2'			=> __('Footer Nav 2',	'zinc'),
		'footer_legal'			=> __('Footer Legal',	'zinc')
	);

	register_nav_menus($locations);
}
add_action( 'init', 'zinc_menus' );



// Register widget areas
function register_vel_sidebars(){
	register_sidebar( array(
		'name'			=> 'Footer company info',
		'id'			=> 'footer_company_info',
		'before_widget'	=> '<div>',
		'after_widget'	=> '</div>',
	));
	register_sidebar( array(
		'name'			=> 'Footer Nav 1',
		'id'			=> 'footer_nav_1',
		'before_widget'	=> '<div>',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3>',
		'after_title'	=> '</h3>',
	));
	register_sidebar( array(
		'name'			=> 'Footer Nav 2',
		'id'			=> 'footer_nav_2',
		'before_widget'	=> '<div>',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3>',
		'after_title'	=> '</h3>',
	));
	register_sidebar( array(
		'name'			=> 'Footer contact info',
		'id'			=> 'footer_contact_info',
		'before_widget'	=> '<div>',
		'after_widget'	=> '</div>',
	));
}
add_action('widgets_init', 'register_vel_sidebars');



// Enable svg support
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

$filetype = wp_check_filetype( $filename, $mimes );
	return [
		'ext'             => $filetype['ext'],
		'type'            => $filetype['type'],
		'proper_filename' => $data['proper_filename']
	];
}, 10, 4 );
	
function cc_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );
	function fix_svg() {
	echo '<style type="text/css">
			.attachment-266x266, .thumbnail img {
				width: 100% !important;
				height: auto !important;
			}
			</style>';
	}
add_action( 'admin_head', 'fix_svg' );


// Clear pre fill customer details bug
add_filter('woocommerce_checkout_get_value','__return_empty_string', 1, 1);


// Call posts news
/*
add_action( 'init', 'news_register_taxonomy_for_object_type' );
function news_register_taxonomy_for_object_type() {
    register_taxonomy_for_object_type( 'post_tag', 'news' );
}; */

include_once __DIR__ . '/inc/post-types.php';
include_once __DIR__ . '/inc/register-blocks.php';
include_once __DIR__ . '/inc/video-helpers.php';
include_once __DIR__ . '/inc/default-media.php';
include_once __DIR__ . '/inc/block-defaults.php';
include_once __DIR__ . '/inc/taxonomies.php';