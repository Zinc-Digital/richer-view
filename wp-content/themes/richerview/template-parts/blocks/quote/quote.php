<?php
/**
 * Quote block
 *
 * A large centred quote with the person's name and title underneath.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$quote = trim((string) get_field('quote'), " \t\n\r\"“”");
$name  = get_field('name');
$role  = get_field('role');

$wrapper_attributes = array(
	'class' => 'b-quote',
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($quote) : ?>
  <figure class="b-quote__inner">
    <blockquote class="b-quote__quote">
      <p>&ldquo;<?php echo nl2br(esc_html($quote), false); ?>&rdquo;</p>
    </blockquote>

    <?php if ($name || $role) : ?>
    <figcaption class="b-quote__author">
      <?php if ($name) : ?>
      <cite class="b-quote__name"><?php echo esc_html($name); ?></cite>
      <?php endif; ?>
      <?php if ($role) : ?>
      <span class="b-quote__role"><?php echo esc_html($role); ?></span>
      <?php endif; ?>
    </figcaption>
    <?php endif; ?>
  </figure>
  <?php elseif ($is_preview) : ?>
  <p class="b-quote__placeholder"><?php esc_html_e('Add the quote in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
