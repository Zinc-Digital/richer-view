<?php
/**
 * Testimonials block
 *
 * Heading with a speech-bubble doodle and a draggable row of quote cards and
 * video cards. Video cards open the video in a pop-up
 * (assets/js/blocks/testimonials.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading      = get_field('heading');
$show_doodle  = get_field('show_doodle') !== false;
$hint         = get_field('hint');
$drag_label   = get_field('drag_label');
$testimonials = get_field('testimonials');

$images = get_template_directory_uri() . '/assets/images/figma';

$wrapper_attributes = array(
	'class' => 'b-testimonials',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($heading || $hint) : ?>
  <div class="b-testimonials__header">
    <?php if ($heading) : ?>
    <h2 class="b-testimonials__heading">
      <?php echo esc_html($heading); ?><?php if ($show_doodle) : ?><span class="b-testimonials__doodle" aria-hidden="true"><img src="<?php echo esc_url($images . '/testimonials-doodle.svg'); ?>" width="86.2057" height="64.1837" alt="" /></span><?php endif; ?>
    </h2>
    <?php endif; ?>

    <?php if ($hint) : ?>
    <p class="b-testimonials__hint"><?php echo esc_html($hint); ?></p>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($testimonials) : ?>
  <div class="b-testimonials__slider" tabindex="0" role="region" aria-label="<?php echo esc_attr($heading ?: __('Testimonials', 'zinc')); ?>">
    <ul class="b-testimonials__track">
      <?php foreach ($testimonials as $item) :
				$is_video = ($item['type'] ?? 'quote') === 'video';
			?>
      <li class="b-testimonials__item">
        <?php if ($is_video) :
					$video_type = $item['video_type'] ?? 'youtube';
					$embed      = in_array($video_type, array('youtube', 'vimeo'), true) ? zinc_get_video_embed_url($item['video_url'], $video_type) : '';
					$file_url   = $video_type === 'file' && !empty($item['video_file']['url']) ? $item['video_file']['url'] : '';
					$label      = $item['expand_label'] ?: __('click to expand', 'zinc');
					$position   = in_array($item['image_position'], array('left', 'right'), true) ? $item['image_position'] : 'center';
					$title      = $item['video_title'] ?: __('Video testimonial', 'zinc');
				?>
        <button type="button" class="b-testimonials__card b-testimonials__card--video"<?php if ($embed) : ?> data-video-embed="<?php echo esc_url($embed); ?>"<?php elseif ($file_url) : ?> data-video-src="<?php echo esc_url($file_url); ?>"<?php endif; ?> data-cursor-label="<?php echo esc_attr($item['play_label'] ?: __('Play', 'zinc')); ?>" aria-haspopup="dialog" aria-label="<?php echo esc_attr(sprintf(__('Play video: %s', 'zinc'), $title)); ?>">
          <?php if ($item['image']) {
						echo wp_get_attachment_image($item['image'], 'large', false, array(
							'class'		=> 'b-testimonials__image b-testimonials__image--' . $position,
							'sizes'		=> '(min-width: 1025px) 360px, 300px',
							'loading'	=> 'lazy',
							'alt'		=> '',
						));
					} ?>
          <?php if ($is_preview && !$embed && !$file_url) : ?>
          <span class="b-testimonials__warning"><?php esc_html_e('This video link isn’t recognised – check the link in the block settings.', 'zinc'); ?></span>
          <?php endif; ?>
          <span class="b-testimonials__expand">
            <img src="<?php echo esc_url($images . '/icon-video-expand.svg'); ?>" width="20.0009" height="12.5001" alt="" />
            <?php echo esc_html($label); ?>
          </span>
        </button>
        <?php else :
					$colour = in_array($item['colour'], array('coral', 'yellow', 'blue'), true) ? $item['colour'] : 'coral';
				?>
        <blockquote class="b-testimonials__card b-testimonials__card--quote b-testimonials__card--<?php echo esc_attr($colour); ?>">
          <?php if ($item['quote']) : ?>
          <p class="b-testimonials__quote">&ldquo;<?php echo esc_html(trim($item['quote'], " \t\n\r\"“”")); ?>&rdquo;</p>
          <?php endif; ?>

          <?php if ($item['name'] || $item['role']) : ?>
          <footer class="b-testimonials__author">
            <?php if ($item['name']) : ?>
            <cite class="b-testimonials__name"><?php echo esc_html($item['name']); ?></cite>
            <?php endif; ?>
            <?php if ($item['role']) : ?>
            <span class="b-testimonials__role"><?php echo esc_html($item['role']); ?></span>
            <?php endif; ?>
          </footer>
          <?php endif; ?>
        </blockquote>
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

  <dialog class="b-testimonials__modal" aria-label="<?php esc_attr_e('Video', 'zinc'); ?>">
    <button type="button" class="b-testimonials__close" aria-label="<?php esc_attr_e('Close video', 'zinc'); ?>">
      <img src="<?php echo esc_url($images . '/menu-close.svg'); ?>" width="41" height="41" alt="" />
    </button>
    <div class="b-testimonials__player"></div>
  </dialog>
  <?php elseif ($is_preview) : ?>
  <p class="b-testimonials__placeholder"><?php esc_html_e('Add testimonials in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
