<?php
/**
 * FAQ block
 *
 * Heading on the left and a list of questions that open to show their answer.
 * The open question turns yellow; a round badge ("open" / "close") replaces the
 * mouse pointer over the questions (assets/js/blocks/faq.js).
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading     = get_field('heading');
$items       = get_field('items') ?: array();
$divider     = (bool) get_field('divider');
$open_label  = get_field('open_label');
$close_label = get_field('close_label');
$schema      = get_field('schema') !== false;

$id = 'faq-' . substr(md5($block['id'] ?? uniqid()), 0, 8);

// The question that starts open, if any
$open_index = null;
foreach ($items as $index => $item) {
	if (!empty($item['open'])) {
		$open_index = $index;
		break;
	}
}

$wrapper_attributes = array(
	'class'				=> 'b-faq' . ($divider ? ' b-faq--divider' : ''),
	'data-open-label'	=> $open_label ?: __('open', 'zinc'),
	'data-close-label'	=> $close_label ?: __('close', 'zinc'),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-faq__inner">
    <?php if ($heading) : ?>
    <h2 class="b-faq__heading"><?php echo nl2br(esc_html($heading), false); ?></h2>
    <?php endif; ?>

    <?php if ($items) : ?>
    <ul class="b-faq__list">
      <?php foreach ($items as $index => $item) :
				$is_open = $index === $open_index;
				$item_id = $id . '-' . $index;
			?>
      <li class="b-faq__item<?php if ($item['answer']) echo ' has-answer'; ?><?php if ($is_open) echo ' is-open'; ?>">
        <h3 class="b-faq__question">
          <button type="button" class="b-faq__toggle" id="<?php echo esc_attr($item_id); ?>-toggle" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($item_id); ?>-panel">
            <?php echo esc_html($item['question']); ?>
          </button>
        </h3>
        <div class="b-faq__panel" id="<?php echo esc_attr($item_id); ?>-panel" role="region" aria-labelledby="<?php echo esc_attr($item_id); ?>-toggle">
          <div class="b-faq__panel-inner">
            <?php if ($item['answer']) : ?>
            <div class="b-faq__answer"><?php echo wp_kses_post($item['answer']); ?></div>
            <?php endif; ?>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php elseif ($is_preview) : ?>
    <p class="b-faq__placeholder"><?php esc_html_e('Add questions in the block settings.', 'zinc'); ?></p>
    <?php endif; ?>
  </div>

  <?php if ($items) : ?>
  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge"></div>
  </div>
  <?php endif; ?>

  <?php
	// FAQ structured data, so search engines can show the questions in results
	if ($schema && $items && !$is_preview) {
		$entities = array();
		foreach ($items as $item) {
			if (empty($item['question']) || empty($item['answer'])) continue;
			$entities[] = array(
				'@type'				=> 'Question',
				'name'				=> wp_strip_all_tags($item['question']),
				'acceptedAnswer'	=> array('@type' => 'Answer', 'text' => wp_kses($item['answer'], array('a' => array('href' => array()), 'br' => array(), 'p' => array(), 'strong' => array(), 'em' => array(), 'ul' => array(), 'ol' => array(), 'li' => array()))),
			);
		}
		if ($entities) {
			echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities)) . '</script>';
		}
	}
	?>
</section>
