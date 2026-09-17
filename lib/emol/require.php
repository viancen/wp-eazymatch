<?php
if (!defined('EMOL_DIR')) {
    die('no direct access');
}

class emol_require
{
    static private $includes = array();

    static private function registerInclude($includeName)
    {
        if (!self::hasInclude($includeName)) {
            self::$includes[] = $includeName;
        }
    }

    static public function hasInclude($includeName)
    {
        return in_array($includeName, self::$includes);
    }

    static public function admin()
    {
        if (!is_admin() || self::hasInclude('admin')) {
            return;
        }

        // add jquery from the google CDN for speed
        function load_emol_js_admin()
        {
            wp_enqueue_script('jquery-ui-sortable');

            wp_deregister_script('emol-admin');
            wp_register_script('emol-admin', (plugins_url('wp-eazymatch') . '/assets/scripts/admin.js'), 'jquery');
            wp_enqueue_script('emol-admin');

            //wp_localize_script( 'emol-ajax-request', 'EmolAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }

        add_action('admin_enqueue_scripts', 'load_emol_js_admin');

        self::registerInclude('admin');
    }

    static public function jquery()
    {
        // the jquery library will conflict when wordpress is in admin mode
        if (is_admin() || self::hasInclude('jquery')) {
            return;
        }

        // add jquery from the google CDN for speed
        function load_emol_js_jquery()
        {
            wp_enqueue_script('jquery');
        }

        add_action('wp_enqueue_scripts', 'load_emol_js_jquery');

        self::registerInclude('jquery');
    }

    static public function recaptcha()
    {
        self::altcha();
    }

    static public function altcha()
    {
        if (self::hasInclude('altcha')) {
            return;
        }

        if (!function_exists('load_emol_altcha')) {
            function load_emol_altcha()
            {
                wp_register_script(
                    'emol-altcha',
                    plugins_url('wp-eazymatch') . '/assets/scripts/altcha.min.js',
                    array(),
                    '1.4.2',
                    true
                );
                wp_enqueue_script('emol-altcha');
            }
        }

        add_action('wp_enqueue_scripts', 'load_emol_altcha');

        self::registerInclude('altcha');
    }

    static public function jqueryUi()
    {
        if (is_admin() || self::hasInclude('jquery-ui')) {
            return;
        }

        self::ensureFormThemeClass();

        $pluginTheme = class_exists('emol_form_theme', false) && emol_form_theme::isPluginTheme();
        $jqskin = get_option('emol_jquery_ui_skin');

        if (!$pluginTheme && empty($jqskin)) {
            return;
        }

        // jquery is required for datepickers and dialogs
        self::jquery();

        function load_emol_js_jqueryui()
        {
            $pluginTheme = class_exists('emol_form_theme', false) && emol_form_theme::isPluginTheme();
            $jqskin = get_option('emol_jquery_ui_skin') ? get_option('emol_jquery_ui_skin') : 'base';

            wp_deregister_script('jquery-ui');
            wp_register_script('jquery-ui', (plugins_url('wp-eazymatch') . '/assets/jquery-ui/jquery-ui.min.js'), array('jquery'));
            wp_enqueue_script('jquery-ui');

            if (!$pluginTheme) {
                wp_deregister_style('jquery-ui');
                wp_register_style('jquery-ui', (plugins_url('wp-eazymatch') . '/assets/jquery-ui/themes/' . $jqskin . '/jquery-ui.min.css'), false);
                wp_enqueue_style('jquery-ui');
            }
        }

        add_action('wp_enqueue_scripts', 'load_emol_js_jqueryui');

        self::registerInclude('jquery-ui');
        self::formTheme();
    }

    static private function ensureFormThemeClass()
    {
        if (class_exists('emol_form_theme', false)) {
            return;
        }

        $file = dirname(__FILE__) . '/form/theme.php';
        if (is_readable($file)) {
            require_once $file;
        }
    }

    static public function formTheme()
    {
        if (self::hasInclude('emol-form-theme')) {
            return;
        }

        self::ensureFormThemeClass();

        if (!class_exists('emol_form_theme', false) || !emol_form_theme::isPluginTheme()) {
            return;
        }

        emol_form_theme::boot();
        self::basicCss();

        function load_emol_css_form_theme()
        {
            $id = emol_form_theme::id();
            $ver = defined('EMOL_VERSION') ? EMOL_VERSION : false;
            $base = plugins_url('wp-eazymatch') . '/assets/css/forms/';

            wp_register_style('emol-form-theme-base', $base . 'base.css', array('emol-css'), $ver);
            wp_register_style('emol-form-theme', $base . $id . '.css', array('emol-form-theme-base'), $ver);
            wp_enqueue_style('emol-form-theme');

            $inline = emol_form_theme::inlineCss();
            if ($inline !== '') {
                wp_add_inline_style('emol-form-theme', $inline);
            }
        }

        add_action('wp_enqueue_scripts', 'load_emol_css_form_theme', 15);

        self::registerInclude('emol-form-theme');
    }

    static public function basicCss()
    {
        if (self::hasInclude('emol-css')) {
            return;
        }

        // if style.css exists the user has defined his own stylesheets
        function load_emol_css_basic()
        {
            $ver = defined('EMOL_VERSION') ? EMOL_VERSION : false;

            wp_deregister_style('emol-css');
            wp_register_style('emol-css', (plugins_url('wp-eazymatch') . '/assets/css/style.default.css'), array(), $ver);
            wp_enqueue_style('emol-css');
            wp_add_inline_style('emol-css', emol_jobtext::frontCss());
        }

        add_action('wp_enqueue_scripts', 'load_emol_css_basic');

        self::registerInclude('emol-css');

        // if style.css exists the user has defined his own stylesheets
        $uploadinfo = wp_upload_dir();

        if (file_exists($uploadinfo['basedir'] . '/eazymatch.style.css')) {
            function load_emol_css_user()
            {
                $uploadinfo = wp_upload_dir();
                $deps = array('emol-css');
                if (class_exists('emol_form_theme', false) && emol_form_theme::isPluginTheme()) {
                    $deps[] = 'emol-form-theme';
                }
                wp_deregister_style('emol-css-user');
                wp_register_style('emol-css-user', ($uploadinfo['baseurl'] . '/eazymatch.style.css'), $deps);
                wp_enqueue_style('emol-css-user');
            }

            add_action('wp_enqueue_scripts', 'load_emol_css_user', 20);
        }
    }

    static public function basicJavascript()
    {
        if (self::hasInclude('emol-js')) {
            return;
        }

        // jquery is required
        self::jquery();

        function load_emol_js_basic()
        {
            wp_deregister_script('emol-js');
            wp_register_script('emol-js', (plugins_url('wp-eazymatch') . '/assets/scripts/emol.js'), array('jquery'));
            wp_enqueue_script('emol-js');
            wp_localize_script('emol-js', 'EmolForm', array(
                'required' => defined('EMOL_ERR_REQUIRED') ? EMOL_ERR_REQUIRED : 'Dit veld is niet of incorrect ingevuld',
                'email' => defined('EMOL_ERR_VALID_EMAIL') ? EMOL_ERR_VALID_EMAIL : 'Dit is een ongeldig e-mailadres',
                'wait' => defined('EMOL_FORM_WAIT') ? EMOL_FORM_WAIT : 'Een moment geduld',
            ));
        }

        add_action('wp_enqueue_scripts', 'load_emol_js_basic');

        self::registerInclude('emol-js');
    }


    static public function basic()
    {
        self::basicCss();
        self::basicJavascript();
    }

    /**
     * require all emol scripts/styles
     *
     * @param string $name
     *
     * @return bool
     */
    static public function all()
    {
        self::basic();
        self::jqueryUi();
        self::formTheme();

		return true;
    }

}
