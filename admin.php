<?php

if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

// add the update checker


/*
global $MyUpdateChecker;
$MyUpdateChecker = new emol_updater( 'https://wordpress.eazymatch.cloud/info.json',
	EMOL_DIR . '/eazymatch.php'
);
*/

//when activated do install function to create nescesserry settings
include( EMOL_DIR . '/install.php' );
register_activation_hook( __FILE__, 'eazymatch_install' );


add_action( 'admin_menu', 'eazymatch_admin_menu' );

/**
 * Add the eazyMatch Admin menu
 *
 */
function eazymatch_admin_menu() {
	add_menu_page( 'EazyMatch', EMOL_ADMIN_GLOBAL, 'manage_options', 'emol-admin', 'eazymatch_plugin_options', 'https://emol.eazymatch.cloud/favicon-16x16.png' );

	add_submenu_page( 'emol-admin', EMOL_ADMIN_JOB, EMOL_ADMIN_JOB, 'manage_options', 'emol-job', 'eazymatch_plugin_job' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_MANAGER, EMOL_ADMIN_MANAGER, 'manage_options', 'emol-manager', 'eazymatch_plugin_manager' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_CV, EMOL_ADMIN_CV, 'manage_options', 'emol-cv', 'eazymatch_plugin_cv' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_FORM, EMOL_ADMIN_FORM, 'manage_options', 'emol-forms', 'eazymatch_plugin_form' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_FORM_AVG, EMOL_ADMIN_FORM_AVG, 'manage_options', 'emol-avg', 'eazymatch_plugin_avg' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_ACC_FORM, EMOL_ADMIN_ACC_FORM, 'manage_options', 'emol-applicant-account', 'eazymatch_formmanager_applicant_account' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_STYLESHEET, EMOL_ADMIN_STYLESHEET, 'manage_options', 'emol-stylesheet', 'eazymatch_plugin_stylesheet' );
	add_submenu_page( 'emol-admin', EMOL_ADMIN_SHARING, EMOL_ADMIN_SHARING, 'manage_options', 'emol-sharing', 'eazymatch_plugin_sharing' );

	// add_submenu_page( 'emol-admin', EMOL_ADMIN_SHARING, EMOL_ADMIN_SHARING, 'manage_options', 'emol-sharing', 'eazymatch_plugin_sharing');
	// add_submenu_page( 'emol-admin', EMOL_ADMIN_ACCOUNT, EMOL_ADMIN_ACCOUNT, 'manage_options', 'emol-cv', 'eazymatch_plugin_account');
}

/**
 * include admin javascript
 */
emol_require::admin();

/**
 * Handle all vars that are configurable
 *
 */
include( EMOL_DIR . '/admin/forminstance.php' );
include( EMOL_DIR . '/admin/job.php' );
include( EMOL_DIR . '/admin/managers.php' );
include( EMOL_DIR . '/admin/cv.php' );
include( EMOL_DIR . '/admin/global.php' );
include( EMOL_DIR . '/admin/forms.php' );
include( EMOL_DIR . '/admin/avg.php' );
include( EMOL_DIR . '/admin/stylesheet.php' );
include( EMOL_DIR . '/admin/sharing.php' );
include( EMOL_DIR . '/admin/applicant-account.php' );
?>