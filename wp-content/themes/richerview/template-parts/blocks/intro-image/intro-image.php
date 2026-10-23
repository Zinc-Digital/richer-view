<?php
/**
 * Intro with Image block
 *
 * Large heading with intro text alongside, followed by a wide photo or
 * background video (assets/js/blocks/intro-image.js) with an optional round
 * badge (e.g. "making videos since 2007") overlapping it.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading    = get_field('heading');
$text       = get_field('text');
$image      = get_field('image');
$position   = get_field('image_position');
$show_badge = get_field('show_badge') !== false;
$badge      = get_field('badge');
$spacing    = get_field('spacing') === 'standard' ? 'standard' : 'page-top';
$level      = get_field('heading_level') === 'h2' ? 'h2' : 'h1';

$position = in_array($position, array('top', 'bottom'), true) ? $position : 'center';

// Optional muted looping video (YouTube, Vimeo or upload) instead of the image – it shows straight away
$video_embed = '';
$video_file  = '';
if (get_field('media_type') === 'video') {
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

$wrapper_attributes = array(
	'class' => 'b-intro-image' . ($spacing === 'page-top' ? ' b-intro-image--page-top' : ''),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-intro-image__inner">
    <?php if ($heading || $text) : ?>
    <div class="b-intro-image__header">
      <?php if ($heading) : ?>
      <<?php echo $level; ?> class="b-intro-image__heading"><?php echo esc_html($heading); ?></<?php echo $level; ?>>
      <?php endif; ?>

      <?php if ($text) : ?>
      <div class="b-intro-image__text"><?php echo wp_kses(wpautop($text), array('p' => array(), 'br' => array())); ?></div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($image || $video_embed || $video_file) : ?>
    <div class="b-intro-image__media">
      <?php if ($image) {
				echo wp_get_attachment_image($image, 'full', false, array(
					'class'		=> 'b-intro-image__image b-intro-image__image--' . $position,
					'sizes'		=> '(min-width: 1240px) 1190px, 100vw',
					'loading'	=> $spacing === 'page-top' ? 'eager' : 'lazy',
				));
			} ?>

      <?php if ($video_embed) : ?>
      <div class="b-intro-image__video" data-video-embed="<?php echo esc_url($video_embed); ?>" aria-hidden="true"><?php zinc_background_video_iframe($video_embed); ?></div>
      <?php elseif ($video_file) : ?>
      <div class="b-intro-image__video" aria-hidden="true">
        <video src="<?php echo esc_url($video_file); ?>" muted loop playsinline autoplay preload="auto" tabindex="-1"></video>
      </div>
      <?php endif; ?>

      <?php if ($show_badge && $badge) : ?>
      <p class="b-intro-image__badge"><?php echo nl2br(esc_html($badge), false); ?></p>
      <?php endif; ?>
    </div>
    <?php elseif ($is_preview) : ?>
    <p><?php esc_html_e('Add an image or video in the block settings.', 'zinc'); ?></p>
    <?php endif; ?>
  </div>
</section>
