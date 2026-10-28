<?php
/**
 * Video Feature block
 *
 * Full-width image with a heading over the bottom left and a round "Play"
 * badge. Clicking anywhere on it opens the video, with sound, in a pop-up
 * (assets/js/blocks/video-feature.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading    = get_field('heading');
$image      = get_field('image');
$position   = get_field('image_position');
$play_label = get_field('play_label') ?: __('Play', 'zinc');

$position = in_array($position, array('top', 'bottom'), true) ? $position : 'center';

// The video to open: a YouTube/Vimeo player (with sound) or a video file
$video_type = get_field('video_type');
$embed      = in_array($video_type, array('youtube', 'vimeo'), true) ? zinc_get_video_embed_url(get_field('video_url'), $video_type) : '';
$file_url   = '';
if ($video_type === 'file') {
	$file     = get_field('video_file');
	$file_url = $file['url'] ?? '';
} elseif ($video_type === 'link') {
	$file_url = zinc_get_video_file_link(get_field('video_link'));
}

$images = get_template_directory_uri() . '/assets/images/figma';

$wrapper_attributes = array(
	'class' => 'b-video-feature',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($image) {
		echo wp_get_attachment_image($image, 'full', false, array(
			'class'		=> 'b-video-feature__image b-video-feature__image--' . $position,
			'sizes'		=> '100vw',
			'loading'	=> 'lazy',
			'alt'		=> '',
		));
	} elseif ($is_preview) { ?>
  <p class="b-video-feature__placeholder"><?php esc_html_e('Add an image and the video in the block settings.', 'zinc'); ?></p>
  <?php } ?>

  <?php if ($heading) : ?>
  <h2 class="b-video-feature__heading"><?php echo nl2br(esc_html($heading), false); ?></h2>
  <?php endif; ?>

  <?php if ($embed || $file_url) : ?>
  <button type="button" class="b-video-feature__play"<?php if ($embed) : ?> data-video-embed="<?php echo esc_url($embed); ?>"<?php else : ?> data-video-src="<?php echo esc_url($file_url); ?>"<?php endif; ?> aria-haspopup="dialog" aria-label="<?php echo esc_attr(sprintf(__('Play video: %s', 'zinc'), wp_strip_all_tags($heading ?: get_the_title()))); ?>">
    <span class="b-video-feature__badge"><?php echo esc_html($play_label); ?></span>
  </button>

  <dialog class="b-video-feature__modal" aria-label="<?php esc_attr_e('Video', 'zinc'); ?>">
    <button type="button" class="b-video-feature__close" aria-label="<?php esc_attr_e('Close video', 'zinc'); ?>">
      <img src="<?php echo esc_url($images . '/menu-close.svg'); ?>" width="41" height="41" alt="" />
    </button>
    <div class="b-video-feature__player"></div>
  </dialog>
  <?php elseif ($is_preview) : ?>
  <span class="b-video-feature__badge b-video-feature__badge--preview"><?php echo esc_html($play_label); ?></span>
  <?php endif; ?>
</section>
