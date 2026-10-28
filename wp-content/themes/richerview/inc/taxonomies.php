<?php
/**
 * Custom taxonomies
 */

/**
 * Work categories – the services a project falls under (film, events…).
 * Used for the filter on the Our work page and the "services" pills on each project.
 */
function zinc_register_work_categories() {
	register_taxonomy('work_category', array('work'), array(
		'labels'			=> array(
			'name'			=> __('Categories', 'zinc'),
			'singular_name'	=> __('Category', 'zinc'),
			'menu_name'		=> __('Categories', 'zinc'),
			'all_items'		=> __('All categories', 'zinc'),
			'edit_item'		=> __('Edit category', 'zinc'),
			'add_new_item'	=> __('Add new category', 'zinc'),
			'new_item_name'	=> __('New category name', 'zinc'),
			'search_items'	=> __('Search categories', 'zinc'),
			'not_found'		=> __('No categories found.', 'zinc'),
		),
		'hierarchical'		=> true, // tick boxes in the editor
		'public'			=> true,
		'publicly_queryable'	=> false, // no archive pages – the Our work page filters by category
		'rewrite'			=> false,
		'show_in_rest'		=> true,
		'show_admin_column'	=> true,
	));
}
add_action('init', 'zinc_register_work_categories');

// Start with the categories from the design (only added once – they can be renamed or removed after)
function zinc_add_default_work_categories() {
	if (get_option('zinc_work_categories_added')) return;
	foreach (array('film', 'events', 'content', 'broadcast', 'podcasts', 'crew') as $name) {
		if (!term_exists($name, 'work_category')) {
			wp_insert_term($name, 'work_category');
		}
	}
	update_option('zinc_work_categories_added', 1);
}
add_action('init', 'zinc_add_default_work_categories', 30);
