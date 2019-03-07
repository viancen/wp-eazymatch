<?php

class emol_form_field_date extends emol_form_field_inputdate {
	public function __construct( $config = array() ) {
		// make sure jquery ui is loaded, hence it will need this
		emol_require::jqueryUi();

		parent::__construct( $config );
	}
}