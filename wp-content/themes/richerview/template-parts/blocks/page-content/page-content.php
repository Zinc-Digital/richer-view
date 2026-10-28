<?php
/**
 * Content block
 *
 * A page heading (with an optional "last updated" date) and a column of text
 * written with the editor's own blocks – headings, paragraphs, lists, tables,
 * quotes. For pages like terms and conditions or the privacy policy.
 *
 * @var array  $block      The block settings and attributes.
 * @var bool   $is_preview True during backend preview render.
 * @var string $content    The blocks inside (front end).
 */

$show_heading = get_field('field_6b7b3c4d5b005') !== false;
$show_updated = get_field('field_6b7b3c4d5b006') !== false;
$spacing      = get_field('spacing') === 'standard' ? 'standard' : 'page-top';

// Heading: this block's own, or the page title (a new, unsaved page has none yet)
$heading = get_field('heading');
if (!$heading && get_post_status() !== 'auto-draft') {
	$heading = get_the_title();
}
if (!$heading && $is_preview) {
	$heading = __('page title', 'zinc');
}

// Blocks it starts with, and the ones that can go inside
$allowed  = array('core/heading', 'core/paragraph', 'core/list', 'core/list-item', 'core/table', 'core/quote', 'core/separator', 'core/buttons', 'core/button', 'core/details');
$template = array(
	array('core/heading', array('level' => 2, 'content' => '1. introduction')),
	array('core/paragraph', array('placeholder' => __('Start writing…', 'zinc'))),
);

$wrapper_attributes = array(
	'class' => 'b-content' . ($spacing === 'page-top' ? ' b-content--page-top' : ''),
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <div class="b-content__inner">
    <?php if ($show_heading && $heading) : ?>
    <header class="b-content__header">
      <h1 class="b-content__heading"><?php echo nl2br(esc_html($heading), false); ?></h1>
      <?php if ($show_updated && get_post_status() !== 'auto-draft') : ?>
      <p class="b-content__updated"><?php printf(esc_html__('last updated %s', 'zinc'), esc_html(strtolower(get_the_modified_date('j F Y')))); ?></p>
      <?php endif; ?>
    </header>
    <?php endif; ?>

    <div class="b-content__body">
      <InnerBlocks allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed)); ?>" template="<?php echo esc_attr(wp_json_encode($template)); ?>" />
    </div>
  </div>
</section>
