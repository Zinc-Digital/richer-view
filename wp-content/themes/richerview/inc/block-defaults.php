<?php
/**
 * Block defaults
 *
 * Starting content for blocks when they're first added in the editor.
 */

/**
 * Client Logos – start with the logos from the design (in the design's order).
 *
 * Only used while the block has never had its logos saved, so removing logos
 * and saving isn't undone. The logo files live in assets/images/client-logos/
 * and are added to the Media Library the first time they're needed.
 */
function zinc_client_logos_defaults() {
	return array(
		array('the-1-1-diet.png', 'The 1:1 Diet'),
		array('dpd.png', 'DPD'),
		array('first-bus.png', 'First Bus'),
		array('axiom-hospitality.png', 'Axiom Hospitality'),
		array('saint-gobain.png', 'Saint-Gobain'),
		array('the-1-1-diet.png', 'The 1:1 Diet'),
		array('odeon.png', 'Odeon'),
		array('citysprint.png', 'CitySprint'),
		array('vitality.png', 'Vitality'),
		array('jewson.png', 'Jewson'),
		array('nhs-supply-chain.png', 'NHS Supply Chain'),
		array('cef.png', 'CEF'),
	);
}

function zinc_client_logos_default_value($value, $post_id, $field) {
	if (!empty($value) || strpos((string) $post_id, 'block_') !== 0) {
		return $value;
	}

	// A brand new block has no "logos" entry in its data at all. If the entry is
	// there but empty, the logos were removed on purpose – leave it empty.
	$data = acf_get_meta($post_id);
	if (is_array($data) && array_key_exists('logos', $data)) {
		return $value;
	}

	$logo_key = 'field_6a1b2c3d4e603';
	$link_key = 'field_6a1b2c3d4e604';
	$rows     = array();

	foreach (zinc_client_logos_defaults() as list($file, $name)) {
		$id = zinc_get_theme_image_attachment('assets/images/client-logos/' . $file, 'Client logo: ' . $name, $name);
		if ($id) {
			$rows[] = array($logo_key => $id, $link_key => '');
		}
	}

	return $rows ?: $value;
}
add_filter('acf/load_value/key=field_6a1b2c3d4e602', 'zinc_client_logos_default_value', 20, 3);

/**
 * Contact – list the site's Gravity Forms in the block's "Form" setting, and
 * default to the first one.
 */
function zinc_contact_form_choices($field) {
	$field['choices'] = array();
	if (class_exists('GFAPI')) {
		foreach (GFAPI::get_forms() as $form) {
			$field['choices'][$form['id']] = $form['title'];
		}
	}
	if ($field['choices'] && empty($field['default_value'])) {
		$field['default_value'] = array_key_first($field['choices']);
	}
	return $field;
}
add_filter('acf/load_field/key=field_6abfd25eae010', 'zinc_contact_form_choices');
