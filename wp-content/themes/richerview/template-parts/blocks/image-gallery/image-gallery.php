<?php
/**
 * Image Gallery block
 *
 * A row of images and/or muted looping videos that can be dragged sideways
 * (assets/js/blocks/image-gallery.js): large ones running off both sides,
 * starting centred, or ("From the left") narrower portrait ones starting in
 * line with the page heading. Videos play when visible or on hover, like the
 * Services Showcase cards (assets/js/components/background-video.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$items = get_field('items') ?: array();
// Galleries saved before videos were added only have a list of image IDs
if (!$items && ($legacy = get_field('images'))) {
	foreach ((array) $legacy as $image_id) {
		$items[] = array('image' => $image_id, 'video_type' => 'none');
	}
}
$drag_label = get_field('drag_label');
$layout     = get_field('field_6b04c5d6e4005') === 'left' ? 'left' : 'centred';

$images = get_template_directory_uri() . '/assets/images/figma';

$wrapper_attributes = array(
	'class'			=> 'b-gallery b-gallery--' . $layout,
	'data-start'	=> $layout === 'left' ? 'start' : 'centre',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($items) : ?>
  <div class="b-gallery__slider" tabindex="0" role="region" aria-label="<?php esc_attr_e('Gallery', 'zinc'); ?>">
    <ul class="b-gallery__track">
      <?php foreach ($items as $item) :
				$video_type  = $item['video_type'] ?? 'none';
				$video_embed = in_array($video_type, array('youtube', 'vimeo'), true) ? zinc_get_background_video_embed_url($item['video_url'] ?? '', $video_type) : '';
				$video_file  = '';
				if ($video_type === 'file') {
					$video_file = $item['video_file']['url'] ?? '';
				} elseif ($video_type === 'link') {
					$video_file = zinc_get_video_file_link($item['video_link'] ?? '');
				}
				$playback = ($item['video_playback'] ?? '') === 'hover' ? 'hover' : 'autoplay';
				$image    = $item['image'] ?? null;
				if (!$image && !$video_embed && !$video_file) continue;
			?>
      <li class="b-gallery__item<?php if ($video_embed || $video_file) echo ' has-video'; ?>">
        <?php if ($image) {
					echo wp_get_attachment_image($image, 'large', false, array(
						'class'		=> 'b-gallery__image',
						'sizes'		=> $layout === 'left' ? '(min-width: 1025px) 389px, 78vw' : '(min-width: 1025px) 530px, 78vw',
						'loading'	=> 'lazy',
					));
				} ?>

        <?php if ($video_embed) : ?>
        <div class="b-gallery__video" data-video-embed="<?php echo esc_url($video_embed); ?>" data-video-playback="<?php echo esc_attr($playback); ?>" aria-hidden="true"></div>
        <?php elseif ($video_file) : ?>
        <div class="b-gallery__video<?php if (!$image) echo ' b-gallery__video--file'; ?>" data-video-playback="<?php echo esc_attr($playback); ?>" aria-hidden="true">
          <video src="<?php echo esc_url($video_file); ?>" muted loop playsinline preload="metadata" tabindex="-1"></video>
        </div>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge c-cursor__badge--drag">
      <span class="c-cursor__icon c-cursor__icon--chevron c-cursor__icon--chevron-left"><img src="<?php echo esc_url($images . '/cursor-icon-chevron.svg'); ?>" width="6" height="10" alt="" /></span>
      <span class="c-cursor__label"><?php echo esc_html($drag_label ?: __('drag', 'zinc')); ?></span>
      <span class="c-cursor__icon c-cursor__icon--chevron"><img src="<?php echo esc_url($images . '/cursor-icon-chevron.svg'); ?>" width="6" height="10" alt="" /></span>
    </div>
  </div>
  <?php elseif ($is_preview) : ?>
  <p class="b-gallery__placeholder"><?php esc_html_e('Add images or videos in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
