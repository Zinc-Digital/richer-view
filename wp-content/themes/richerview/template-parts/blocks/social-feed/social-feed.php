<?php
/**
 * Social Feed block
 *
 * Page heading above a feed from a plugin shortcode (the Zinc Instagram Feed
 * mu-plugin's [instagram_feed], or Smash Balloon's [instagram-feed]), styled as
 * in the design: a grid of tall tiles three across, a round "view" badge over
 * each post and a yellow "load more" link (assets/js/blocks/social-feed.js).
 *
 * Until the plugin is installed, the editor shows placeholder tiles and the
 * page shows just the heading.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading   = get_field('heading');
$shortcode = trim((string) get_field('field_6b591a2b39003'));
$spacing   = get_field('spacing') === 'standard' ? 'standard' : 'page-top';
$level     = get_field('heading_level') === 'h2' ? 'h2' : 'h1';

// Only run the shortcode if a plugin provides it (otherwise it would show as text)
$feed = '';
$tag  = preg_match('/^\[\s*([\w-]+)/', $shortcode, $match) ? $match[1] : '';
if ($tag && shortcode_exists($tag)) {
	$feed = do_shortcode($shortcode);
}

$wrapper_attributes = array(
	'class'					=> 'b-feed' . ($spacing === 'page-top' ? ' b-feed--page-top' : ''),
	'data-view-label'		=> get_field('view_label') ?: __('view', 'zinc'),
	'data-load-more-label'	=> get_field('load_more_label') ?: __('load more', 'zinc'),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($heading) : ?>
  <div class="b-feed__header">
    <<?php echo $level; ?> class="b-feed__heading"><?php echo nl2br(esc_html($heading), false); ?></<?php echo $level; ?>>
  </div>
  <?php endif; ?>

  <?php if ($feed) : ?>
  <div class="b-feed__feed"><?php echo $feed; ?></div>

  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge"><span class="c-cursor__label"><?php echo esc_html(get_field('view_label') ?: __('view', 'zinc')); ?></span></div>
  </div>
  <?php elseif ($is_preview) : ?>
  <div class="b-feed__placeholder">
    <p class="b-feed__note">
      <?php if (!$shortcode) {
				esc_html_e('Add your feed’s shortcode in the block settings.', 'zinc');
			} elseif ($tag && !shortcode_exists($tag)) {
				printf(esc_html__('The %s shortcode isn’t available yet – install and connect the feed plugin (e.g. Smash Balloon Instagram Feed). The posts will show here.', 'zinc'), '<code>[' . esc_html($tag) . ']</code>');
			} ?>
    </p>
    <ul class="b-feed__tiles" aria-hidden="true">
      <?php for ($i = 0; $i < 9; $i++) : ?><li class="b-feed__tile"></li><?php endfor; ?>
    </ul>
  </div>
  <?php endif; ?>
</section>
