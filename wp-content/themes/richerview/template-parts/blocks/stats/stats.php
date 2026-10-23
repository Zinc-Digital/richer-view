<?php
/**
 * Stats block
 *
 * A row of big numbers with short labels, separated by thin dividers. Each
 * stat can change to a brand colour on hover. Plain numbers (e.g. "640+",
 * "12,000+") count to their value when scrolled into view
 * (assets/js/blocks/stats.js); anything else (e.g. "24/7") is shown as written.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$stats   = get_field('stats');
$animate = get_field('count_animation') !== false;

/**
 * Split a stat like "12,000+" into prefix / number / suffix for the count animation.
 * Returns null if it isn't a single plain number (e.g. "24/7").
 */
$parse_number = function ($text) {
	if (!preg_match('/^(\D*?)(\d{1,3}(?:,\d{3})+|\d+)(\.\d+)?(\D*)$/u', trim($text), $match)) {
		return null;
	}
	return array(
		'prefix'    => $match[1],
		'value'     => (float) str_replace(',', '', $match[2] . ($match[3] ?? '')),
		'separator' => strpos($match[2], ',') !== false,
		'decimals'  => !empty($match[3]) ? strlen($match[3]) - 1 : 0,
		'suffix'    => $match[4],
	);
};

$wrapper_attributes = array(
	'class' => 'b-stats',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($stats) : ?>
  <ul class="b-stats__list">
    <?php foreach ($stats as $stat) :
			$hover = in_array($stat['hover_colour'], array('yellow', 'blue', 'coral'), true) ? ' b-stats__item--' . $stat['hover_colour'] : '';
		?>
    <li class="b-stats__item<?php echo esc_attr($hover); ?>">
      <?php if ($stat['number'] !== '') :
				$count = $animate ? $parse_number($stat['number']) : null;
				$from  = isset($stat['count_from']) && $stat['count_from'] !== '' ? (float) $stat['count_from'] : 0;
			?>
      <p class="b-stats__number">
        <?php if ($count && $from !== $count['value']) : ?>
        <span class="b-stats__sr"><?php echo esc_html($stat['number']); ?></span>
        <span class="b-stats__count" aria-hidden="true" data-count-from="<?php echo esc_attr($from); ?>" data-count-to="<?php echo esc_attr($count['value']); ?>" data-decimals="<?php echo esc_attr($count['decimals']); ?>" data-separator="<?php echo $count['separator'] ? '1' : '0'; ?>" data-prefix="<?php echo esc_attr($count['prefix']); ?>" data-suffix="<?php echo esc_attr($count['suffix']); ?>"><?php echo esc_html($stat['number']); ?></span>
        <?php else : ?>
        <?php echo esc_html($stat['number']); ?>
        <?php endif; ?>
      </p>
      <?php endif; ?>
      <?php if ($stat['label']) : ?>
      <p class="b-stats__label"><?php echo esc_html($stat['label']); ?></p>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php elseif ($is_preview) : ?>
  <p><?php esc_html_e('Add stats in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
