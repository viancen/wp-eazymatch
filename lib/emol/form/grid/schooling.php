<?php

/**
 *
 */
class emol_form_grid_schooling extends emol_form_grid {
	protected $fieldConfig = array(
		'id'               => array(
			'type' => 'emol_form_field_hidden'
		),
		'schoolingtype_id' => array(
			'type'  => 'emol_form_field_combo_schoolingtype',
			'label' => EMOL_ACCOUNT_APP_SCHOOLING_TYPE_LABEL
		),
		'institute'        => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_SCHOOLING_INSTITUTE_LABEL
		),
		'degree'           => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_SCHOOLING_DEGREE_LABEL
		),
		'startdate'        => array(
			'type'  => 'emol_form_field_date',
			'label' => EMOL_ACCOUNT_APP_SCHOOLING_STARTDATE_LABEL
		),
		'enddate'          => array(
			'type'  => 'emol_form_field_date',
			'label' => EMOL_ACCOUNT_APP_SCHOOLING_ENDDATE_LABEL
		)
	);
}