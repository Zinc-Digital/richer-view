<?php
/**
 * Custom post types
 */

/**
 * Services – one page per service (film, events, content…), built with blocks.
 * Pages sit under the "What we do" page's address, e.g. /what-we-do/film/.
 * A new service starts with the blocks from the service page design.
 */
function zinc_register_services() {
	register_post_type('service', array(
		'labels'		=> array(
			'name'					=> __('Services', 'zinc'),
			'singular_name'			=> __('Service', 'zinc'),
			'menu_name'				=> __('Services', 'zinc'),
			'add_new'				=> __('Add service', 'zinc'),
			'add_new_item'			=> __('Add new service', 'zinc'),
			'edit_item'				=> __('Edit service', 'zinc'),
			'new_item'				=> __('New service', 'zinc'),
			'view_item'				=> __('View service', 'zinc'),
			'view_items'			=> __('View services', 'zinc'),
			'search_items'			=> __('Search services', 'zinc'),
			'not_found'				=> __('No services found.', 'zinc'),
			'not_found_in_trash'	=> __('No services found in the bin.', 'zinc'),
			'all_items'				=> __('All services', 'zinc'),
			'item_published'		=> __('Service published.', 'zinc'),
			'item_updated'			=> __('Service updated.', 'zinc'),
		),
		'public'		=> true,
		'show_in_rest'	=> true, // block editor
		'menu_position'	=> 21,
		'menu_icon'		=> 'dashicons-video-alt3',
		'hierarchical'	=> false,
		'has_archive'	=> false, // the "What we do" page lists them
		'rewrite'		=> array('slug' => 'what-we-do', 'with_front' => false),
		'supports'		=> array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions'),
		'template'		=> array(
			array('acf/service-hero'),
			array('acf/accordion-image'),
			array('acf/project-list'),
			array('acf/faq'),
			array('acf/services-showcase', array('data' => array('heading_style' => 'compact', '_heading_style' => 'field_6a2c4e8f1b499'))),
		),
	));
}
add_action('init', 'zinc_register_services');

/**
 * Our Work – one page per project, built with blocks. Pages sit under the
 * "Our work" page's address, e.g. /our-work/first-bus-uniform-catwalk/, and
 * are grouped by category (inc/taxonomies.php) for the filter on the Our work page.
 * The project's client, year and listing image are set in the editor sidebar.
 */
function zinc_register_work() {
	register_post_type('work', array(
		'labels'		=> array(
			'name'					=> __('Our Work', 'zinc'),
			'singular_name'			=> __('Project', 'zinc'),
			'menu_name'				=> __('Our Work', 'zinc'),
			'add_new'				=> __('Add project', 'zinc'),
			'add_new_item'			=> __('Add new project', 'zinc'),
			'edit_item'				=> __('Edit project', 'zinc'),
			'new_item'				=> __('New project', 'zinc'),
			'view_item'				=> __('View project', 'zinc'),
			'view_items'			=> __('View projects', 'zinc'),
			'search_items'			=> __('Search projects', 'zinc'),
			'not_found'				=> __('No projects found.', 'zinc'),
			'not_found_in_trash'	=> __('No projects found in the bin.', 'zinc'),
			'all_items'				=> __('All projects', 'zinc'),
			'featured_image'		=> __('Listing image', 'zinc'),
			'set_featured_image'	=> __('Set listing image', 'zinc'),
			'remove_featured_image'	=> __('Remove listing image', 'zinc'),
			'use_featured_image'	=> __('Use as listing image', 'zinc'),
			'item_published'		=> __('Project published.', 'zinc'),
			'item_updated'			=> __('Project updated.', 'zinc'),
		),
		'public'		=> true,
		'show_in_rest'	=> true, // block editor
		'menu_position'	=> 22,
		'menu_icon'		=> 'dashicons-format-video',
		'hierarchical'	=> false,
		'has_archive'	=> false, // the "Our work" page lists them
		'rewrite'		=> array('slug' => 'our-work', 'with_front' => false),
		'supports'		=> array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
		'template'		=> array(
			array('acf/work-hero'),
			array('acf/heading-text', array('data' => array('heading' => 'the brief', '_heading' => 'field_6a6a8c0d5f002'))),
			array('acf/image-gallery'),
			array('acf/video-feature'),
			array('acf/quote'),
			array('acf/project-list', array('data' => array(
				'heading'		=> 'keep watching',
				'_heading'		=> 'field_6ad1f2a3b1010',
				'source'		=> 'work',
				'_source'		=> 'field_6ad1f2a3b1020',
				'limit'			=> 5,
				'_limit'		=> 'field_6ad1f2a3b1022',
				'divider'		=> 1,
				'_divider'		=> 'field_6ad1f2a3b1025',
			))),
		),
	));
}
add_action('init', 'zinc_register_work');

/**
 * Vacancies – open roles, listed on the Careers page (Vacancy List block) and
 * each with its own page, e.g. /careers/junior-editor/. A role's type, closing
 * date, summary and apply link are set in the editor sidebar; it drops off the
 * list after its closing date.
 */
function zinc_register_vacancies() {
	register_post_type('vacancy', array(
		'labels'		=> array(
			'name'					=> __('Vacancies', 'zinc'),
			'singular_name'			=> __('Vacancy', 'zinc'),
			'menu_name'				=> __('Vacancies', 'zinc'),
			'add_new'				=> __('Add vacancy', 'zinc'),
			'add_new_item'			=> __('Add new vacancy', 'zinc'),
			'edit_item'				=> __('Edit vacancy', 'zinc'),
			'new_item'				=> __('New vacancy', 'zinc'),
			'view_item'				=> __('View vacancy', 'zinc'),
			'view_items'			=> __('View vacancies', 'zinc'),
			'search_items'			=> __('Search vacancies', 'zinc'),
			'not_found'				=> __('No vacancies found.', 'zinc'),
			'not_found_in_trash'	=> __('No vacancies found in the bin.', 'zinc'),
			'all_items'				=> __('All vacancies', 'zinc'),
			'item_published'		=> __('Vacancy published.', 'zinc'),
			'item_updated'			=> __('Vacancy updated.', 'zinc'),
		),
		'public'		=> true,
		'show_in_rest'	=> true, // block editor
		'menu_position'	=> 23,
		'menu_icon'		=> 'dashicons-groups',
		'hierarchical'	=> false,
		'has_archive'	=> false, // the Careers page lists them
		'rewrite'		=> array('slug' => 'careers', 'with_front' => false),
		'supports'		=> array('title', 'editor', 'revisions'),
		'template'		=> array(
			array('acf/service-hero'),
			array('acf/heading-text', array('data' => array('heading' => 'the role', '_heading' => 'field_6a6a8c0d5f002'))),
			array('acf/contact', array('anchor' => 'apply', 'data' => array(
				'heading'			=> 'apply for this role',
				'_heading'			=> 'field_6abfd25eae002',
				'text'				=> 'Tell us a bit about yourself and send a link to your reel.',
				'_text'				=> 'field_6abfd25eae003',
				'show_details'		=> 0,
				'_show_details'		=> 'field_6abfd25eae004',
				'spacing'			=> 'standard',
				'_spacing'			=> 'field_6abfd25eae012',
				'heading_level'		=> 'h2',
				'_heading_level'	=> 'field_6abfd25eae013',
			))),
		),
	));
}
add_action('init', 'zinc_register_vacancies');

// Refresh permalinks once after the post types change, so the new addresses work straight away
function zinc_flush_post_type_rewrites() {
	$version = 'vacancies-1';
	if (get_option('zinc_post_types_version') !== $version) {
		flush_rewrite_rules(false);
		update_option('zinc_post_types_version', $version);
	}
}
add_action('init', 'zinc_flush_post_type_rewrites', 20);
