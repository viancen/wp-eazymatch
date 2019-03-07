<?php
add_action( 'wp_ajax_emol-formstance-dummy', 'emol_formstance_dummy' );

function emol_formstance_dummy() {
	// get the submitted parameters
	$forminstanceId = $_POST['forminstanceId'];
	$fieldId        = $_POST['fieldId'];

	$formInstance = emol_form_manager::getInstance()->get( $forminstanceId );

	$dummyField = $formInstance->getDummyField( $fieldId );

	// echo '<b class="emol_move_handler" style="cursor: pointer;">&uarr;&darr;</b> &nbsp; ';
	echo '<a class="emol_remove_handler" href="#">' . __( 'Del' ) . '</a> &nbsp; ';
	echo $dummyField->getConfigElement();

	exit;
}