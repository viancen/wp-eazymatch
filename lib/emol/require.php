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

    static public function jsSocials()
    {
        if (!function_exists('emol_load_jsSocials')) {
            function emol_load_jsSocials()
            {
                wp_register_script('jsSocials', '//cdnjs.cloudflare.com/ajax/libs/jsSocials/1.4.0/jssocials.min.js', false, '1.4.0');
                wp_enqueue_script('jsSocials');
            }
        }
        add_action('wp_enqueue_scripts', 'emol_load_jsSocials');

        if (!function_exists('emol_load_cssSocials')) {
            function emol_load_cssSocials()
            {
                wp_register_style('cssSocials1', '//cdnjs.cloudflare.com/ajax/libs/jsSocials/1.4.0/jssocials.min.css', false, '1.4.0');
                wp_register_style('cssSocials2', '//cdnjs.cloudflare.com/ajax/libs/jsSocials/1.4.0/jssocials-theme-flat.min.css', false, '1.4.0');
                wp_enqueue_style('cssSocials1');
                wp_enqueue_style('cssSocials2');
            }
        }
        add_action('wp_enqueue_scripts', 'emol_load_cssSocials');
    }

    static public function font_awesome()
    {
        if (!function_exists('emol_load_fa')) {
            function emol_load_fa()
            {
                wp_register_style('fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, '4.7.0');
                wp_enqueue_style('fontawesome');
            }
        }

        add_action('wp_enqueue_scripts', 'emol_load_fa');
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

        // add jquery from the google CDN for speed
        function load_emol_recaptcha()
        {
            wp_deregister_script('recaptcha');
            wp_register_script('recaptcha', ('//www.google.com/recaptcha/api.js'));
            wp_enqueue_script('recaptcha');
        }

        add_action('wp_enqueue_scripts', 'load_emol_recaptcha');

        self::registerInclude('recaptcha');

    }

    static public function jqueryUi()
    {
        if (is_admin() || self::hasInclude('jquery-ui')) {
            return;
        }

        $jqskin = get_option('emol_jquery_ui_skin');
        if (!empty($jqskin)) {
            // jquery is required for validation
            self::jquery();

            // add jquery-ui from the google CDN for speed
            function load_emol_js_jqueryui()
            {

                $jqskin = get_option('emol_jquery_ui_skin') ? get_option('emol_jquery_ui_skin') : 'base';

                wp_deregister_script('jquery-ui');
                wp_register_script('jquery-ui', (plugins_url('wp-eazymatch') . '/assets/jquery-ui/jquery-ui.min.js'), array('jquery'));
                wp_enqueue_script('jquery-ui');

                wp_deregister_style('jquery-ui');
                wp_register_style('jquery-ui', (plugins_url('wp-eazymatch') . '/assets/jquery-ui/themes/' . $jqskin . '/jquery-ui.min.css'), false);
                wp_enqueue_style('jquery-ui');

            }

            add_action('wp_enqueue_scripts', 'load_emol_js_jqueryui');

            self::registerInclude('jquery-ui');
        }
    }

    static public function basicCss()
    {
        if (self::hasInclude('emol-css')) {
            return;
        }

        // if style.css exists the user has defined his own stylesheets
        function load_emol_css_basic()
        {
            wp_deregister_style('emol-css');
            wp_register_style('emol-css', (plugins_url('wp-eazymatch') . '/assets/css/style.default.css'), false);
            wp_enqueue_style('emol-css');
        }

        add_action('wp_enqueue_scripts', 'load_emol_css_basic');

        self::registerInclude('emol-css');

        // if style.css exists the user has defined his own stylesheets
        $uploadinfo = wp_upload_dir();

        if (file_exists($uploadinfo['basedir'] . '/eazymatch.style.css')) {
            function load_emol_css_user()
            {
                $uploadinfo = wp_upload_dir();
                wp_deregister_style('emol-css-user');
                wp_register_style('emol-css-user', ($uploadinfo['baseurl'] . '/eazymatch.style.css'), false);
                wp_enqueue_style('emol-css-user');
            }

            add_action('wp_enqueue_scripts', 'load_emol_css_user');
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
            wp_register_script('emol-js', (plugins_url('wp-eazymatch') . '/assets/scripts/emol.js'), 'jquery');
            wp_enqueue_script('emol-js');
        }

        add_action('wp_enqueue_scripts', 'load_emol_js_basic');

        self::registerInclude('emol-js');
    }


    static public function basic()
    {
        self::basicCss();
        self::basicJavascript();

        if (get_option('emol_sharing_links')) {
            self::jsSocials();
            self::font_awesome();
        }
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
    }

}
