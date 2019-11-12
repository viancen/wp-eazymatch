<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * inschrijfformulier... needs some improvements, handeling of this form is now done by the fakepost script
 *
 */
class emol_shortcode_apply {

	var $captcha;

	function getContent() {

		global $emol_side;

		global $trailingData;

		//isset in functions.php, by the custom title function
		global $emol_job;

		if ( is_null( $emol_job ) ) {
			eazymatch_connect();
			$emol_job_id = ( get_query_var( 'emol_job_id' ) );
			if ( ! empty( $emol_job_id ) ) {

				$emol_job_id = explode( '-', $emol_job_id );
				$emol_job_id = array_pop( $emol_job_id );

				$trunk = new EazyTrunk();

				// create a response array and add all the requests to the trunk
				$emol_job['job']            = &$trunk->request( 'job', 'getFullPublished', array( $emol_job_id ) );
				$emol_job['jobTexts']       = &$trunk->request( 'job', 'getCustomTexts', array( $emol_job_id ) );
				$emol_job['jobCompetences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $emol_job_id ) );

				// execute the trunk request
				$trunk->execute();
			}
		}

		return emol_get_apply_form( $emol_job['job'] );
		//getApplyForm();
	}


}

