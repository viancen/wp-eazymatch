<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Central registry of all [eazymatch] shortcode views and their parameters.
 *
 * Used by:
 *  - emol_shortcodehandler  (which views are allowed)
 *  - admin/shortcodes.php   (the "Shortcodes" overview page in the CMS)
 *
 * Add a new view or parameter here and it automatically shows up in the CMS.
 */
class emol_shortcoderegistry {

	/**
	 * Name of the shortcode attribute that selects a job text block as teaser
	 * for job overviews (jobs / jobpage).
	 */
	const ATTR_JOB_TEASER_TEXT = 'job-teaser-text';

	/**
	 * Returns all views, keyed by the value of the "view" attribute.
	 *
	 * Each view: array(
	 *   'description' => string,
	 *   'params'      => array( name => array( 'type', 'required', 'default', 'description', 'example' ) ),
	 *   'examples'    => string[]   complete shortcode examples
	 * )
	 *
	 * @return array
	 */
	static public function getAll() {
		$teaserParam = array(
			'type'        => 'tekstblok-id',
			'required'    => false,
			'default'     => '(omschrijving van de vacature)',
			'description' => 'Wijst één van de tekstblokken van de vacature aan als korte tekst in het overzicht. '
			                 . 'Gebruik het id van het tekstblok (zie de lijst onderaan deze pagina) of de originele EazyMatch-titel van het blok. '
			                 . 'Is het blok bij een vacature leeg of onbekend, dan wordt de gewone omschrijving getoond. '
			                 . 'De korte tekst wordt alleen getoond wanneer "Korte omschrijving in zoekresultaat?" onder Vacatures aan staat.',
			'example'     => self::ATTR_JOB_TEASER_TEXT . '="12"',
		);

		return array(
			'jobs'       => array(
				'description' => 'Compacte lijst met gepubliceerde vacatures. Geschikt voor de homepage of landingspagina\'s.',
				'params'      => array(
					'limit'                    => array(
						'type'        => 'geheel getal',
						'required'    => false,
						'default'     => '"Aantal resultaten per pagina" (Vacatures), anders 5',
						'description' => 'Maximaal aantal getoonde vacatures.',
						'example'     => 'limit="10"',
					),
					'competences'              => array(
						'type'        => 'lijst van id\'s, kommagescheiden',
						'required'    => false,
						'default'     => '(geen filter)',
						'description' => 'Toont alleen vacatures met deze competentie-id\'s uit het EazyMatch-matchprofiel. Niet-numerieke waarden worden genegeerd.',
						'example'     => 'competences="31,152"',
					),
					self::ATTR_JOB_TEASER_TEXT => $teaserParam,
				),
				'examples'    => array(
					'[eazymatch view="jobs"]',
					'[eazymatch view="jobs" limit="10" competences="31,152"]',
					'[eazymatch view="jobs" limit="5" ' . self::ATTR_JOB_TEASER_TEXT . '="12"]',
				),
			),
			'searchjobs' => array(
				'description' => 'Zoekformulier voor vacatures. Verstuurt naar de "Job zoek page" die onder Vacatures is ingesteld.',
				'params'      => array(
					'settings' => array(
						'type'        => 'sleutel=waarde paren, gescheiden door |',
						'required'    => true,
						'default'     => '',
						'description' => 'Ondersteunde sleutels: title (kop boven het formulier), button (tekst op de zoekknop), reset (tekst van de link die alle filters wist).',
						'example'     => 'settings="title=Zoek een vacature|button=Zoeken|reset=Alle vacatures tonen"',
					),
				),
				'examples'    => array(
					'[eazymatch view="searchjobs" settings="title=Zoek een vacature|button=Zoeken|reset=Alle vacatures tonen"]',
				),
			),
			'jobpage'    => array(
				'description' => 'Volledige vacaturezoekresultaten met paginering en filters. Plaats deze op de pagina die onder Vacatures als "Job zoek page" is ingesteld.',
				'params'      => array(
					self::ATTR_JOB_TEASER_TEXT => $teaserParam,
				),
				'examples'    => array(
					'[eazymatch view="jobpage"]',
					'[eazymatch view="jobpage" ' . self::ATTR_JOB_TEASER_TEXT . '="12"]',
				),
			),
			'job'        => array(
				'description' => 'Eén volledige vacature. Plaats deze op de pagina die onder Vacatures als "Vacature weergave pagina" is ingesteld; het vacature-id komt uit de url.',
				'params'      => array(),
				'examples'    => array(
					'[eazymatch view="job"]',
				),
			),
			'apply'      => array(
				'description' => 'Sollicitatieformulier. Plaats deze op de pagina die onder Vacatures als "Sollicitatie pagina" is ingesteld; het vacature-id komt uit de url.',
				'params'      => array(),
				'examples'    => array(
					'[eazymatch view="apply"]',
				),
			),
			'cv'         => array(
				'description' => 'Compacte lijst met gepubliceerde cv\'s uit de CV-database.',
				'params'      => array(),
				'examples'    => array(
					'[eazymatch view="cv"]',
				),
			),
			'react'      => array(
				'description' => 'Reactieformulier waarmee een bedrijf op een kandidaat uit de CV-database reageert.',
				'params'      => array(
					'applicant_id' => array(
						'type'        => 'geheel getal',
						'required'    => false,
						'default'     => '(kandidaat-id uit de url)',
						'description' => 'Id van de kandidaat waarop gereageerd wordt, wanneer dit niet uit de url komt.',
						'example'     => 'applicant_id="4711"',
					),
				),
				'examples'    => array(
					'[eazymatch view="react"]',
				),
			),
		);
	}

	/**
	 * Names of all views that may be used in [eazymatch view="..."]
	 *
	 * @return string[]
	 */
	static public function getViews() {
		return array_keys( self::getAll() );
	}

	/**
	 * @param string $view
	 *
	 * @return bool
	 */
	static public function isView( $view ) {
		return array_key_exists( (string) $view, self::getAll() );
	}
}
