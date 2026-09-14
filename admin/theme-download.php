<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Download of the bundled "EazyMatch Recruitment" WordPress theme.
 *
 * Streams a zip of theme/eazymatch-recruitment/ (built on the fly when
 * ZipArchive is available) or the prebuilt eazymatch-recruitment.zip that ships
 * with the plugin. Triggered from the EazyMatch overview screen.
 */

add_action( 'admin_post_emol_download_theme', 'eazymatch_download_theme' );

/**
 * Slug of the bundled theme.
 */
if ( ! defined( 'EMOL_THEME_SLUG' ) ) {
	define( 'EMOL_THEME_SLUG', 'eazymatch-recruitment' );
}

/**
 * Is the bundled theme available for download (as a folder or prebuilt zip)?
 *
 * @return bool
 */
function eazymatch_theme_is_available() {
	return is_dir( EMOL_DIR . '/theme/' . EMOL_THEME_SLUG )
	       || file_exists( EMOL_DIR . '/' . EMOL_THEME_SLUG . '.zip' );
}

/**
 * Build the theme zip on the fly from the theme folder.
 *
 * @return string Path to a temporary zip file, or '' on failure.
 */
function eazymatch_build_theme_zip() {
	$theme_dir = EMOL_DIR . '/theme/' . EMOL_THEME_SLUG;

	if ( ! class_exists( 'ZipArchive' ) || ! is_dir( $theme_dir ) ) {
		return '';
	}

	$tmp = wp_tempnam( EMOL_THEME_SLUG . '.zip' );
	$zip = new ZipArchive();

	if ( $zip->open( $tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
		return '';
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $theme_dir, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ( $iterator as $file ) {
		$path = $file->getPathname();

		// Path inside the zip, prefixed with the theme folder name.
		$relative = ltrim( substr( $path, strlen( $theme_dir ) ), '/\\' );
		$relative = str_replace( '\\', '/', $relative );

		if ( '' === $relative || false !== strpos( $relative, '.DS_Store' ) ) {
			continue;
		}

		$local = EMOL_THEME_SLUG . '/' . $relative;

		if ( $file->isDir() ) {
			$zip->addEmptyDir( $local );
		} else {
			$zip->addFile( $path, $local );
		}
	}

	$zip->close();

	return $tmp;
}

/**
 * Handle the download request.
 */
function eazymatch_download_theme() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	check_admin_referer( 'emol_download_theme' );

	$prebuilt = EMOL_DIR . '/' . EMOL_THEME_SLUG . '.zip';

	$zip_file  = eazymatch_build_theme_zip();
	$temporary = ( '' !== $zip_file );

	if ( '' === $zip_file && file_exists( $prebuilt ) ) {
		$zip_file = $prebuilt;
	}

	if ( '' === $zip_file || ! file_exists( $zip_file ) ) {
		wp_die( esc_html__( 'Het themabestand kon niet worden gevonden.', 'Emol-3.0-identifier' ) );
	}

	// Clear any buffered output so the archive is not corrupted.
	while ( ob_get_level() > 0 ) {
		ob_end_clean();
	}

	nocache_headers();
	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename="' . EMOL_THEME_SLUG . '.zip"' );
	header( 'Content-Length: ' . filesize( $zip_file ) );

	readfile( $zip_file );

	if ( $temporary ) {
		@unlink( $zip_file );
	}

	exit;
}
