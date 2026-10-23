<?php
/**
 * Team Slider block
 *
 * Heading with a "drag to…" hint and a draggable row of team members (photo,
 * name, role), finishing with an optional "want to join the crew?" card.
 * Dragging comes from assets/js/components/drag-slider.js.
 *
 * @var array $block      The block settings and attributes.
 * @var bool  $is_preview True during backend preview render.
 */

$heading    = get_field('heading');
$hint       = get_field('hint');
$members    = get_field('members');
$show_join  = get_field('show_join') !== false;
$join_title = get_field('join_heading');
$join_link  = get_field('join_link');
$start      = get_field('start_position') === 'start' ? 'start' : 'end';
$drag_label = get_field('drag_label');

$images = get_template_directory_uri() . '/assets/images/figma';

$wrapper_attributes = array(
	'class'			=> 'b-team',
	'data-start'	=> $start,
);
if (!empty($block['anchor'])) {
	$wrapper_attributes['id'] = $block['anchor'];
}
?>

<section <?php echo get_block_wrapper_attributes($wrapper_attributes); ?>>
  <?php if ($heading || $hint) : ?>
  <div class="b-team__header">
    <?php if ($heading) : ?>
    <h2 class="b-team__heading"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>
    <?php if ($hint) : ?>
    <p class="b-team__hint"><?php echo esc_html($hint); ?></p>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($members || ($show_join && $join_title)) : ?>
  <div class="b-team__slider" tabindex="0" role="region" aria-label="<?php echo esc_attr($heading ?: __('Team', 'zinc')); ?>">
    <ul class="b-team__track">
      <?php foreach ((array) $members as $member) :
				$position = in_array($member['image_position'], array('center', 'bottom'), true) ? $member['image_position'] : 'top';
			?>
      <li class="b-team__item">
        <figure class="b-team__member">
          <div class="b-team__photo">
            <?php if ($member['photo']) {
							echo wp_get_attachment_image($member['photo'], 'large', false, array(
								'class'		=> 'b-team__image b-team__image--' . $position,
								'sizes'		=> '(min-width: 1025px) 360px, 300px',
								'loading'	=> 'lazy',
								'alt'		=> '',
							));
						} ?>
          </div>
          <?php if ($member['name'] || $member['role']) : ?>
          <figcaption class="b-team__caption">
            <?php if ($member['name']) : ?>
            <span class="b-team__name"><?php echo esc_html($member['name']); ?></span>
            <?php endif; ?>
            <?php if ($member['role']) : ?>
            <span class="b-team__role"><?php echo esc_html($member['role']); ?></span>
            <?php endif; ?>
          </figcaption>
          <?php endif; ?>
        </figure>
      </li>
      <?php endforeach; ?>

      <?php if ($show_join && $join_title) : ?>
      <li class="b-team__item">
        <div class="b-team__join">
          <p class="b-team__join-heading"><?php echo esc_html($join_title); ?></p>
          <?php if ($join_link) : ?>
          <a class="b-team__join-link" href="<?php echo esc_url($join_link['url']); ?>"<?php if ($join_link['target']) : ?> target="<?php echo esc_attr($join_link['target']); ?>" rel="noopener"<?php endif; ?>>
            <span class="b-team__join-text">
              <?php echo esc_html($join_link['title'] ?: __('See career openings', 'zinc')); ?>
              <img class="b-team__join-arrow" src="<?php echo esc_url($images . '/arrow-small.svg'); ?>" width="17" height="12" alt="" />
            </span>
            <img class="b-team__join-underline" src="<?php echo esc_url($images . '/link-underline.svg'); ?>" width="219" height="3" alt="" />
          </a>
          <?php endif; ?>
        </div>
      </li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="c-cursor" aria-hidden="true">
    <div class="c-cursor__badge c-cursor__badge--drag">
      <span class="c-cursor__icon c-cursor__icon--chevron c-cursor__icon--chevron-left"><img src="<?php echo esc_url($images . '/cursor-icon-chevron.svg'); ?>" width="6" height="10" alt="" /></span>
      <span class="c-cursor__label"><?php echo esc_html($drag_label ?: __('drag', 'zinc')); ?></span>
      <span class="c-cursor__icon c-cursor__icon--chevron"><img src="<?php echo esc_url($images . '/cursor-icon-chevron.svg'); ?>" width="6" height="10" alt="" /></span>
    </div>
  </div>
  <?php elseif ($is_preview) : ?>
  <p class="b-team__placeholder"><?php esc_html_e('Add team members in the block settings.', 'zinc'); ?></p>
  <?php endif; ?>
</section>
