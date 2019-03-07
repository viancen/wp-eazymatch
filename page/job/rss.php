<?php

/**
 * Container for rss feed
 */
class emol_page_job_rss extends emol_pagedummy {
	/**
	 * The slug for the fake post.  This is the URL for your plugin, like:
	 * http://site.com/about-me or http://site.com/?page_id=about-me
	 * @var string
	 */
	var $page_slug = '';

	/**
	 * The title for your fake post.
	 * @var string
	 */
	var $page_title = 'Rss';

	/**
	 * Allow pings?
	 * @var string
	 */
	var $ping_status = 'open';


	/**
	 * EazyMatch 3.0 Api
	 *
	 * @var mixed
	 */
	var $emolApi;


	/**
	 * Class constructor
	 */
	function __construct( $slug, $function = '' ) {


		$this->page_slug = $slug . '/' . $function;

		//first connect to the api
		$this->emolApi = eazymatch_connect();

		if ( ! $this->emolApi ) {
			eazymatch_trow_error();
		}

		$content    = '';
		$typeOutput = 'application/xml';
		switch ( $function ) {
			case 'trovit':
				if ( get_option( 'emol_sharing_trovit' ) == 1 ) {
					$content = $this->trovit();
				}
				break;
			case 'indeed':
				if ( get_option( 'emol_sharing_indeed' ) == 1 ) {
					$content = $this->indeed();
				}
				break;
			case 'atom':
				if ( get_option( 'emol_sharing_atom' ) == 1 ) {
					$content = $this->atom();
				}
				break;
			case 'jooble':
				if ( get_option( 'emol_sharing_jooble' ) == 1 ) {
					$content = $this->jooble();
				}
				break;
			case 'uitzendbureau':
				if ( get_option( 'emol_sharing_uitzendbureau' ) == 1 ) {
					$content = $this->uitzendbureau();
				}
				break;
			case 'simplyhired':
				if ( get_option( 'emol_sharing_simplyhired' ) == 1 ) {
					$content = $this->simplyhired();
				}
				break;
			case 'rssfull':
				if ( get_option( 'emol_sharing_rssfull' ) == 1 ) {
					$content = $this->fullrss();
				}
				break;
			case 'adzuna':
				if ( get_option( 'emol_sharing_adzuna' ) == 1 ) {
					$content = $this->adzuna();
				}
				break;
			case 'sitemap':
				if ( get_option( 'emol_sharing_sitemap' ) == 1 ) {
					$content = $this->sitemap();
				}
				break;
			case 'json':
				if ( get_option( 'emol_sharing_json' ) == 1 ) {
					$typeOutput = 'application/json';
					$content    = $this->json();
				}
				break;

			default:
				if ( get_option( 'emol_sharing_rss' ) == 1 ) {
					$content = $this->rss();
				}
				break;
		}

		if ( $content == '' ) {
			$content = '<disabled>feed disabled</disabled>';
		}

		//echo $content; exit();
		ob_get_clean();

		header( 'Content-Type: ' . $typeOutput . '; charset=utf-8' );
		//echo "<pre>";
		print trim( $content );
		exit();
	}


	/**
	 * Sitemap
	 *
	 */
	function sitemap() {

		$items = '
        <url>
            <loc>' . get_bloginfo( 'wpurl' ) . '/</loc>
            <lastmod>' . date( 'Y-m-d', strtotime( '-1 day' ) ) . '</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
        <url>
            <loc>' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_url' ) . '/</loc>
            <lastmod>' . date( 'Y-m-d', strtotime( '-1 day' ) ) . '</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.9</priority>
        </url>';

		//first try and get all published jobid's
		$search = emol_jobfilter_factory::createDefault()->getFilterArray();

		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->siteSearch( $search, 0, 500 );
		} catch ( Exception $e ) {
			$feed = '';
		}

		//basic job url for this website

		$items = '';
		foreach ( $jobs as $job ) {

			$jobUrl = emol_get_job_url( $job );
			//$jobUrl = ''.$baseJobUrl.'/'.$job['id'].'/'.eazymatch_friendly_seo_string($job['name']).'/';

			if ( $job['description'] == '' ) {
				$job['description'] = ' - ';
			}

			$items .= '<url>' . PHP_EOL;
			$items .= '  <loc>' . ( $jobUrl ) . '</loc>' . PHP_EOL;
			$items .= '  <lastmod>' . date( 'Y-m-d', strtotime( $job['datemodified'] ) ) . '</lastmod>' . PHP_EOL;
			$items .= '  <changefreq>daily</changefreq>' . PHP_EOL;
			$items .= '  <priority>0.7</priority>' . PHP_EOL;
			$items .= '</url>' . PHP_EOL;

		}

		$result = '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        ' . $items . '
        </urlset>';

		return $result;


	}


	/**
	 * Normal RSS feed
	 * ATOM
	 */
	function atom() {


		$search = emol_jobfilter_factory::createDefault()->getFilterArray();

		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->siteSearch( $search, 0, 500 );
		} catch ( SoapFault $e ) {
			$feed = '';
		}

		//basic job url for this website
		$items = '';
		foreach ( $jobs as $job ) {

			$jobUrl = emol_get_job_url( $job );

			if ( $job['description'] == '' ) {
				$job['description'] = ' - ';
			}

			$items .= '<entry>' . PHP_EOL;
			$items .= '  <id>' . ( $jobUrl ) . '</id>' . PHP_EOL;
			$items .= '  <title>' . htmlspecialchars( $job['name'] ) . '</title>' . PHP_EOL;
			$items .= '  <updated>' . date( 'c', strtotime( $job['datemodified'] . ' - 2 hour' ) ) . '</updated>' . PHP_EOL;
			$items .= '  <link href="' . $jobUrl . '" />' . PHP_EOL;
			$items .= '  <summary><![CDATA[' . htmlspecialchars( $job['description'] ) . ']]></summary>' . PHP_EOL;
			$items .= '</entry>' . PHP_EOL;

		}

		$result = '<?xml version="1.0" encoding="utf-8"?>';
		$result .= '<feed xmlns="http://www.w3.org/2005/Atom">
        <title>' . strtoupper( get_option( 'emol_instance' ) ) . '</title>
        <subtitle>Jobs ' . strtoupper( get_option( 'emol_instance' ) ) . '</subtitle>
        <link href="' . get_bloginfo( 'wpurl' ) . '/em-jobfeed/atom/" rel="self" />
        <link href="' . get_bloginfo( 'wpurl' ) . '" />
        <id>' . ( get_bloginfo( 'wpurl' ) ) . '/</id>
        <updated>' . date( 'c' ) . '</updated>
        <author>
        <name>' . strtoupper( get_option( 'emol_instance' ) ) . '</name>
        </author>
        ';
		$result .= $items;
		$result .= '</feed>';

		return $result;
	}

	//gets the data from a URL
	function get_tiny_url( $url ) {
		if ( get_option( 'emol_sharing_tiny' ) == 1 ) {
			$ch      = curl_init();
			$timeout = 5;
			curl_setopt( $ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
			$data = curl_exec( $ch );
			curl_close( $ch );

			return $data;
		} else {
			return $url;
		}
	}


	/**
	 * Normal RSS feed
	 */
	function rss() {
		$genDate = date( "r" );

		//first try and get all published jobid's
		$search = emol_jobfilter_factory::createDefault()->getFilterArray();

		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->siteSearch( $search, 0, 500 );
		} catch ( SoapFault $e ) {
			$feed = '';
		}

		//basic job url for this website

		$items = '';
		foreach ( $jobs as $job ) {

			$jobUrl = emol_get_job_url( $job );
			if ( $fullJob['startdate'] == null ) {
				$fullJob['startdate'] = date( 'Ymd' );
			}
			$tiny = $this->get_tiny_url( $jobUrl );

			$items .= '<item>' . PHP_EOL;
			$items .= '  <guid>' . $jobUrl . '</guid>' . PHP_EOL;
			$items .= '  <title>' . htmlspecialchars( $job['name'] ) . '</title>' . PHP_EOL;
			$items .= '  <pubDate>' . date( 'r', strtotime( $job['datemodified'] . ' - 2 hour' ) ) . '</pubDate>' . PHP_EOL;
			$items .= '  <link>' . $jobUrl . '</link>' . PHP_EOL;
			$items .= '  <description><![CDATA[' . htmlspecialchars( $job['description'] ) . '. <a href="' . $tiny . '">' . htmlspecialchars( $job['name'] ) . '</a>]]></description>' . PHP_EOL;
			$items .= '</item>' . PHP_EOL;

		}

		$result = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$result .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;
		$result .= '  <channel>' . PHP_EOL;
		$result .= '    <title>' . strtoupper( get_option( 'emol_instance' ) ) . '</title>' . PHP_EOL;
		$result .= '    <link>' . get_bloginfo( 'wpurl' ) . '</link>' . PHP_EOL;
		$result .= '    <atom:link href="' . get_bloginfo( 'wpurl' ) . '/em-jobfeed/rss/" rel="self" type="application/rss+xml" />' . PHP_EOL;
		$result .= '    <description>' . 'Jobfeed for ' . get_option( 'emol_instance' ) . '</description>' . PHP_EOL;
		$result .= '    <language>' . get_bloginfo( 'language' ) . '</language>' . PHP_EOL;
		$result .= '    <pubDate>' . $genDate . '</pubDate>' . PHP_EOL;
		$result .= $items;
		$result .= '  </channel>' . PHP_EOL;
		$result .= '</rss>' . PHP_EOL;

		return $result;
	}


	/**
	 * Normal RSS feed
	 * RSS2
	 */
	function fullrss() {

		//increase timelimit, this could take a while on big dbs
		set_time_limit( 500 );

		//first try and get all published jobid's
		$search = emol_jobfilter_factory::createDefault()->getFilterArray();

		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->siteSearch( $search, 0, 500 );
		} catch ( Exception $e ) {
			$feed = '';
		}

		//create trunk
		$trunk   = new EazyTrunk();
		$i       = 0;
		$results = array();
		foreach ( $jobs as $job ) {
			$i ++;
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getBasicPublished', array( $job['id'] ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job['id'] ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job['id'] ) );
		}
		// execute the trunk request
		$trunk->execute();

		$items = '';
		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];

			$jobUrl = emol_get_job_url( $fullJob );
			if ( $fullJob['startdate'] == null ) {
				$fullJob['startdate'] = date( 'Ymd' );
			}

			$region = '';
			if ( isset( $fullJob['Address']['Region']['name'] ) && $fullJob['Address']['Region']['name'] != '' ) {
				$region = $fullJob['Address']['Region']['name'];
			}

			$items .= '<item>' . PHP_EOL;
			$items .= '  <guid>' . $jobUrl . '</guid>' . PHP_EOL;
			$items .= '  <title>' . htmlspecialchars( $fullJob['name'] ) . '</title>' . PHP_EOL;
			$items .= '  <pubDate>' . date( 'r', strtotime( $fullJob['datemodified'] . ' - 2 hour' ) ) . '</pubDate>' . PHP_EOL;
			$items .= '  <link>' . $jobUrl . '</link>' . PHP_EOL;
			$items .= '  <region>' . $region . '</region>' . PHP_EOL;
			$items .= '  <description><![CDATA[' . htmlspecialchars( $fullJob['description'] ) . ']]></description>' . PHP_EOL;

			//now for our custom fields
			//teksten
			if ( is_array( $texts ) ) {
				$items .= '  <texts>' . PHP_EOL;
				$text  = '';

				foreach ( $texts as $val ) {
					$items .= '    <' . eazymatch_friendly_seo_string( $val['title'] ) . '><![CDATA[' . htmlspecialchars( $val['value'] ) . ']]></' . eazymatch_friendly_seo_string( $val['title'] ) . '>' . PHP_EOL;
				}
				$items .= '  </texts>' . PHP_EOL;
			}
			//competencies
			if ( is_array( $competencies ) ) {
				if ( $competencies[0] !== false ) {
					$comp = '';
					foreach ( $competencies as $unit ) {
						$comp .= '    <competence id="' . $unit['id'] . '" level="' . $unit['level'] . '" lft="' . $unit['lft'] . '" rgt="' . $unit['rgt'] . '"><![CDATA[' . htmlspecialchars( trim( $unit['name'] ) ) . ']]></competence>' . PHP_EOL;

					}
				}
			}
			$items .= '  <competences>' . PHP_EOL . $comp . '</competences>' . PHP_EOL;
			$items .= '</item>' . PHP_EOL;

		}

		$result = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$result .= '<rss version="2.0">' . PHP_EOL;
		$result .= '  <channel>' . PHP_EOL;
		$result .= '    <title>' . strtoupper( get_option( 'emol_instance' ) ) . '</title>' . PHP_EOL;
		$result .= '    <link>' . get_bloginfo( 'wpurl' ) . '</link>' . PHP_EOL;
		$result .= '    <description>' . 'Jobfeed for ' . get_option( 'emol_instance' ) . '</description>' . PHP_EOL;
		$result .= '    <language>' . get_bloginfo( 'language' ) . '</language>' . PHP_EOL;
		$result .= '    <pubDate>' . date( "r" ) . '</pubDate>' . PHP_EOL;
		$result .= $items;
		$result .= '  </channel>' . PHP_EOL;
		$result .= '</rss>' . PHP_EOL;

		return $result;
	}


	/**
	 * Trovit validated RSS feed
	 *
	 */
	function trovit() {

		//first try and get all published jobid's
		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->getPublishedId();
		} catch ( SoapFault $e ) {
			$feed = '';
		}

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();


		$items = '';
		foreach ( $results as $jResult ) {


			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );
			if ( $fullJob['startdate'] == null ) {
				$fullJob['startdate'] = date( 'Ymd' );
			}

			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<city><![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]></city>';
			}

			$region = '';
			if ( isset( $fullJob['Address']['Region']['name'] ) && $fullJob['Address']['Region']['name'] != '' ) {
				$region = $fullJob['Address']['Region']['name'];
			}

			$zipcode = '';
			if ( isset( $fullJob['Address']['zipcode'] ) && $fullJob['Address']['zipcode'] != '' ) {
				$zipcode = '<postcode><![CDATA[' . utf8_encode( $fullJob['Address']['zipcode'] ) . ']]></postcode>';
			}

			$jobtype = '';
			if ( isset( $fullJob['Statusses'] ) && count( $fullJob['Statusses'] ) > 0 ) {
				$jobtype = '<category><![CDATA[' . $fullJob['Statusses'][0]['Jobstatus']['name'] . ']]></category>';
			}

			//geen salaris yet

			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;
			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}

			if ( $fullJob['description'] == '' ) {
				$fullJob['description'] = $fullJob['name'];
			}

			if ( strlen( $output_text[0] ) > 29 ) {

				$items .= '
                <ad>
                <id><![CDATA[' . $fullJob['id'] . ']]></id>
                <url><![CDATA[' . $jobUrl . ']]></url>
                <title><![CDATA[' . utf8_encode( $fullJob['name'] ) . ']]></title>
                <content><![CDATA[' . utf8_encode( $output_text[0] ) . ']]></content>
                ' . $city . '
                ' . $zipcode . '
                <salary><![CDATA[' . utf8_encode( $output_text[5] ) . ']]></salary>
                <working_hours><![CDATA[' . utf8_encode( $fullJob['hours'] ) . ']]></working_hours>
                <experience><![CDATA[' . utf8_encode( $output_text[1] ) . ']]></experience>
                <requirements><![CDATA[' . utf8_encode( $output_text[2] ) . ']]></requirements>
                <contract><![CDATA[' . utf8_encode( $output_text[3] ) . ']]></contract>
                ' . $jobtype . '
                <date><![CDATA[' . utf8_encode( date( 'd/m/Y', strtotime( $fullJob['startdate'] ) ) ) . ']]></date>
                <time><![CDATA[' . utf8_encode( date( 'H:i:s', strtotime( $fullJob['startdate'] ) ) ) . ']]></time>
                <studies><![CDATA[' . utf8_encode( $output_text[4] ) . ']]></studies>
                </ad>';

			}

		}

		$result = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$result .= '<trovit>' . PHP_EOL;
		$result .= $items;
		$result .= '</trovit>' . PHP_EOL;

		return $result;
	}


	/**
	 * simplyhired validated RSS feed
	 *
	 */
	function simplyhired() {

		//first try and get all published jobid's
		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->getPublishedId();
		} catch ( SoapFault $e ) {
			$feed = '';
		}

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();


		$items = '';
		foreach ( $results as $jResult ) {


			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );
			if ( $fullJob['startdate'] == null ) {
				$fullJob['startdate'] = date( 'Ymd' );
			}

			$apply_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $fullJob['id'] . '/' . eazymatch_friendly_seo_string( $fullJob['name'] );


			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<state><![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]></state>';
			} else {
				//is mandatory for simplyhired
				continue;
			}

			$region = '';
			if ( isset( $fullJob['Address']['Region']['name'] ) && $fullJob['Address']['Region']['name'] != '' ) {
				$region = $fullJob['Address']['Region']['name'];
			}

			$zipcode = '';
			if ( isset( $fullJob['Address']['zipcode'] ) && $fullJob['Address']['zipcode'] != '' ) {
				$zipcode = '<zip><![CDATA[' . utf8_encode( $fullJob['Address']['zipcode'] ) . ']]></zip>';
			}

			$jobtype = '';
			if ( isset( $fullJob['Statusses'] ) && count( $fullJob['Statusses'] ) > 0 ) {
				$jobtype = '<category><![CDATA[' . $fullJob['Statusses'][0]['Jobstatus']['name'] . ']]></category>';
			}

			//geen salaris yet

			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;
			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}

			if ( $fullJob['description'] == '' ) {
				$fullJob['description'] = $fullJob['name'];
			}

			if ( strlen( $output_text[0] ) > 29 ) {

				$items .= '
                <job>
                <title><![CDATA[' . utf8_encode( $fullJob['name'] ) . ']]></title>
                <job-board-name><![CDATA[' . utf8_encode( get_bloginfo( 'name' ) ) . ']]></job-board-name>
                <job-board-url><![CDATA[' . utf8_encode( get_bloginfo( 'wpurl' ) ) . ']]></job-board-url>
                <job-code>' . $fullJob['id'] . '</job-code>
                <detail-url><![CDATA[' . $jobUrl . ']]></detail-url>
                <apply-url><![CDATA[' . $apply_url . ']]></apply-url>
                <description>
                    <summary><![CDATA[' . utf8_encode( $output_text[0] ) . ']]></summary>
                    <required-skills><![CDATA[' . utf8_encode( $output_text[2] ) . ']]></required-skills>
                    <required-education><![CDATA[' . utf8_encode( $output_text[4] ) . ']]></required-education>
                </description>
                <posted-date><![CDATA[' . utf8_encode( date( 'd/m/Y', strtotime( $fullJob['created'] ) ) ) . ']]></posted-date>
                <location>
                    ' . $city . '
                    ' . $zipcode . '
                    <country>NL</country>
                </location>
                <company>
                    <name><![CDATA[' . utf8_encode( get_bloginfo( 'name' ) ) . ']]></name>
                </company>
                </job>';

			}

		}

		$result = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$result .= '<jobs>' . PHP_EOL;
		$result .= $items;
		$result .= '</jobs>' . PHP_EOL;

		return $result;
	}

	/**
	 * Indeed format
	 *
	 */
	function indeed() {

		//first try and get all published jobid's
		try {
			$wsJob = $this->emolApi->get( 'job' );
			$jobs  = $wsJob->getPublishedId();
		} catch ( SoapFault $e ) {
			$feed = '';
		}

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();


		$items = '';
		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );

			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<city><![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]></city>';
			}

			$region = '';
			if ( isset( $fullJob['Address']['Region']['name'] ) && $fullJob['Address']['Region']['name'] != '' ) {
				$region = $fullJob['Address']['Region']['name'];
			}

			$zipcode = '';
			if ( isset( $fullJob['Address']['zipcode'] ) && $fullJob['Address']['zipcode'] != '' ) {
				$zipcode = '<postalcode><![CDATA[' . utf8_encode( $fullJob['Address']['zipcode'] ) . ']]></postalcode>';
			}

			$jobtype = '';
			if ( isset( $fullJob['Statusses'] ) && count( $fullJob['Statusses'] ) > 0 ) {
				$jobtype = $fullJob['Statusses'][0]['Jobstatus']['name'];
			}

			$jobvalue = '';
			if ( isset( $fullJob['Valuestatusses'] ) && count( $fullJob['Valuestatusses'] ) > 0 ) {
				$jobvalue = $fullJob['Valuestatusses'][0]['Valuestatus']['name'];
			}


			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;
			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}

			$items .= '<job>
            <title><![CDATA[' . $fullJob['name'] . ']]></title>
            <date><![CDATA[' . date( 'D, d M Y H:i:s O', strtotime( $fullJob['created'] ) ) . ']]></date>
            <referencenumber><![CDATA[' . $fullJob['id'] . ']]></referencenumber>
            <url><![CDATA[' . $jobUrl . ']]></url>
            <company><![CDATA[' . ucfirst( get_option( 'emol_instance' ) ) . ']]></company>
            ' . $city . '
            <country><![CDATA[NL]]></country>
            ' . $zipcode . '
            <description><![CDATA[' . $output_text[0] . ']]></description>
            <salary><![CDATA[' . $output_text[5] . ']]></salary>
            <education><![CDATA[' . $output_text[4] . ']]></education>
            <jobtype><![CDATA[' . $jobvalue . ']]></jobtype>
            <category><![CDATA[' . $jobtype . ']]></category>
            <experience><![CDATA[' . $output_text[1] . ']]></experience>
            </job>';

		}

		$result = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$result .= '<source>' . PHP_EOL;
		$result .= '<publisher>' . strtoupper( get_option( 'emol_instance' ) ) . '</publisher>' . PHP_EOL;
		$result .= '<publisherurl>' . get_bloginfo( 'wpurl' ) . '</publisherurl>' . PHP_EOL;
		$result .= '<lastBuildDate>' . date( 'D, d M Y H:i:s O' ) . '</lastBuildDate>' . PHP_EOL;
		$result .= $items;
		$result .= '</source>' . PHP_EOL;

		return $result;
	}


	/**
	 * jooble format
	 *
	 */
	function jooble() {

		//first try and get all published jobid's
		$wsJob = $this->emolApi->get( 'job' );
		$jobs  = $wsJob->getPublishedId();

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();


		$items = '';
		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );

			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]>';
			}

			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;
			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}

			if ( is_null( $fullJob['enddate'] ) ) {
				$fullJob['enddate'] = '';
			} else {
				$fullJob['enddate'] = date( 'd.m.Y', strtotime( $fullJob['enddate'] ) );
			}

			if ( is_null( $fullJob['datemodified'] ) ) {
				$fullJob['datemodified'] = '';
			} else {
				$fullJob['datemodified'] = date( 'd.m.Y', strtotime( $fullJob['datemodified'] ) );
			}

			if ( is_null( $fullJob['created'] ) ) {
				$fullJob['created'] = '';
			} else {
				$fullJob['created'] = date( 'd.m.Y', strtotime( $fullJob['created'] ) );
			}

			$items .= '<job id="' . $fullJob['id'] . '">
    <link><![CDATA[' . $jobUrl . ']]></link>
    <name><![CDATA[' . $fullJob['name'] . ']]></name>
    <region>' . $city . '</region>
    <description><![CDATA[' . trim( implode( '. ', $output_text ), '. -.' ) . ']]></description>
    <pubdate>' . $fullJob['created'] . '</pubdate>
    <expire>' . $fullJob['enddate'] . '</expire>
    <updated>' . $fullJob['datemodified'] . '</updated>
</job>
';

		}

		$result = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$result .= '<jobs>' . PHP_EOL;
		$result .= $items;
		$result .= '</jobs>' . PHP_EOL;

		return $result;
	}

	/**
	 * uitzendbureau format
	 *
	 */
	function uitzendbureau() {

		//first try and get all published jobid's
		$wsJob = $this->emolApi->get( 'job' );
		$jobs  = $wsJob->getPublishedId();

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();


		$items = '';
		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );

			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]>';
			}

			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;
			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}

			if ( is_null( $fullJob['enddate'] ) ) {
				$fullJob['enddate'] = '';
			} else {
				$fullJob['enddate'] = date( 'd.m.Y', strtotime( $fullJob['enddate'] ) );
			}

			if ( is_null( $fullJob['datemodified'] ) ) {
				$fullJob['datemodified'] = '';
			} else {
				$fullJob['datemodified'] = date( 'Y-m-d', strtotime( $fullJob['datemodified'] ) );
			}

			if ( is_null( $fullJob['created'] ) ) {
				$fullJob['created'] = '';
			} else {
				$fullJob['created'] = date( 'Y-m-d', strtotime( $fullJob['created'] ) );
			}

			$items .= '<job>
    <jobId>' . $fullJob['id'] . '</jobId>
    <jobAddedDate>' . $fullJob['created'] . '</jobAddedDate>
    <jobTitle><![CDATA[' . $fullJob['name'] . ']]></jobTitle>
    <jobDescription><![CDATA[' . trim( implode( '. ', $output_text ), '. -.' ) . ']]></jobDescription>
    <jobLocation>
        <locationPlace>' . $city . '</locationPlace>
    </jobLocation>
    <jobUrl><![CDATA[' . $jobUrl . ']]></jobUrl>
</job>
';

		}

		$result = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
		$result .= '<jobs version="1.0">' . PHP_EOL;
		$result .= $items;
		$result .= '</jobs>' . PHP_EOL;

		return $result;
	}


	/**
	 * adzuna format
	 *

	 */

	function adzuna() {

		//first try and get all published jobid's
		$wsJob = $this->emolApi->get( 'job' );
		$jobs  = $wsJob->getPublishedId();

		//create trunk
		$trunk   = new EazyTrunk();
		$results = array();

		$i = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob'] = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']   = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			//$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;

		}

		// execute the trunk request
		$trunk->execute();


		$items = '';

		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];
			$jobUrl       = emol_get_job_url( $fullJob );
			$city         = '';

			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = '<![CDATA[' . utf8_encode( $fullJob['Address']['city'] ) . ']]>';
			}

			$output_text[0] = '-'; //omschrijving
			$output_text[1] = '-'; //werkervaring
			$output_text[2] = '-'; //gewenste kennis
			$output_text[3] = '-'; //contract / periode
			$output_text[4] = '-'; //opleiding
			$output_text[5] = '-'; //salaris

			$i = 0;

			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text[ $i ] = $val['value'];
					$i ++;
				}
			}


			if ( is_null( $fullJob['enddate'] ) ) {
				$fullJob['enddate'] = '';
			} else {
				$fullJob['enddate'] = date( 'd.m.Y', strtotime( $fullJob['enddate'] ) );
			}


			if ( is_null( $fullJob['datemodified'] ) ) {
				$fullJob['datemodified'] = '';
			} else {
				$fullJob['datemodified'] = date( 'Y-m-d', strtotime( $fullJob['datemodified'] ) );
			}

			if ( is_null( $fullJob['created'] ) ) {
				$fullJob['created'] = '';
			} else {
				$fullJob['created'] = date( 'Y-m-d', strtotime( $fullJob['created'] ) );
			}

			$items .= '<job>
	           <title><![CDATA[' . $fullJob['name'] . ']]></title>
	            <id>' . $fullJob['id'] . '</id>
	            <description><![CDATA[' . trim( implode( '. ', $output_text ), '. -.' ) . ']]></description>
	            <url><![CDATA[' . $jobUrl . ']]></url>
           		<location>' . $city . '</location>
           		<postcode></postcode>
           		<company><![CDATA[' . get_bloginfo( 'name' ) . ']]></company>
            	<date>' . $fullJob['created'] . '</date>
            </job> ';

		}

		$result = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
		$result .= '<jobs>' . PHP_EOL;
		$result .= $items;
		$result .= '</jobs>' . PHP_EOL;

		return $result;

	}

	/**
	 * json format
	 *
	 */
	function json() {

		//first try and get all published jobid's
		$wsJob = $this->emolApi->get( 'job' );
		$jobs  = $wsJob->getPublishedId();

		//create trunk
		$trunk = new EazyTrunk();

		$results = array();
		$i       = 0;
		foreach ( $jobs as $job ) {
			$results[ $i ]['fullJob']     = &$trunk->request( 'job', 'getFullPublished', array( $job ) );
			$results[ $i ]['texts']       = &$trunk->request( 'job', 'getCustomTexts', array( $job ) );
			$results[ $i ]['competences'] = &$trunk->request( 'job', 'getCompetenceTree', array( $job ) );
			$i ++;
		}
		// execute the trunk request
		$trunk->execute();

		$items = [];
		foreach ( $results as $jResult ) {

			$fullJob      = $jResult['fullJob'];
			$texts        = $jResult['texts'];
			$competencies = $jResult['competences'];


			$jobUrl = emol_get_job_url( $fullJob );

			$city = '';
			if ( isset( $fullJob['Address']['city'] ) && $fullJob['Address']['city'] != '' ) {
				$city = utf8_encode( $fullJob['Address']['city'] );
			}

			$zipcode = '';
			if ( isset( $fullJob['Address']['zipcode'] ) && $fullJob['Address']['zipcode'] != '' ) {
				$zipcode = utf8_encode( $fullJob['Address']['zipcode'] );
			}

			$jobtype = '';
			if ( isset( $fullJob['Statusses'] ) && count( $fullJob['Statusses'] ) > 0 ) {
				$jobtype = $fullJob['Statusses'][0]['Jobstatus']['name'];
			}

			$output_text = '';

			if ( is_array( $texts ) ) {
				foreach ( $texts as $val ) {
					$output_text .= $val['value'] . PHP_EOL . PHP_EOL;
				}
			}
			if ( empty( $output_text ) ) {
				continue;
			}

			if ( is_null( $fullJob['enddate'] ) ) {
				$fullJob['enddate'] = '';
			} else {
				$fullJob['enddate'] = date( 'd.m.Y', strtotime( $fullJob['enddate'] ) );
			}

			if ( is_null( $fullJob['datemodified'] ) ) {
				$fullJob['datemodified'] = '';
			} else {
				$fullJob['datemodified'] = date( 'Y-m-d', strtotime( $fullJob['datemodified'] ) );
			}

			if ( is_null( $fullJob['created'] ) ) {
				$fullJob['created'] = '';
			} else {
				$fullJob['created'] = date( 'Y-m-d', strtotime( $fullJob['created'] ) );
			}

			$items[] = array(
				'@context'           => "http://schema.org",
				'@type'              => "JobPosting",
				'identifier'         => $fullJob['id'],
				'datePosted'         => $fullJob['startpublished'],
				'title'              => $fullJob['name'],
				'description'        => $output_text,
				'employmentType'     => $jobtype,
				'hiringOrganization' => get_bloginfo( 'name' ),
				'jobLocation'        => array(
					'@type'   => 'Place',
					'address' => array(
						'@type'           => 'PostalAddress',
						'addressLocality' => $city,
						'postalCode'      => $zipcode,
						'addressCountry'  => 'NL'
					)
				),
				'salaryCurrency'     => 'EUR',
				'validThrough'       => $fullJob['endpublished'],
				'url'                => $jobUrl,
			);
		}

		return json_encode( $items, JSON_PRETTY_PRINT );

	}

}
