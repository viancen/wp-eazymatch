<?php

/**
 *
 */
class emol_form_grid_skill extends emol_form_grid {
	protected $fieldConfig = array(
		'id'          => array(
			'type' => 'emol_form_field_hidden'
		),
		'title'       => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_SKILL_TITLE_LABEL
		),
		'description' => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_SKILL_DESCRIPTION_LABEL
		)
	);
}