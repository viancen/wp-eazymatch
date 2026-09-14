<?php

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

/**
 * Regression test for the job-teaser-text shortcode attribute.
 *
 * Covers the pure mapping (emol_jobteaser) and the shortcode registry, without
 * WordPress or an API connection.
 *
 * Run: php tests/job-teaser-text-regression.php
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

require_once EMOL_DIR . '/lib/emol/shortcoderegistry.php';
require_once EMOL_DIR . '/lib/emol/jobteaser.php';

// --- registry -------------------------------------------------------------

emol_test_assert_same(
	array( 'jobs', 'searchjobs', 'jobpage', 'job', 'apply', 'cv', 'react' ),
	emol_shortcoderegistry::getViews(),
	'Registry must list every view the shortcode handler accepted before.'
);
emol_test_assert_same( true, emol_shortcoderegistry::isView( 'jobs' ), 'jobs is a view' );
emol_test_assert_same( false, emol_shortcoderegistry::isView( 'nope' ), 'unknown view is rejected' );
emol_test_assert_same( false, emol_shortcoderegistry::isView( '' ), 'empty view is rejected' );

foreach ( emol_shortcoderegistry::getAll() as $view => $info ) {
	emol_test_assert_same( true, is_string( $info['description'] ) && $info['description'] !== '', "$view has a description" );
	foreach ( $info['params'] as $name => $param ) {
		foreach ( array( 'type', 'required', 'default', 'description', 'example' ) as $key ) {
			emol_test_assert_same( true, array_key_exists( $key, $param ), "$view.$name has '$key'" );
		}
	}
}

$attr = emol_shortcoderegistry::ATTR_JOB_TEASER_TEXT;
emol_test_assert_same( 'job-teaser-text', $attr, 'attribute name' );
emol_test_assert_same( true, isset( emol_shortcoderegistry::getAll()['jobs']['params'][ $attr ] ), 'jobs documents job-teaser-text' );
emol_test_assert_same( true, isset( emol_shortcoderegistry::getAll()['jobpage']['params'][ $attr ] ), 'jobpage documents job-teaser-text' );

// --- fromAtts -------------------------------------------------------------

emol_test_assert_same( '', emol_jobteaser::fromAtts( array() ), 'no attribute -> empty' );
emol_test_assert_same( '', emol_jobteaser::fromAtts( 'not an array' ), 'non-array atts -> empty' );
emol_test_assert_same( '', emol_jobteaser::fromAtts( array( $attr => '  ' ) ), 'blank attribute -> empty' );
emol_test_assert_same( '12', emol_jobteaser::fromAtts( array( $attr => ' 12 ' ) ), 'attribute is trimmed' );
emol_test_assert_same( '12', emol_jobteaser::fromAtts( array( 'job_teaser_text' => '12' ) ), 'underscore variant accepted' );

// --- fixtures (shape of form.getJobTextDescription / job.getCustomTexts) --

$definitions = array(
	array( 'id' => 10, 'label' => 'Functieomschrijving' ),
	array( 'id' => 12, 'label' => 'Wij bieden' ),
	array( 'id' => 13, 'label' => 'Wat vragen wij' ),
);

$textsPerJob = array(
	101 => array(
		array( 'title' => 'Functieomschrijving', 'value' => 'Je gaat aan de slag als developer.' ),
		array( 'title' => 'Wij bieden', 'value' => "Een <b>goed</b> salaris\nen een laptop." ),
	),
	102 => array(
		array( 'title' => 'Functieomschrijving', 'value' => 'Chauffeur.' ),
		array( 'title' => 'Wij bieden', 'value' => '' ),          // empty -> fallback
	),
	103 => array(
		array( 'title' => 'Functieomschrijving', 'value' => 'Kok.' ), // block absent -> fallback
	),
	104 => array(
		array( 'title' => 'Wij bieden', 'value' => '<p> </p>' ),      // only markup -> fallback
	),
	105 => 'error: null response',                                     // broken response -> fallback
);

// --- resolveTitle ----------------------------------------------------------

emol_test_assert_same( 'Wij bieden', emol_jobteaser::resolveTitle( '12', $definitions ), 'numeric id -> label' );
emol_test_assert_same( 'Wij bieden', emol_jobteaser::resolveTitle( 12, $definitions ), 'int id -> label' );
emol_test_assert_same( null, emol_jobteaser::resolveTitle( '99', $definitions ), 'unknown id -> null' );
emol_test_assert_same( null, emol_jobteaser::resolveTitle( '12', null ), 'id without definitions -> null' );
emol_test_assert_same( 'Wij bieden', emol_jobteaser::resolveTitle( 'Wij bieden', array() ), 'title passes through' );
emol_test_assert_same( null, emol_jobteaser::resolveTitle( '', $definitions ), 'empty -> null' );

// --- pickText --------------------------------------------------------------

emol_test_assert_same( "Een <b>goed</b> salaris\nen een laptop.", emol_jobteaser::pickText( 'Wij bieden', $textsPerJob[101] ), 'exact title' );
emol_test_assert_same( "Een <b>goed</b> salaris\nen een laptop.", emol_jobteaser::pickText( 'wij BIEDEN ', $textsPerJob[101] ), 'title match is case/space insensitive' );
emol_test_assert_same( null, emol_jobteaser::pickText( 'Wij bieden', $textsPerJob[102] ), 'empty value -> null' );
emol_test_assert_same( null, emol_jobteaser::pickText( 'Wij bieden', $textsPerJob[103] ), 'missing block -> null' );
emol_test_assert_same( null, emol_jobteaser::pickText( 'Wij bieden', $textsPerJob[104] ), 'markup-only value -> null' );
emol_test_assert_same( null, emol_jobteaser::pickText( 'Wij bieden', $textsPerJob[105] ), 'non-array texts -> null' );
emol_test_assert_same( null, emol_jobteaser::pickText( null, $textsPerJob[101] ), 'null title -> null' );

// --- map -------------------------------------------------------------------

emol_test_assert_same(
	array( 101 => "Een <b>goed</b> salaris\nen een laptop." ),
	emol_jobteaser::map( '12', $definitions, $textsPerJob ),
	'map by id: only jobs with a usable block are returned'
);
emol_test_assert_same(
	array( 101 => 'Je gaat aan de slag als developer.', 102 => 'Chauffeur.', 103 => 'Kok.' ),
	emol_jobteaser::map( 'Functieomschrijving', $definitions, $textsPerJob ),
	'map by title'
);
emol_test_assert_same( array(), emol_jobteaser::map( '99', $definitions, $textsPerJob ), 'unknown id -> nothing (fallback to description)' );
emol_test_assert_same( array(), emol_jobteaser::map( '', $definitions, $textsPerJob ), 'empty reference -> nothing' );

// --- fetchForJobs short-circuits without touching the API -----------------

emol_test_assert_same( array(), emol_jobteaser::fetchForJobs( array( array( 'id' => 1 ) ), '' ), 'no attribute -> no API call' );
emol_test_assert_same( array(), emol_jobteaser::fetchForJobs( array(), '12' ), 'no jobs -> no API call' );

echo "job-teaser-text regression test passed.\n";
