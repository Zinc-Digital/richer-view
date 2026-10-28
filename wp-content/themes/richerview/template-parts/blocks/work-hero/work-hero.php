<?php
/**
 * Work Hero block
 *
 * Full-width photo or background video at the top of a project page, with the
 * title and the project's details (client logo or name, year and services)
 * over the bottom left. The details come from the project itself: the
 * "Project details" box in the editor sidebar and its categories.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$post_id  = get_the_ID();
$position = get_field('image_position');
$position = in_array($position, array('top', 'bottom'), true) ? $position : 'center';

// Heading: this block's own, or the project title (a new, unsaved project has none yet)
$heading = get_field('heading');
if (!$heading && get_post_status($post_id) !== 'auto-draft') {
	$heading = get_the_title($post_id);
}
if (!$heading && $is_preview) {
	$heading = __('Project title', 'zinc');
}

// Photo, or the project's listing image if none is chosen
$image = get_field('image') ?: get_post_thumbnail_id($post_id);

// Optional muted looping video instead of the image – it shows straight away
$video_embed = '';
$video_file  = '';
if (get_field('field_6af3b4c5d3020') === 'video') {
	$video_type = get_field('video_type');
	if (in_array($video_type, array('youtube', 'vimeo'), true)) {
		$video_embed = zinc_get_background_video_embed_url(get_field('video_url'), $video_type);
	} elseif ($video_type === 'file') {
		$file       = get_field('video_file');
		$video_file = $file['url'] ?? '';
	} elseif ($video_type === 'link') {
		$video_file = zinc_get_video_file_link(get_field('video_link'));
	}
	if ($video_embed || $video_file) {
		$image = null;
	}
}

// Project details
$details = array();
if (get_field('field_6af3b4c5d3003') !== false) {
	$client_name = get_field('client_name', $post_id);
	$client_logo = get_field('client_logo', $post_id);
	if ($client_logo || $client_name) {
		$details['client'] = $client_logo
			? wp_get_attachment_image($client_logo, 'medium', false, array('class' => 'b-work-hero__logo', 'alt' => $client_name ?: '', 'loading' => 'eager'))
			: '<span class="b-work-hero__value">' . esc_html($client_name) . '</span>';
	}
	if ($year = get_field('year', $post_id)) {
		$details['year'] = '<span class="b-work-hero__value b-work-hero__value--bold">' . esc_html($year) . '</span>';
	}
	$terms = get_the_terms($post_id, 'work_category');
	if ($terms && !is_wp_error($terms)) {
		// Each service links to the Our work page, showing just that category
		$listing = get_page_by_path('our-work');
		$pills = '';
		foreach ($terms as $term) {
			$pills .= $listing
				? '<a class="b-work-hero__tag" href="' . esc_url(add_query_arg('category', $term->slug, get_permalink($listing))) . '">' . esc_html($term->name) . '</a>'
				: '<span class="b-work-hero__tag">' . esc_html($term->name) . '</span>';
		}
		$details['services'] = '<span class="b-work-hero__tags">' . $pills . '</span>';
	}
	if (!$details && $is_preview) {
		$details['client'] = '<span class="b-work-hero__value">' . esc_html__('Add the client and year in the “Project details” box in the sidebar.', 'zinc') . '</span>';
	}
}
$labels = array(
	'client'	=> get_field('client_label') ?: __('Client:', 'zinc'),
	'year'		=> get_field('year_label') ?: __('Year:', 'zinc'),
	'services'	=> get_field('services_label') ?: __('Services:', 'zinc'),
);

$wrapper_attributes = array(
	'class' => 'b-work-hero',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-work-hero__media">
    <?php if ($image) {
			echo wp_get_attachment_image($image, 'full', false, array(
				'class'		=> 'b-work-hero__image b-work-hero__image--' . $position,
				'sizes'		=> '100vw',
				'loading'	=> 'eager',
				'alt'		=> '',
			));
		} ?>

    <?php if ($video_embed) : ?>
    <div class="b-work-hero__video" data-video-embed="<?php echo esc_url($video_embed); ?>" aria-hidden="true"><?php zinc_background_video_iframe($video_embed); ?></div>
    <?php elseif ($video_file) : ?>
    <div class="b-work-hero__video" aria-hidden="true">
      <video src="<?php echo esc_url($video_file); ?>" muted loop playsinline autoplay preload="auto" tabindex="-1"></video>
    </div>
    <?php endif; ?>
  </div>

  <div class="b-work-hero__content">
    <?php if ($is_preview && !$image && !$video_embed && !$video_file) : ?>
    <p class="b-work-hero__value"><?php esc_html_e('Add a photo or video in the block settings (Media tab), or set the project’s listing image.', 'zinc'); ?></p>
    <?php endif; ?>

    <?php if ($heading) : ?>
    <h1 class="b-work-hero__heading"><?php echo nl2br(esc_html($heading), false); ?></h1>
    <?php endif; ?>

    <?php if ($details) : ?>
    <dl class="b-work-hero__details">
      <?php foreach ($details as $key => $value) : ?>
      <div class="b-work-hero__detail b-work-hero__detail--<?php echo esc_attr($key); ?>">
        <dt class="b-work-hero__label"><?php echo esc_html($labels[$key]); ?></dt>
        <dd class="b-work-hero__data"><?php echo $value; ?></dd>
      </div>
      <?php endforeach; ?>
    </dl>
    <?php endif; ?>
  </div>
</section>
