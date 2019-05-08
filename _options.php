<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// We need to escape these fuckin chars...

function simple_lineup__section(){
	/*  
	 *  Creates custom general-settings option. In those user can enable
 	 *  for which content type he wants to enable sheduling functionality.
	 */

	$settingsPageDisplay = 'general';
	$sectionID = 'simple_lineup__section';

	add_settings_section(
		$sectionID,
		'Sheduling settings',  // Section Title
		'simple_lineup__settings_description',  // Callback
		$settingsPageDisplay
	);
	
    $content_type_choices = [];

    foreach(get_post_types(['public' => true, 'publicly_queryable' => true]) as $ctype_key => $ctype_name){
        if($ctype_name === 'attachment'){
            continue;
        }
        $content_type_choices[] = $ctype_name;
    }
	
	$fieldsData = [
		[
			'id' => 'simple_lineup__festival_start_date',  // TODO: should be constant
			'label' => 'Festival Start date',
			'type' => 'date',
		], [
			'id' => 'simple_lineup__festival_end_date',  // TODO: should be constant
			'label' => 'Festival End date',
			'type' => 'date',
		], [
			'id' => SIMPLE_LINEUP__CONTENT_TYPES_FIELD,
			'label' => 'Content types',
			'type' => 'checkbox',
		]
	];

	foreach($fieldsData as $field){
		$renderFunctionName = null;  // Ideally this should never stay null.

		switch($field['type']){  // There may come multiple types.
			case 'checkbox':
				$renderFunctionName = 'simple_lineup__settings_field_render';
				break;
			default:
				$renderFunctionName = 'simple_lineup__default_field_render';
		}

		add_settings_field(
			// NOTE: This does not work for separate choices. If field has the same ID, it will be overridden by the last choice.
			$field['id'],
			$field['label'],
			$renderFunctionName,  // !important - Callback where the args go!
			$settingsPageDisplay,  // Page it will be displayed (General Settings)
			$sectionID,
			[$field['id'], $field['type'], $content_type_choices] // The $args
		);
		register_setting('general', $field['id'], 'esc_attr');
	}

}


function simple_lineup__settings_description() {  // Section Callback
	echo '<p>Specify festival starting and ending date. Check which content types should be sheduling enabled.</p>';  
}


function simple_lineup__settings_field_render($args){
	$field_id = $args[0];
	$field_type = $args[1];
	$content_type_choices = $args[2];

	$serialized_option_value = get_option($field_id); // if not filled, returns false!
	$old_option_values = $serialized_option_value === false ? [] : json_decode($serialized_option_value);

	$output = '<div class="js-frontend-jsonizer"><table class="form-table"><tbody>';
	
	foreach($content_type_choices as $content_type) {
		$checked = in_array($content_type, $old_option_values) ? ' checked' : '';

		$choice_id = $field_id . '_' . $content_type;
		$output .= '<tr>';
		$output .= '<th><label for="' . $choice_id . '">' . ucwords($content_type) . '</label></th>';
		$output .= '<td><input type="' . $field_type . '" id="' . $choice_id . '" class="js-frontend-jsonizer--option" value="' . $content_type . '"' . $checked . '></td>';
		$output .= '</tr>';
	}

	$output .= '</table>';
	$output .= '<input type="hidden" class="js-frontend-jsonizer--json-field" name="' . $field_id . '" value="' . htmlentities($serialized_option_value) . '">';
	$output .= '</div>';
	echo $output;
}

add_action('admin_init', 'simple_lineup__section');


add_filter('pre_update_option_simple_lineup__content_types', function($value, $old_value, $option_name) {
	// This is getting serialized on frontend. We need to do additional work before saving json-field to DB.
	return html_entity_decode($value);
}, 10, 3);


function simple_lineup__default_field_render($args) {  // Textbox Callback
    $field_id = $args[0];
    $fieldType = $args[1];
    $option = get_option($field_id);
    echo '<input type="' . $fieldType . '" id="'. $field_id .'" name="'. $field_id .'" value="' . $option . '" required>';
}
