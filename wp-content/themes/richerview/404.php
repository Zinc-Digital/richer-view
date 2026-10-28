<?php
/**
 * 404 page
 *
 * A full-screen photo or looping video with the "wrong page, right vibe"
 * message, a round badge that follows the mouse (assets/js/components/not-found.js)
 * and the legal links in the corner – no footer. The wording, photo and video
 * are set in Theme Settings > 404 page.
 */

get_header();

$option = function ($name, $default = '') {
	$value = get_field($name, 'option');
	return $value === null || $value === '' || $value === false ? $default : $value;
};

$label      = $option('nf_label', __('error 404', 'zinc'));
$heading    = $option('nf_heading', __('wrong page, right vibe', 'zinc'));
$text       = $option('nf_text', __('This page didn\'t make the final cut. It may have moved, or it was never shot in the first place.', 'zinc'));
$link_label = $option('nf_link_label', __('back to the home page', 'zinc'));
$badge      = $option('nf_badge', "that's\nnot the shot.");
$position   = $option('nf_image_position', 'center');
$position   = in_array($position, array('top', 'bottom'), true) ? $position : 'center';

// Optional muted looping video instead of the photo – it shows straight away
$video_embed = '';
$video_file  = '';
if ($option('nf_media_type') === 'video') {
	$video_type = $option('nf_video_type');
	if (in_array($video_type, array('youtube', 'vimeo'), true)) {
		$video_embed = zinc_get_background_video_embed_url($option('nf_video_url'), $video_type);
	} elseif ($video_type === 'file') {
		$file       = $option('nf_video_file', array());
		$video_file = $file['url'] ?? '';
	} elseif ($video_type === 'link') {
		$video_file = zinc_get_video_file_link($option('nf_video_link'));
	}
}
$image = $video_embed || $video_file ? null : $option('nf_image');

$figma = get_template_directory_uri() . '/assets/images/figma';
?>

<main class="page-wrapper">
  <section class="c-not-found">
    <div class="c-not-found__media" aria-hidden="true">
      <?php if ($image) {
				echo wp_get_attachment_image($image, 'full', false, array(
					'class'		=> 'c-not-found__image c-not-found__image--' . $position,
					'sizes'		=> '100vw',
					'alt'		=> '',
				));
			} elseif (!$video_embed && !$video_file) { ?>
      <img class="c-not-found__image" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/services/not-found.jpg'); ?>" width="1500" height="844" alt="" />
      <?php } ?>

      <?php if ($video_embed) : ?>
      <div class="c-not-found__video" data-video-embed="<?php echo esc_url($video_embed); ?>"><?php zinc_background_video_iframe($video_embed); ?></div>
      <?php elseif ($video_file) : ?>
      <div class="c-not-found__video">
        <video src="<?php echo esc_url($video_file); ?>" muted loop playsinline autoplay preload="auto" tabindex="-1"></video>
      </div>
      <?php endif; ?>
    </div>

    <div class="c-not-found__content">
      <p class="c-not-found__label"><?php echo esc_html($label); ?></p>
      <h1 class="c-not-found__heading"><?php echo nl2br(esc_html($heading), false); ?></h1>
      <p class="c-not-found__text"><?php echo esc_html($text); ?></p>
      <a class="c-not-found__link" href="<?php echo esc_url(home_url('/')); ?>">
        <span class="c-not-found__link-text">
          <?php echo esc_html($link_label); ?>
          <img src="<?php echo esc_url($figma . '/project-arrow-hover.svg'); ?>" width="12" height="9" alt="" />
        </span>
        <img class="c-not-found__underline" src="<?php echo esc_url($figma . '/home-link-underline.svg'); ?>" width="187" height="4" alt="" />
      </a>
    </div>

    <?php if ($badge) : ?>
    <p class="c-not-found__badge" aria-hidden="true"><?php echo nl2br(esc_html($badge), false); ?></p>
    <?php endif; ?>

    <?php get_template_part('template-parts/components/legal-links', null, array('class' => 'c-not-found__legal')); ?>
  </section>

<?php get_footer(null, array('minimal' => true)); ?>
