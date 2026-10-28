<?php
/**
 * Services Showcase block
 *
 * Centred heading with a hand-drawn underline (or a smaller left-aligned one),
 * followed by a row of service cards. On desktop the cards scroll sideways as the page scrolls
 * (assets/js/blocks/services-showcase.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading   = get_field('heading');
$highlight = get_field('highlight');
$text      = get_field('text');
$cards     = get_field('cards');
$animate   = get_field('scroll_animation') !== false;
$compact   = get_field('heading_style') === 'compact';

$wrapper_attributes = array(
	'class' => 'b-services' . ($compact ? ' b-services--compact' : ''),
);
if ($animate) {
	$wrapper_attributes['data-scroll-animation'] = 'true';
}
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}

// Underline the chosen words with the hand-drawn scribble
$heading_html = esc_html($heading);
if ($heading && $highlight) {
	$scribble = sprintf(
		'<img class="b-services__scribble" src="%s" width="667" height="25.9999" alt="" />',
		esc_url(get_template_directory_uri() . '/assets/images/figma/services-underline.svg')
	);
	$heading_html = preg_replace(
		'/' . preg_quote(esc_html(trim($highlight)), '/') . '/iu',
		'<span class="b-services__highlight">$0' . $scribble . '</span>',
		$heading_html,
		1
	);
}
$heading_html = nl2br($heading_html, false);

/**
 * Hover badge contents for a card (text + optional icon), or '' if turned off.
 */
$cursor_icons = array(
	'exclamation'	=> array('file' => 'cursor-icon-exclamation.svg', 'width' => '4.02136', 'height' => '13.7888'),
	'magnifier'		=> array('file' => 'cursor-icon-magnifier.svg', 'width' => '17.0661', 'height' => '17.0644'),
);
$get_cursor_html = function ($cursor) use ($cursor_icons) {
	$label = trim((string) ($cursor['label'] ?? ''));
	$icon  = $cursor['icon'] ?? 'none';
	$icon_html = '';

	if (isset($cursor_icons[$icon])) {
		$icon_html = sprintf(
			'<span class="c-cursor__icon c-cursor__icon--%s"><img src="%s" width="%s" height="%s" alt="" /></span>',
			esc_attr($icon),
			esc_url(get_template_directory_uri() . '/assets/images/figma/' . $cursor_icons[$icon]['file']),
			esc_attr($cursor_icons[$icon]['width']),
			esc_attr($cursor_icons[$icon]['height'])
		);
	} elseif ($icon === 'custom' && !empty($cursor['icon_custom'])) {
		$icon_html = '<span class="c-cursor__icon">' . wp_get_attachment_image($cursor['icon_custom'], 'thumbnail', false, array('alt' => '')) . '</span>';
	}

	if ($label === '' && $icon_html === '') return '';

	$label_html = $label !== '' ? '<span class="c-cursor__label">' . esc_html($label) . '</span>' : '';
	return ($cursor['icon_position'] ?? 'after') === 'before' ? $icon_html . $label_html : $label_html . $icon_html;
};
$has_cursor = false;
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($heading || $text) : ?>
  <div class="b-services__intro">
    <?php if ($heading) : ?>
    <h2 class="b-services__heading"><?php echo $heading_html; ?></h2>
    <?php endif; ?>

    <?php if ($text) : ?>
    <p class="b-services__text"><?php echo esc_html($text); ?></p>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($cards) : ?>
  <div class="b-services__slider">
    <div class="b-services__viewport">
      <ul class="b-services__track">
        <?php foreach ($cards as $card) :
					$link     = $card['link'];
					$tag      = $link ? 'a' : 'div';
					$tags     = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $card['tags'])));
					// Each card shows an image, a background video (started by services-showcase.js),
					// or with "Play on hover" an image that the video fades in over
					$video_type  = $card['video_type'] ?? 'none';
					$video_embed = in_array($video_type, array('youtube', 'vimeo'), true) ? zinc_get_background_video_embed_url($card['video_url'], $video_type) : '';
					$video_file  = $video_type === 'file' && !empty($card['video_file']['url']) ? $card['video_file'] : null;
					$has_video   = in_array($video_type, array('youtube', 'vimeo', 'file'), true);
					$playback    = ($card['video_playback'] ?? '') === 'hover' ? 'hover' : 'autoplay';
					$image       = !$has_video || $playback === 'hover' ? $card['image'] : null;
					$position    = in_array($card['image_position'], array('top', 'bottom'), true) ? $card['image_position'] : 'center';
					$cursor_html = $get_cursor_html($card['cursor'] ?? array());
					$has_cursor  = $has_cursor || $cursor_html !== '';
				?>
        <li class="b-services__item">
          <?php if ($cursor_html) : ?>
          <template class="b-services__cursor-template"><?php echo $cursor_html; ?></template>
          <?php endif; ?>
          <<?php echo $tag; ?> class="b-services__card"<?php if ($link) : ?> href="<?php echo esc_url($link['url']); ?>"<?php if ($link['target']) : ?> target="<?php echo esc_attr($link['target']); ?>" rel="noopener"<?php endif; ?><?php endif; ?>>
            <?php if ($image) {
							echo wp_get_attachment_image($image, 'full', false, array(
								'class'		=> 'b-services__image b-services__image--' . $position,
								'sizes'		=> '(min-width: 1025px) 389px, 300px',
								'loading'	=> 'lazy',
								'alt'		=> '',
							));
						} ?>

            <?php if ($video_embed) : ?>
            <div class="b-services__video" data-video-embed="<?php echo esc_url($video_embed); ?>" data-video-playback="<?php echo esc_attr($playback); ?>" aria-hidden="true"></div>
            <?php elseif ($video_file) : ?>
            <div class="b-services__video<?php if (!$image) echo ' b-services__video--file'; ?>" data-video-playback="<?php echo esc_attr($playback); ?>" aria-hidden="true">
              <video src="<?php echo esc_url($video_file['url']); ?>" muted loop playsinline preload="metadata" tabindex="-1"></video>
            </div>
            <?php endif; ?>

            <?php if ($card['title']) : ?>
            <h3 class="b-services__title"><?php echo esc_html($card['title']); ?></h3>
            <?php endif; ?>

            <?php if ($tags) : ?>
            <ul class="b-services__tags">
              <?php foreach ($tags as $item) : ?>
              <li class="b-services__tag"><?php echo esc_html($item); ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </<?php echo $tag; ?>>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <?php if ($has_cursor) : ?>
  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge"></div>
  </div>
  <?php endif; ?>
  <?php elseif ($is_preview) : ?>
  <p class="b-services__placeholder"><?php esc_html_e('Add service cards in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
