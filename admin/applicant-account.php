<?php
function eazymatch_formmanager_applicant_account() {
	$formAdmin = new emol_form_admin( 'emol_forminstance_applicant_account' );
	$formAdmin->display();

	return;
}
