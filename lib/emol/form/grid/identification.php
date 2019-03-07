<?php

/**
 *
 */
class emol_form_grid_identification extends emol_form_grid {
	protected $fieldConfig = array(
		'id'                    => array(
			'type' => 'emol_form_field_hidden'
		),
		'identificationtype_id' => array(
			'type'  => 'emol_form_field_combo_identificationtype',
			'label' => EMOL_ACCOUNT_APP_IDENTIFICATION_TYPE_LABEL
		),
		'number'                => array(
			'type'  => 'emol_form_field_text',
			'label' => EMOL_ACCOUNT_APP_IDENTIFICATION_NUMBER_LABEL
		),
		'experationdate'        => array(
			'type'  => 'emol_form_field_date',
			'label' => EMOL_ACCOUNT_APP_IDENTIFICATION_EXPARATIONDATE_LABEL
		),
		'document'              => array(
			'type'  => 'emol_form_field_file',
			'label' => EMOL_ACCOUNT_APP_IDENTIFICATION_DOCUMENT_LABEL
		)
	);

	public function __construct( $config = array() ) {
		$this->fieldConfig['document']['downloadUrl'] = '/' . get_option( 'emol_account_url' ) . '/identificationdoc?id={id}';
		parent::__construct( $config );
	}

	public function setValue( $value ) {
		foreach ( $value as &$row ) {
			if ( isset( $row['Document'] ) ) {
				$row['document'] = $row['Document'];
				unset( $row['Document'] );
			}
		}


		return parent::setValue( $value );
	}
}