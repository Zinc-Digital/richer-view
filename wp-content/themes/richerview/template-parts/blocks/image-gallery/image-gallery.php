<?php
/**
 * Image Gallery block
 *
 * A row of images that can be dragged sideways (assets/js/blocks/image-gallery.js):
 * large ones running off both sides, starting centred, or ("From the left")
 * narrower portrait ones starting in line with the page heading.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$gallery    = get_field('images') ?: array();
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
  <?php if ($gallery) : ?>
  <div class="b-gallery__slider" tabindex="0" role="region" aria-label="<?php esc_attr_e('Image gallery', 'zinc'); ?>">
    <ul class="b-gallery__track">
      <?php foreach ($gallery as $image_id) : ?>
      <li class="b-gallery__item">
        <?php echo wp_get_attachment_image($image_id, 'large', false, array(
					'class'		=> 'b-gallery__image',
					'sizes'		=> $layout === 'left' ? '(min-width: 1025px) 389px, 78vw' : '(min-width: 1025px) 530px, 78vw',
					'loading'	=> 'lazy',
				)); ?>
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
  <p class="b-gallery__placeholder"><?php esc_html_e('Add images in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
