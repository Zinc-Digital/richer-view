<?php
/**
 * Client Logos block
 *
 * A grid of client logos separated by thin divider lines.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

// Looked up by field key (not name) so a brand new block still gets the default
// logos from inc/block-defaults.php before anything has been saved
$logos   = get_field('field_6a1b2c3d4e602');
$columns = (int) get_field('columns');
$columns = in_array($columns, array(3, 4, 5, 6), true) ? $columns : 6;

$wrapper_attributes = array(
	'class' => 'b-client-logos b-client-logos--cols-' . $columns,
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($logos) : ?>
  <div class="b-client-logos__inner">
    <ul class="b-client-logos__grid">
      <?php foreach ($logos as $item) :
				if (empty($item['logo'])) continue;

				$link  = $item['link'];
				$image = wp_get_attachment_image($item['logo'], 'medium', false, array(
					'class'		=> 'b-client-logos__logo',
					'loading'	=> 'lazy',
				));
			?>
      <li class="b-client-logos__item">
        <?php if ($link) : ?>
        <a class="b-client-logos__link" href="<?php echo esc_url($link['url']); ?>" <?php if ($link['target']) : ?>target="<?php echo esc_attr($link['target']); ?>" rel="noopener" <?php endif; ?><?php if ($link['title']) : ?>aria-label="<?php echo esc_attr($link['title']); ?>" <?php endif; ?>>
          <?php echo $image; ?>
        </a>
        <?php else : ?>
          <?php echo $image; ?>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php elseif ($is_preview) : ?>
  <p class="b-client-logos__placeholder"><?php esc_html_e('Add client logos in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
