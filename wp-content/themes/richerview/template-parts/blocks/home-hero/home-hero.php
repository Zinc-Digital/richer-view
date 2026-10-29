<?php
/**
 * Home Hero block
 *
 * A full-screen photo or video with the big "richer view." wordmark, intro
 * text and a "scroll" hint. As the page is scrolled it zooms out to show the
 * main photo as one tile in a staggered grid of more photos and videos
 * (assets/js/blocks/home-hero.js).
 *
 * The grid has 13 places around the main tile, filled from the Grid tab in
 * reading order (row by row, left to right). With fewer tiles they repeat to
 * fill the grid. With none – or with the zoom turned off, in the editor, or for
 * visitors who prefer reduced motion – the block is just the full-screen photo.
 *
 * Once zoomed out, clicking a tile opens its video (with sound) or photo in a
 * pop-up, with a round "zoom" badge following the mouse over the tiles – or
 * goes to the tile's link, if it has one.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading      = get_field('heading') ?: __('richer view.', 'zinc');
$text         = get_field('field_6b8c1d2e3f003');
$scroll_label = get_field('field_6b8c1d2e3f005');
$zoom         = get_field('field_6b8c1d2e3f041') !== false;
$cursor_label = trim((string) get_field('field_6b8c1d2e3f042'));
$tiles        = get_field('tiles') ?: array();

$figma = get_template_directory_uri() . '/assets/images/figma';

/**
 * Photo and/or muted looping video for the main tile or a grid tile. A video
 * fades in over the photo once it's playing (assets/js/components/background-video.js);
 * an upload with no photo shows its first frame straight away.
 */
$render_media = function ($media, $sizes, $loading) {
	$video_embed = '';
	$video_file  = '';
	if (($media['media_type'] ?? 'image') === 'video') {
		$video_type = $media['video_type'] ?? '';
		if (in_array($video_type, array('youtube', 'vimeo'), true)) {
			$video_embed = zinc_get_background_video_embed_url($media['video_url'] ?? '', $video_type);
		} elseif ($video_type === 'file') {
			$video_file = $media['video_file']['url'] ?? '';
		} elseif ($video_type === 'link') {
			$video_file = zinc_get_video_file_link($media['video_link'] ?? '');
		}
	}
	$image    = $media['image'] ?? null;
	$position = in_array($media['image_position'] ?? '', array('top', 'bottom'), true) ? $media['image_position'] : 'center';

	$html = '';
	if ($image) {
		$html .= wp_get_attachment_image($image, 'full', false, array(
			'class'		=> 'b-home-hero__image b-home-hero__image--' . $position,
			'sizes'		=> $sizes,
			'loading'	=> $loading,
			'alt'		=> '',
		));
	}
	if ($video_embed) {
		$html .= '<div class="b-home-hero__video" data-video-embed="' . esc_url($video_embed) . '"></div>';
	} elseif ($video_file) {
		$html .= '<div class="b-home-hero__video' . ($image ? '' : ' b-home-hero__video--file') . '">'
			. '<video src="' . esc_url($video_file) . '" muted loop playsinline preload="' . ($loading === 'eager' ? 'auto' : 'metadata') . '" tabindex="-1"></video>'
			. '</div>';
	}
	return $html;
};

/**
 * Attributes for opening a tile in the pop-up: its video with sound and
 * controls, or its photo at full size.
 */
$lightbox_attributes = function ($media) {
	$type = '';
	$src  = '';
	if (($media['media_type'] ?? 'image') === 'video') {
		$video_type = $media['video_type'] ?? '';
		if (in_array($video_type, array('youtube', 'vimeo'), true)) {
			$type = 'embed';
			$src  = zinc_get_video_embed_url($media['video_url'] ?? '', $video_type);
		} elseif ($video_type === 'file') {
			$type = 'video';
			$src  = $media['video_file']['url'] ?? '';
		} elseif ($video_type === 'link') {
			$type = 'video';
			$src  = zinc_get_video_file_link($media['video_link'] ?? '');
		}
	}
	if (!$src && !empty($media['image'])) {
		$type = 'image';
		$src  = wp_get_attachment_image_url($media['image'], 'full');
	}
	if (!$src) return '';

	$label = $type === 'image' ? __('View photo', 'zinc') : __('Play video', 'zinc');
	$alt   = $type === 'image' ? (string) get_post_meta($media['image'], '_wp_attachment_image_alt', true) : '';
	return sprintf(
		' data-lightbox="%s" data-lightbox-src="%s" data-lightbox-label="%s"%s',
		esc_attr($type),
		esc_url($src),
		esc_attr($label),
		$alt !== '' ? ' data-lightbox-alt="' . esc_attr($alt) . '"' : ''
	);
};

$main = array(
	'media_type'		=> get_field('main_media_type'),
	'image'				=> get_field('main_image'),
	'image_position'	=> get_field('main_image_position'),
	'video_type'		=> get_field('main_video_type'),
	'video_url'			=> get_field('main_video_url'),
	'video_file'		=> get_field('main_video_file'),
	'video_link'		=> get_field('main_video_link'),
);
// A main video plays straight away, so it doesn't need its photo
$main_html = $render_media($main, '100vw', 'eager');
if (!$main_html && !$is_preview) {
	$main_html = '<img class="b-home-hero__image" src="' . esc_url(get_template_directory_uri() . '/assets/images/services/not-found.jpg') . '" width="1500" height="844" alt="" />';
}

// Grid places around the main tile, in the design's own pixels at 1440 wide:
// rows are 321.67 apart and tiles 495.43 apart, every other row shifted half a
// tile. The main tile's centre is (720, 446).
$slots = array(
	array(-1.5, -1), array(-0.5, -1), array(0.5, -1), array(1.5, -1),
	array(-1, 0), array(1, 0),
	array(-1.5, 1), array(-0.5, 1), array(0.5, 1), array(1.5, 1),
	array(-1, 2), array(0, 2), array(1, 2),
);
$slot_style = function ($column, $row) {
	return sprintf('--x: %.2f; --y: %.2f;', 720 + $column * 495.43 - 237.96, 446 + $row * 321.67 - 151.17);
};

$tiles = array_values(array_filter($tiles, function ($tile) {
	return !empty($tile['image']) || ($tile['media_type'] ?? '') === 'video';
}));
$has_grid = $zoom && $tiles;

$wrapper_attributes = array(
	'class' => 'b-home-hero' . ($has_grid ? ' b-home-hero--grid' : ''),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
$has_cursor = $has_grid && $cursor_label !== '';
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-home-hero__stage">
    <div class="b-home-hero__mosaic">
      <div class="b-home-hero__tile b-home-hero__tile--main" style="<?php echo esc_attr($slot_style(0, 0)); ?>"<?php if ($has_grid) echo $lightbox_attributes($main); ?>>
        <?php echo $main_html; ?>
      </div>

      <?php if ($has_grid) :
				foreach ($slots as $i => list($column, $row)) :
					// Fewer tiles than places: start again from the first
					$tile   = $tiles[$i % count($tiles)];
					$repeat = $i >= count($tiles);
					$link   = $tile['link'] ?? null;
					$tag    = $link ? 'a' : 'div';
				?>
      <<?php echo $tag; ?> class="b-home-hero__tile" style="<?php echo esc_attr($slot_style($column, $row)); ?>"<?php if ($link) : ?> href="<?php echo esc_url($link['url']); ?>"<?php if ($link['target']) : ?> target="<?php echo esc_attr($link['target']); ?>" rel="noopener"<?php endif; ?><?php if ($link['title']) : ?> aria-label="<?php echo esc_attr($link['title']); ?>"<?php endif; ?><?php else : echo $lightbox_attributes($tile); endif; ?><?php if ($repeat) : ?> tabindex="-1" aria-hidden="true" data-repeat<?php endif; ?>>
        <?php echo $render_media($tile, '(max-width: 767px) 70vw, 50vw', 'lazy'); ?>
      </<?php echo $tag; ?>>
      <?php endforeach;
			endif; ?>
    </div>

    <div class="b-home-hero__overlay">
      <h1 class="b-home-hero__heading">
        <img class="b-home-hero__wordmark" src="<?php echo esc_url($figma . '/hero-wordmark.svg'); ?>" width="771.293" height="288" alt="<?php echo esc_attr($heading); ?>" />
      </h1>

      <?php if ($text || $scroll_label) : ?>
      <div class="b-home-hero__footer">
        <?php if ($text) : ?>
        <p class="b-home-hero__text"><?php echo esc_html($text); ?></p>
        <?php endif; ?>

        <?php if ($scroll_label) : ?>
        <span class="b-home-hero__scroll" aria-hidden="true">
          <span class="b-home-hero__scroll-label"><?php echo esc_html($scroll_label); ?></span>
          <img src="<?php echo esc_url($figma . '/scroll-mouse.svg'); ?>" width="17.9972" height="34.3769" alt="" />
        </span>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($has_cursor) : ?>
  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge">
      <span class="c-cursor__icon c-cursor__icon--magnifier"><img src="<?php echo esc_url($figma . '/cursor-icon-magnifier.svg'); ?>" width="17.0661" height="17.0644" alt="" /></span>
      <span class="c-cursor__label"><?php echo esc_html($cursor_label); ?></span>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($has_grid) : ?>
  <dialog class="b-home-hero__modal" aria-label="<?php esc_attr_e('Video', 'zinc'); ?>">
    <button type="button" class="b-home-hero__close" aria-label="<?php esc_attr_e('Close', 'zinc'); ?>">
      <img src="<?php echo esc_url($figma . '/menu-close.svg'); ?>" width="41" height="41" alt="" />
    </button>
    <div class="b-home-hero__player"></div>
  </dialog>
  <?php endif; ?>
</section>
