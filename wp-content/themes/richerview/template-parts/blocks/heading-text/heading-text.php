<?php
/**
 * Heading with Text block
 *
 * Large heading on the left, with a lead paragraph and body copy on the right.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading = get_field('heading');
$lead    = get_field('lead');
$body    = get_field('body');
$level   = get_field('heading_level') === 'h1' ? 'h1' : 'h2';

$wrapper_attributes = array(
	'class' => 'b-heading-text',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-heading-text__inner">
    <?php if ($heading) : ?>
    <<?php echo $level; ?> class="b-heading-text__heading"><?php echo nl2br(esc_html($heading), false); ?></<?php echo $level; ?>>
    <?php endif; ?>

    <?php if ($lead || $body) : ?>
    <div class="b-heading-text__content">
      <?php if ($lead) : ?>
      <p class="b-heading-text__lead"><?php echo nl2br(esc_html($lead), false); ?></p>
      <?php endif; ?>

      <?php if ($body) : ?>
      <div class="b-heading-text__body"><?php echo wp_kses_post($body); ?></div>
      <?php endif; ?>
    </div>
    <?php elseif ($is_preview && !$heading) : ?>
    <p><?php esc_html_e('Add a heading and text in the block settings.', 'zinc'); ?></p>
    <?php endif; ?>
  </div>
</section>
