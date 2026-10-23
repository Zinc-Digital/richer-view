<?php
/**
 * Default media
 *
 * Turns images bundled with the theme into Media Library items, so they can be
 * used as default content in ACF image fields (which need an attachment ID).
 * Each image is only added once – after that the existing item is reused,
 * found by the `_zinc_theme_image` meta key.
 */

/**
 * Get the attachment ID for an image bundled with the theme, adding it to the
 * Media Library the first time if needed.
 *
 * @param string $path  Path relative to the theme folder, e.g. 'assets/images/client-logos/dpd.png'.
 * @param string $title Media Library title.
 * @param string $alt   Alt text.
 * @return int Attachment ID, or 0 if it doesn't exist and can't be added (e.g. no upload permission).
 */
function zinc_get_theme_image_attachment($path, $title = '', $alt = '') {
	$existing = get_posts(array(
		'post_type'			=> 'attachment',
		'post_status'		=> 'inherit',
		'posts_per_page'	=> 1,
		'fields'			=> 'ids',
		'meta_key'			=> '_zinc_theme_image',
		'meta_value'		=> $path,
	));
	if ($existing) {
		return (int) $existing[0];
	}

	$file = get_template_directory() . '/' . ltrim($path, '/');
	if (!file_exists($file) || !current_user_can('upload_files')) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// media_handle_sideload() moves the file, so give it a temporary copy
	$tmp = wp_tempnam(basename($file));
	if (!$tmp || !copy($file, $tmp)) {
		return 0;
	}

	$id = media_handle_sideload(array('name' => basename($file), 'tmp_name' => $tmp), 0, $title);
	if (is_wp_error($id)) {
		@unlink($tmp);
		return 0;
	}

	update_post_meta($id, '_zinc_theme_image', $path);
	if ($alt) {
		update_post_meta($id, '_wp_attachment_image_alt', $alt);
	}

	return (int) $id;
}
