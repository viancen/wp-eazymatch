<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

global $eazymatchOptions;

function eazymatch_install() {
	//if there are styles in the database, but no file exists
	if ( get_option( 'emol_stylesheet' ) ) {
		//@file_put_contents(EMOL_DIR.'/css/style.user.css',get_option('emol_stylesheet'));
		$uploadinfo = wp_upload_dir();
		file_put_contents( $uploadinfo['basedir'] . '/eazymatch.style.css', stripcslashes( get_option( 'emol_stylesheet' ) ) );
	}
}