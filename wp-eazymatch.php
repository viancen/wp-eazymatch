<?php
/*
Plugin Name: EazyMatch
Plugin URI: https://github.com/viancen/wp-eazymatch
Description: De EazyMatch Wordpress plugin. Bij twijfel over de instellingen mail naar support@eazymatch.nl
Name: EazyMatch
Version: 6.1.1
Author: EazyMatch
Author URI: https://eazymatch-online.nl
*/

#php
$globalVersion = '6.1.1';
define('EMOL_VERSION', $globalVersion);

//eazymatch directory on server
if (!defined('EMOL_DIR')) {
	define('EMOL_DIR', dirname(__FILE__));
}

// determines if jobs or cvs are displayed last
global $emol_side;

// define if plugin is in development mode, is always set manually
global $emol_isDebug;

// defnes the url of the core
global $emol_Core;

// global reference to the Eazy Api
global $emol_api;

//configuration values
$emol_db_version = $globalVersion;

//debugging
$emol_isDebug = defined('WP_DEBUG') ? WP_DEBUG : false;

/*check if session started*/
function emol_boot_session()
{
	$sessid = session_id();
	if (empty($sessid)) {
		session_start();
	}
}

//addwp _cldoadded
add_action('wp_loaded', 'emol_boot_session');

//Core location
$emol_Core = 'https://api.eazymatch.cloud';

//check permalink structure
global $trailingData;

$trailingData = '';
$permalink_structure = get_option('permalink_structure');
if (substr($permalink_structure, -1, 1) == '/') {
	$trailingData = '/';
	//maybe later we will add things as .html here...
}

//include language file
$lang = get_option('emol_lang');
if ((string)$lang == '') {
	$lang = get_bloginfo('language');
}
if (file_exists(EMOL_DIR . '/locale/' . $lang . '.php')) {
	include(EMOL_DIR . '/locale/' . $lang . '.php');
} else {
	//default get english
	include(EMOL_DIR . '/locale/en-US.php');
}

// autoloader for include optimalisation
function emol_autoloader($class_name)
{

	// transform classname to filename
	$file_name = str_replace(array(
		'emol_page_',
		'emol_widget_',
		'emol_shortcode_',
		'emol_forminstance_'
	), array(
		'page_',
		'widget_',
		'shortcode_',
		'forminstance_'
	), $class_name);


	$file_name = str_replace('_', '/', $file_name) . '.php';

	// detect non page/widgets
	if (substr($file_name, 0, 5) != 'page/' && substr($file_name, 0, 7) != 'widget/' && substr($file_name, 0, 10) != 'shortcode/' && substr($file_name, 0, 13) != 'forminstance/') {
		$file_name = 'lib/' . $file_name;
	}

	// try to include the file, preventing including errors, manual checking is below
	if (file_exists(EMOL_DIR . '/' . $file_name)) {
		include(EMOL_DIR . '/' . $file_name);
	}

	// check if class is actualy available
	if (class_exists($class_name, false) === true) {
		return true;
	}

	return false;
}

//em autoloader
spl_autoload_register('emol_autoloader');

// always require the basic items
emol_require::basic();

// eazymatch post object handling
$emol_post_obj = new emol_array($_POST);

// eazymatch generic functions
include(EMOL_DIR . '/lib/functions.php');

// adjust page titles etc
include(EMOL_DIR . '/lib/seo.php');

// rewrite urls for jobs etc
include(EMOL_DIR . '/rewrite.php');
include(EMOL_DIR . '/cron.php');

//add shortcodes hooks
add_shortcode('eazymatch', 'emol_shortcodehandler::apply');

// register widgets
// widgets use old classnames :( so aliasses must be used
// legacy widget naming support
class_alias('emol_widget_search', 'EazyMatchSearchWidget');
class_alias('emol_widget_login', 'EazyMatchLoginWidget');
class_alias('emol_widget_linkedin', 'EazyMatchLinkedinWidget');

class_alias('emol_widget_job_top5', 'EazyMatchTop5JobsWidget');
class_alias('emol_widget_job_tags', 'EazyMatchTagWidget');
class_alias('emol_widget_job_typelist', 'EazyMatchTypeJobsWidget');

class_alias('emol_widget_cv_typelist', 'EazyMatchTypeCVWidget');

// register widgets
function emol_widget_init()
{
	// legacy widget naming, dont use this anymore!
	register_widget("EazyMatchSearchWidget");
	register_widget("EazyMatchLinkedinWidget");
	register_widget("EazyMatchLoginWidget");
	register_widget("EazyMatchTop5JobsWidget");
	register_widget("EazyMatchTypeCVWidget");
	register_widget("EazyMatchTagWidget");
	register_widget("EazyMatchTypeJobsWidget");

	// new format:
	//register_widget("emol_widget_cv_search");
	//register_widget("emol_widget_job_search");
}

// prepare client resources
emol_require::jqueryUi();
if (get_option('emol_frm_google_captcha_sitekey') && get_option('emol_frm_google_captcha_secret')) {
	emol_require::recaptcha();
}

add_action('widgets_init', 'emol_widget_init');

//add custom titles for jobs
add_action('wp_title', 'emol_custom_title');
add_filter('wpseo_title', 'emol_custom_title');


//important update for canocnial tags on website that use a shortcode for the jobs
$emol_page_type = get_option('emol_job_page');
if ($emol_page_type && (string)$emol_page_type != '') {
	// remove the default WordPress canonical URL function
	if (function_exists('rel_canonical')) {
		remove_action('wp_head', 'rel_canonical');
	}
	// replace the default WordPress canonical URL function with your own
	add_action('wp_head', 'rel_canonical_with_custom_tag_override');
}


// add some extra functionality when in admin mode
if (is_admin()) {
	require plugin_dir_path(__FILE__) . 'lib/class-wp-eazymatch-autoupdate.php';
	new WP_EazyMatch_Updater(__FILE__, 'viancen', "wp-eazymatch");

	// include the admin functions
	include(EMOL_DIR . '/admin.php');
}
