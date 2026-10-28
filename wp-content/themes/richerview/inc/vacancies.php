<?php
/**
 * Vacancy helpers
 *
 * Shared by the Vacancy List block and the Service Hero on a vacancy's page.
 */

/**
 * A closing date as shown in the design: "30 sept", "14 oct", "1 jan".
 *
 * @param string $ymd Date from the Closing date field (Ymd).
 * @return string Formatted date, or '' if there's none.
 */
function zinc_format_closing_date($ymd) {
	$date = $ymd ? DateTime::createFromFormat('Ymd', $ymd, wp_timezone()) : false;
	if (!$date) return '';
	$label = strtolower(wp_date('j M', $date->getTimestamp()));
	return preg_replace('/\bsep\b/', 'sept', $label);
}

/**
 * Whether a vacancy is still open: no closing date, or the closing date is today or later.
 *
 * @param int $post_id Vacancy ID.
 * @return bool
 */
function zinc_vacancy_is_open($post_id) {
	$ymd = get_field('closing_date', $post_id);
	return !$ymd || $ymd >= wp_date('Ymd');
}

/**
 * Published vacancies, soonest closing first (open-until-filled roles last).
 *
 * @param bool $open_only Leave out roles past their closing date.
 * @return WP_Post[]
 */
function zinc_get_vacancies($open_only = true) {
	$posts = get_posts(array(
		'post_type'			=> 'vacancy',
		'post_status'		=> 'publish',
		'posts_per_page'	=> -1,
		'no_found_rows'		=> true,
	));
	if ($open_only) {
		$posts = array_filter($posts, function ($post) {
			return zinc_vacancy_is_open($post->ID);
		});
	}
	usort($posts, function ($a, $b) {
		$a_date = get_field('closing_date', $a->ID) ?: '99999999';
		$b_date = get_field('closing_date', $b->ID) ?: '99999999';
		return strcmp($a_date, $b_date) ?: strcasecmp($a->post_title, $b->post_title);
	});
	return array_values($posts);
}
