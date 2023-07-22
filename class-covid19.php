<?php
/**
 * Plugin Name: Covid 19 Statistics.
 * Description: Statistics for Covid 19.
 * Author: Sandeep Jain
 * Author URI: http://sandeepjain.me/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 * Plugin URI: http://sandeepjain.me/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version: 1.2
 * License: GPL2
 *
 * @package covid 19
 */

if (!defined('ABSPATH')) {
    exit;
}

interface CovidDataInterface
{
    public function get_covid19_data();
    public function valid_json($string);
}

interface CovidShortcodeInterface
{
    public function covid19_shortcode($atts);
    public function covid19_marquee_shortcode($atts);
}

class Covid19 implements CovidDataInterface, CovidShortcodeInterface
{
    /**
     * Cache time.
     *
     * @var int
     */
    protected $cachetime;

    /**
     * Country API URL.
     *
     * @var string
     */
    protected $country_api_url;

    /**
     * Covid 19 shortcode register.
     */
    public function __construct()
    {
        $this->cachetime       = 20 * MINUTE_IN_SECONDS;
        $this->country_api_url = 'https://9kzzzfwgnwgef8dc.disease.sh/v2/countries/';

        // Hook into the plugins_loaded action to define constants
        add_action('plugins_loaded', array($this, 'define_constants'), 0);

        // Register shortcodes and enqueue scripts
        add_shortcode('covid19', array($this, 'covid19_shortcode'));
        add_shortcode('covid19_marquee', array($this, 'covid19_marquee_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'covid_scripts'));
    }

    /**
     * Define constants used by the plugin.
     */
    public function define_constants()
    {
        // Define the plugin version.
        define('COVID_VER', '1.2');

        // Define the plugin directory path.
        define('COVID_DIR', plugin_dir_path(__FILE__));

        // Define the plugin directory URL.
        define('COVID_URL', plugin_dir_url(__FILE__));
    }

    /**
     * Get covid19 data.
     */
    public function get_covid19_data()
    {
        $cache_name  = 'covid_key';
        $cache_group = 'covid_group';

        // Get cache here.
        $covid_data = wp_cache_get($cache_name, $cache_group);

        if (!empty($covid_data)) {
            return $covid_data;
        }

        $content = wp_remote_get(
            $this->country_api_url,
            array(
                'timeout' => 2 * MINUTE_IN_SECONDS,
            )
        );

        if (is_wp_error($content)) {
            return array();
        }

        $contentbodydata = wp_remote_retrieve_body($content);
        $covid_data_all  = $this->valid_json($contentbodydata);

        if (is_array($covid_data_all)) {
            $covid_data = $covid_data_all;
        }

        // Set cache here.
        wp_cache_set($cache_name, $covid_data, $cache_group, $this->cachetime);

        return $covid_data;
    }

    /**
     * Covid 19 shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string Shortcode markup.
     */
    public function covid19_shortcode($atts)
    {
        $atts         = shortcode_atts(
            array(
                'number' => 10,
            ),
            $atts,
            'covid19'
        );
        $number_entry = !empty($atts['number']) ? $atts['number'] : 300;
        $covid_data   = $this->get_covid19_data();

        ob_start();
        require_once COVID_DIR . '/views/covid19_shortcode.php';
        $markup = ob_get_clean();

        return $markup;
    }

    /**
     * Covid 19 marquee shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string Shortcode markup.
     */
    public function covid19_marquee_shortcode($atts)
    {
        $atts         = shortcode_atts(
            array(
                'number'    => 10,
                'direction' => 'down',
            ),
            $atts,
            'covid19_marquee'
        );
        $number_entry = !empty($atts['number']) ? $atts['number'] : 300;
        $direction    = !empty($atts['direction']) ? $atts['direction'] : 'down';
        $covid_data   = $this->get_covid19_data();

        ob_start();
        require_once COVID_DIR . '/views/covid19_marquee_shortcode.php';
        $markup = ob_get_clean();

        return $markup;
    }

    /**
     * Check valid JSON.
     *
     * @param string $string JSON file.
     * @return mixed Parsed JSON data or empty if invalid.
     */
    public function valid_json($string)
    {
        $result = json_decode($string);

        if (json_last_error() === JSON_ERROR_NONE || json_last_error() === 0) {
            return $result;
        }

        return '';
    }

    /**
     * Enqueue scripts and styles for covid shortcode.
     */
    public function covid_scripts()
    {
        wp_register_style(
            'covid_css',
            COVID_URL . 'css/covid.css',
            '',
            '02052020.12',
            ''
        );
        wp_register_style(
            'datatables_css',
            COVID_URL . 'css/min/datatables.min.css',
            '',
            '23052020.1',
            ''
        );
        wp_register_style(
            'bootstrap',
            COVID_URL . 'css/min/bootstrap.min.css',
            '',
            '02052020.12',
            ''
        );

        wp_register_script(
            'datatables',
            COVID_URL . 'js/min/datatables.min.js',
            array('jquery'),
            '02052020.1',
            true
        );
        wp_register_script(
            'covid_js',
            COVID_URL . 'js/covid.js',
            array('jquery'),
            '02052020.1',
            true
        );
        wp_enqueue_style('bootstrap');
        wp_enqueue_style('datatables_css');
        wp_enqueue_style('covid_css');
        wp_enqueue_script('datatables');
        wp_enqueue_script('covid_js');
    }
}

new Covid19();

