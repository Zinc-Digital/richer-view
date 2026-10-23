<?php
/**
 * Accordion with Image block
 *
 * Heading and a list of points that open to show more detail, with a photo
 * or background video alongside. The open point gets a sparkle doodle and can
 * swap in its own photo or video (assets/js/blocks/accordion-image.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading      = get_field('heading');
$image        = get_field('image');
$position     = get_field('image_position');
$items        = get_field('items') ?: array();
$show_sparkle = get_field('show_sparkle') !== false;

/**
 * Video settings → embed URL (YouTube/Vimeo) or file URL (upload), or nothing.
 * Used for the main media and for each point's own media.
 */
$get_video = function ($media_type, $video_type, $video_url, $video_file, $video_link = '') {
	if ($media_type !== 'video') return null;
	if (in_array($video_type, array('youtube', 'vimeo'), true)) {
		$embed = zinc_get_background_video_embed_url($video_url, $video_type);
		return $embed ? array('embed' => $embed) : null;
	}
	if ($video_type === 'file' && !empty($video_file['url'])) {
		return array('file' => $video_file['url']);
	}
	// A direct link to a video file plays just like an upload
	if ($video_type === 'link' && ($link = zinc_get_video_file_link($video_link))) {
		return array('file' => $link);
	}
	return null;
};

// Main media: an image, or a muted looping video with the image as its poster
$main_video = $get_video(get_field('media_type'), get_field('video_type'), get_field('video_url'), get_field('video_file'), get_field('video_link'));
if (get_field('media_type') === 'video') {
	$image = null; // a video plays straight away – no image first
}

$images = get_template_directory_uri() . '/assets/images/figma';
$id     = 'accordion-' . substr(md5($block['id'] ?? uniqid()), 0, 8);

// The point that starts open: the first one ticked "Open when the page loads", or the first point
$open_index = 0;
foreach ($items as $index => $item) {
	if (!empty($item['open'])) {
		$open_index = $index;
		break;
	}
}

$image_class = function ($position) {
	$position = in_array($position, array('left', 'right'), true) ? $position : 'center';
	return 'b-accordion-image__image b-accordion-image__image--' . $position;
};

// Each point's own video (if it has one), and whether it has its own media at all
$item_videos = array();
foreach ($items as $index => $item) {
	$item_videos[$index] = $get_video($item['media_type'] ?? '', $item['video_type'] ?? '', $item['video_url'] ?? '', $item['video_file'] ?? null, $item['video_link'] ?? '');
}
$item_image = function ($index) use ($items, $item_videos) {
	return empty($item_videos[$index]) && ($items[$index]['media_type'] ?? 'image') !== 'video' ? ($items[$index]['image'] ?? null) : null;
};
$has_own_media = function ($index) use ($item_image, $item_videos) {
	return !empty($item_image($index)) || !empty($item_videos[$index]);
};

/**
 * Output one layer of the photo area: an image, or a video (no poster image –
 * it shows straight away). `$key` matches the point's data-media ("default"
 * for the main media). An uploaded video in the layer shown on load starts
 * playing immediately (autoplay); YouTube/Vimeo players load when the block
 * comes near the screen; the rest wait until their point is opened.
 */
$render_media = function ($key, $image_id, $position, $video, $active) use ($image_class) {
	if ($video) : ?>
      <div class="b-accordion-image__image b-accordion-image__layer<?php if ($active) echo ' is-active'; ?>" data-media="<?php echo esc_attr($key); ?>">
        <?php if (!empty($video['embed'])) : ?>
        <div class="b-accordion-image__video" data-video-embed="<?php echo esc_url($video['embed']); ?>" aria-hidden="true"></div>
        <?php else : ?>
        <div class="b-accordion-image__video" aria-hidden="true">
          <video src="<?php echo esc_url($video['file']); ?>" muted loop playsinline<?php if ($active) echo ' autoplay'; ?> preload="<?php echo $active ? 'auto' : 'metadata'; ?>" tabindex="-1"></video>
        </div>
        <?php endif; ?>
      </div>
	<?php
	elseif ($image_id) :
		echo wp_get_attachment_image($image_id, 'large', false, array(
			'class'			=> $image_class($position) . ($active ? ' is-active' : ''),
			'sizes'			=> '(min-width: 1025px) 474px, 100vw',
			'data-media'	=> $key,
			'loading'		=> 'lazy',
		));
	endif;
};

$wrapper_attributes = array(
	'class' => 'b-accordion-image',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-accordion-image__inner">
    <div class="b-accordion-image__content">
      <?php if ($heading) : ?>
      <h2 class="b-accordion-image__heading"><?php echo esc_html($heading); ?></h2>
      <?php endif; ?>

      <?php if ($items) : ?>
      <ul class="b-accordion-image__list">
        <?php foreach ($items as $index => $item) :
					$is_open  = $index === $open_index;
					$item_id  = $id . '-' . $index;
				?>
        <li class="b-accordion-image__item<?php if ($item['text']) echo ' has-text'; ?><?php if ($is_open) echo ' is-open'; ?>"<?php if ($has_own_media($index)) : ?> data-media="<?php echo esc_attr($item_id); ?>"<?php endif; ?>>
          <h3 class="b-accordion-image__title">
            <button type="button" class="b-accordion-image__toggle" id="<?php echo esc_attr($item_id); ?>-toggle" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($item_id); ?>-panel">
              <?php echo esc_html($item['title']); ?><?php if ($show_sparkle) : ?><span class="b-accordion-image__sparkle" aria-hidden="true"><img src="<?php echo esc_url($images . '/accordion-sparkle.svg'); ?>" width="61.8631" height="56.4638" alt="" /></span><?php endif; ?>
            </button>
          </h3>
          <div class="b-accordion-image__panel" id="<?php echo esc_attr($item_id); ?>-panel" role="region" aria-labelledby="<?php echo esc_attr($item_id); ?>-toggle">
            <div class="b-accordion-image__panel-inner">
              <?php if ($item['text']) : ?>
              <p class="b-accordion-image__text"><?php echo nl2br(esc_html($item['text']), false); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php elseif ($is_preview) : ?>
      <p class="b-accordion-image__placeholder"><?php esc_html_e('Add points in the block settings.', 'zinc'); ?></p>
      <?php endif; ?>
    </div>

    <?php
		$open_has_own = $items && $has_own_media($open_index);
		$any_media    = $image || $main_video || array_filter(array_keys($items), $has_own_media);
		if ($any_media) : ?>
    <div class="b-accordion-image__media">
      <?php
				$render_media('default', $image, $position, $main_video, !$open_has_own);
				foreach ($items as $index => $item) {
					if (!$has_own_media($index)) continue;
					$render_media($id . '-' . $index, $item_image($index), $item['image_position'] ?? '', $item_videos[$index], $index === $open_index);
				}
			?>
    </div>
    <?php endif; ?>
  </div>
</section>
