<?php

/**
 *
 */
class emol_form_grid_experience extends emol_form_grid {
	protected $fieldConfig = array(
		'id'                => array(
			'type' => 'emol_form_field_hidden'
		),
		'experiencetype_id' => array(
			'type'  => 'emol_form_field_combo_experiencetype',
			'label' => EMOL_ACCOUNT_APP_EXPERIENCE_TYPE_LABEL
		),
		'function'          => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_EXPERIENCE_FUNCTION_LABEL
		),
		'company'           => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_EXPERIENCE_COMPANY_LABEL
		),
		'startdate'         => array(
			'type'  => 'emol_form_field_date',
			'label' => EMOL_ACCOUNT_APP_EXPERIENCE_STARTDATE_LABEL
		),
		'enddate'           => array(
			'type'  => 'emol_form_field_date',
			'label' => EMOL_ACCOUNT_APP_EXPERIENCE_ENDDATE_LABEL
		),
		'description'       => array(
			'type'        => 'emol_form_field_textarea',
			'placeholder' => EMOL_ACCOUNT_APP_EXPERIENCE_DESCRIPTION_LABEL,
			'label'       => false
		)
	);
}