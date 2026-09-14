<?php

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

/**
 * Regression test for per-text-block visibility (detail / list).
 *
 * Run: php tests/job-text-display-regression.php
 */

function emol_test_assert_same( $expected, $actual, $message ) {
	if ( $expected === $actual ) {
		return;
	}

	throw new RuntimeException(
		$message . "\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true )
	);
}

if ( ! defined( 'EMOL_DIR' ) ) {
	define( 'EMOL_DIR', dirname( __DIR__ ) );
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default = false ) {
		return $default;
	}
}

if ( ! function_exists( 'emol_firstWords' ) ) {
	function emol_firstWords( $text ) {
		return $text;
	}
}

if ( ! class_exists( 'emol_markdown', false ) ) {
	class emol_markdown {
		public static function parseLists( $text ) {
			return $text;
		}
	}
}

require_once EMOL_DIR . '/lib/emol/jobtext.php';

$empty = array();
emol_test_assert_same( true, emol_jobtext::isVisible( 'Functieomschrijving', 'detail', $empty ), 'empty map: detail on' );
emol_test_assert_same( false, emol_jobtext::isVisible( 'Functieomschrijving', 'list', $empty ), 'empty map: list off' );
emol_test_assert_same( false, emol_jobtext::hasVisibleIn( 'list', $empty ), 'empty map: no list blocks' );
emol_test_assert_same( true, emol_jobtext::hasVisibleIn( 'detail', $empty ), 'empty map: detail has blocks' );

$map = array(
	'Functieomschrijving' => array( 'detail' => 1, 'list' => 0 ),
	'Bedrijfsprofiel'     => array( 'detail' => 0, 'list' => 1 ),
);

emol_test_assert_same( true, emol_jobtext::isVisible( 'Functieomschrijving', 'detail', $map ), 'saved detail on' );
emol_test_assert_same( false, emol_jobtext::isVisible( 'Functieomschrijving', 'list', $map ), 'saved list off' );
emol_test_assert_same( false, emol_jobtext::isVisible( 'Bedrijfsprofiel', 'detail', $map ), 'saved detail off' );
emol_test_assert_same( true, emol_jobtext::isVisible( 'bedrijfsprofiel', 'list', $map ), 'title match is case insensitive' );
emol_test_assert_same( true, emol_jobtext::isVisible( 'Afbeeldingen', 'detail', $map ), 'unknown block defaults on for detail' );
emol_test_assert_same( false, emol_jobtext::isVisible( 'Afbeeldingen', 'list', $map ), 'unknown block defaults off for list' );
emol_test_assert_same( true, emol_jobtext::hasVisibleIn( 'list', $map ), 'one list flag on' );

$allOff = array(
	'Functieomschrijving' => array( 'detail' => 0, 'list' => 0 ),
);
emol_test_assert_same( true, emol_jobtext::allFlagsOff( $allOff ), 'all-zero map is detected' );
emol_test_assert_same( true, emol_jobtext::flagIsOn( array( '0', '1' ) ), 'array flag with 1 is on' );
emol_test_assert_same( false, emol_jobtext::flagIsOn( array( '0', '0' ) ), 'array flag without 1 is off' );
emol_test_assert_same( false, emol_jobtext::hasVisibleIn( 'list', $allOff ), 'all list flags off' );

$blocks = array(
	array( 'title' => 'Functieomschrijving', 'value' => 'Je gaat aan de slag.' ),
	array( 'title' => 'Bedrijfsprofiel', 'value' => 'Wij zijn een bureau.' ),
	array( 'title' => 'Functie eisen', 'value' => '' ),
);

$filtered = emol_jobtext::filterBlocks( $blocks, 'list', $map );
emol_test_assert_same( 1, count( $filtered ), 'list filter keeps one block' );
emol_test_assert_same( 'Bedrijfsprofiel', $filtered[0]['title'], 'list filter keeps bedrijfsprofiel' );

emol_test_assert_same( 'Functieomschrijving', emol_jobtext::blockTitle( array( 'label' => 'Functieomschrijving', 'value' => 'x' ) ), 'title fallback to label' );

$html = emol_jobtext::renderHtml( $blocks, 'detail' );
if ( strpos( $html, 'emol-job-textblocks' ) === false || strpos( $html, 'emol-job-textblock-heading' ) === false ) {
	throw new RuntimeException( 'detail HTML must wrap text blocks independently of the theme' );
}
if ( strpos( $html, 'class="emol-job-heading"' ) !== false ) {
	throw new RuntimeException( 'text block titles must not reuse emol-job-heading' );
}
if ( strpos( emol_jobtext::frontCss(), 'display:block!important' ) === false ) {
	throw new RuntimeException( 'plugin CSS must force text blocks visible' );
}

$desc = emol_jobtext::renderDescriptionHtml( 'Korte meta tekst' );
if ( strpos( $desc, 'emol-job-meta-description' ) === false || strpos( $desc, 'emol-job-page-description' ) !== false ) {
	throw new RuntimeException( 'meta description must use a plugin class that themes do not hide' );
}

echo "job-text-display regression test passed.\n";
