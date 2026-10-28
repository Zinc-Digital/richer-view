<?php
/**
 * Service Hero block
 *
 * Large heading with tag pills and intro text alongside, followed by a wide
 * photo or background video that grows to the full width of the screen as the
 * page is scrolled (assets/js/blocks/service-hero.js). The photo/video is
 * optional, so the block also works as a plain page header (e.g. Careers).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

// Heading: this block's own, or the page title (a new, unsaved page has none yet)
$heading = get_field('heading');
if (!$heading && get_post_status() !== 'auto-draft') {
	$heading = get_the_title();
}
if (!$heading && $is_preview) {
	$heading = __('service name', 'zinc');
}
$tags     = get_field('tags') ?: array();
// On a vacancy's page with no tags set, show its type and closing date
if (!$tags && is_singular('vacancy')) {
	$vacancy_id = get_the_ID();
	if ($type = get_field('type', $vacancy_id)) {
		$tags[] = array('label' => $type);
	}
	if ($closes = zinc_format_closing_date(get_field('closing_date', $vacancy_id))) {
		$tags[] = array('label' => sprintf(__('closes %s', 'zinc'), $closes));
	}
}
$text     = get_field('text');
$image    = get_field('image');
$position = get_field('image_position');
$expand   = get_field('field_6ac0e1f2a0010') !== false;
$spacing  = get_field('spacing') === 'standard' ? 'standard' : 'page-top';
$level    = get_field('heading_level') === 'h2' ? 'h2' : 'h1';

$position = in_array($position, array('top', 'bottom'), true) ? $position : 'center';

// Optional muted looping video (YouTube, Vimeo or upload) instead of the image – it shows straight away
$video_embed = '';
$video_file  = '';
if (get_field('field_6ac0e1f2a0020') === 'video') {
	$video_type = get_field('video_type');
	if (in_array($video_type, array('youtube', 'vimeo'), true)) {
		$video_embed = zinc_get_background_video_embed_url(get_field('video_url'), $video_type);
	} elseif ($video_type === 'file') {
		$file       = get_field('video_file');
		$video_file = $file['url'] ?? '';
	} elseif ($video_type === 'link') {
		// A direct link to a video file plays just like an upload
		$video_file = zinc_get_video_file_link(get_field('video_link'));
	}
	if ($video_embed || $video_file) {
		$image = null; // a video plays straight away – no image first
	}
}

$classes = 'b-service-hero';
if ($spacing === 'page-top') $classes .= ' b-service-hero--page-top';
// No photo or video (e.g. the Careers page, followed by an image row): no space below the text,
// the next block sets the gap
$has_media = $image || $video_embed || $video_file;
if (!$has_media) $classes .= ' b-service-hero--no-media';

$wrapper_attributes = array(
	'class' => $classes,
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-service-hero__header">
    <div class="b-service-hero__intro">
      <?php if ($heading) : ?>
      <<?php echo $level; ?> class="b-service-hero__heading"><?php echo esc_html($heading); ?></<?php echo $level; ?>>
      <?php endif; ?>

      <?php if ($tags) : ?>
      <ul class="b-service-hero__tags">
        <?php foreach ($tags as $tag) : if (empty($tag['label'])) continue; ?>
        <li class="b-service-hero__tag"><?php echo esc_html($tag['label']); ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

    <?php if ($text) : ?>
    <div class="b-service-hero__text"><?php echo wp_kses(wpautop($text), array('p' => array(), 'br' => array())); ?></div>
    <?php endif; ?>
  </div>

  <?php if ($image || $video_embed || $video_file) : ?>
  <div class="b-service-hero__media"<?php if ($expand) echo ' data-expand'; ?>>
    <div class="b-service-hero__frame">
      <?php if ($image) {
				echo wp_get_attachment_image($image, 'full', false, array(
					'class'		=> 'b-service-hero__image b-service-hero__image--' . $position,
					'sizes'		=> '100vw',
					'loading'	=> $spacing === 'page-top' ? 'eager' : 'lazy',
				));
			} ?>

      <?php if ($video_embed) : ?>
      <div class="b-service-hero__video" data-video-embed="<?php echo esc_url($video_embed); ?>" aria-hidden="true"><?php zinc_background_video_iframe($video_embed); ?></div>
      <?php elseif ($video_file) : ?>
      <div class="b-service-hero__video" aria-hidden="true">
        <video src="<?php echo esc_url($video_file); ?>" muted loop playsinline autoplay preload="auto" tabindex="-1"></video>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php elseif ($is_preview && get_field('field_6ac0e1f2a0020') === 'video') : ?>
  <p class="b-service-hero__placeholder"><?php esc_html_e('Add the video in the block settings (Media tab).', 'zinc'); ?></p>
  <?php endif; ?>
</section>
