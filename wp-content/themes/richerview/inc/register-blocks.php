<?php 
//Theme Options
if(function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

//Register block.json blocks (template-parts/blocks/{block-name}/block.json)
function register_theme_blocks() {
	// Shared scripts that block view scripts can list as dependencies (in their .asset.php)
	$shared_scripts = array(
		'zinc-cursor-badge'	=> array('/assets/js/components/cursor-badge.js', array()),
		'zinc-drag-slider'	=> array('/assets/js/components/drag-slider.js', array('zinc-cursor-badge')),
		'zinc-accordion'	=> array('/assets/js/components/accordion.js', array()),
		'zinc-background-video'	=> array('/assets/js/components/background-video.js', array()),
	);
	foreach ($shared_scripts as $handle => list($path, $deps)) {
		wp_register_script($handle, get_template_directory_uri() . $path, $deps, filemtime(get_template_directory() . $path), true);
	}

	$blocks = array(
		'client-logos',
		'services-showcase',
		'testimonials',
		'accordion-image',
		'intro-image',
		'heading-text',
		'stats',
		'team-slider',
		'faq',
		'contact',
		'service-hero',
		'project-list',
		'work-hero',
		'image-gallery',
		'video-feature',
		'quote',
		'vacancy-list',
		'social-feed',
	);

	foreach ($blocks as $block) {
		$block_type = register_block_type(get_template_directory() . '/template-parts/blocks/' . $block);
		if ($block_type) {
			version_block_assets($block_type);
		}
	}
}
add_action('init', 'register_theme_blocks');

// Version block CSS/JS by file modification time so browsers pick up changes straight away
function version_block_assets($block_type) {
	$assets = array(
		array(wp_styles(), array_merge($block_type->style_handles, $block_type->editor_style_handles)),
		array(wp_scripts(), array_merge($block_type->view_script_handles, $block_type->editor_script_handles, $block_type->script_handles)),
	);

	foreach ($assets as list($registry, $handles)) {
		foreach ($handles as $handle) {
			if (empty($registry->registered[$handle]->src)) continue;

			$path = str_replace(get_template_directory_uri(), get_template_directory(), strtok($registry->registered[$handle]->src, '?'));
			if (file_exists($path)) {
				$registry->registered[$handle]->ver = (string) filemtime($path);
			}
		}
	}
}


add_filter( 'acf/the_field/escape_html_optin', '__return_true' );
add_action( 'acf/init', 'set_acf_settings' );
function set_acf_settings() {
    acf_update_setting( 'enable_shortcode', false );
}
?>