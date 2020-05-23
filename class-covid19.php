<?php 
/**
 * Plugin Name: Covid 19 Statics. 
 * Description: Statics for Covid 19.
 * Author: Sandeep jain
 * Author URI:http://sandeepjain.me/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 * Plugin URI:http://sandeepjain.me/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version:1.0
 * License: GPL2
 * 
 * @package   covid 19
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Covid19 {
	/**
	 * Plugin path.
	 *
	 * @var $pluginpath
	 */
	protected $pluginpath;
	/**
	 * Cache time.
	 *
	 * @var $cachetime
	 */
	protected $cachetime;
	/**
	 * Covid 19 shortcode ragister.
	 */
	public function __construct() {
		$this->pluginpath = plugin_dir_url( __FILE__ );
		$this->cachetime  = 20 * MINUTE_IN_SECONDS;
		add_shortcode( 'covid19', array( $this, 'covid19_shortcode' ) );
		add_shortcode( 'covid19_marquee', array( $this, 'covid19_marquee_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'covid_scripts' ) ); 
	}
	/**
	 * Get covid19 data.
	 */
	protected function get_covid19_data() {
		$cache_name  = 'covid_key';
		$cache_group = 'covid_group';
		// get cache here.
		$covid_data = wp_cache_get( $cache_name, $cache_group );
		if ( empty( $covid_data ) ) {
			$content = wp_remote_get( 'https://9kzzzfwgnwgef8dc.disease.sh/v2/countries/', array('timeout' => 120) );
			if ( ! is_wp_error( $content ) ) {
				$contentbodydata = wp_remote_retrieve_body( $content );
				$covid_data_all  = $this->valid_json( $contentbodydata );
				if ( is_array( $covid_data_all ) ) {
					$covid_data = $covid_data_all;
				}
				// set cache here.
				wp_cache_set( $cache_name, $covid_data, $cache_group, $this->cachetime );
			}
		}
		return $covid_data;
	}
	/**
	 * Covid 19 shortcode.
	 *
	 * @param atts $atts string.
	 */
	public function covid19_shortcode( $atts ) {
		$atts         = shortcode_atts(
			array(
				'number' => 10,
			),
			$atts,
			'covid19' 
		);
		$number_entry = ! empty( $atts['number'] ) ? $atts['number'] : 300;
		$covid_data   = $this->get_covid19_data();
		if ( is_array( $covid_data ) && count( $covid_data ) > 0 ) {
			ob_start();
			?>
		<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
		<thead>
		<tr>
		<th class="th-sm">Country</th>
		<th class="th-sm">Active</th>
		<th class="th-sm">Critical</th>
		<th class="th-sm">Recovered</th>
		<th class="th-sm">Deaths</th>
		<th class="th-sm">Total</th>
		</tr>
		</thead>
		<tbody>
			<?php 
			$count           = 1;
			$active_cases    = 0;
			$critical_cases  = 0;
			$recovered_cases = 0;
			$total_cases     = 0;
			foreach ( $covid_data as $covid_stats ) {
				$country_name    = property_exists( $covid_stats, 'country' ) ? $covid_stats->country : '';
				$active_cases    = property_exists( $covid_stats, 'active' ) ? $covid_stats->active : '';
				$critical_cases  = property_exists( $covid_stats, 'critical' ) ? $covid_stats->critical : '';
				$recovered_cases = property_exists( $covid_stats, 'recovered' ) ? $covid_stats->recovered : '';
				$total_cases     = property_exists( $covid_stats, 'cases' ) ? $covid_stats->cases : '';
				$total_deaths    = property_exists( $covid_stats, 'deaths' ) ? $covid_stats->deaths : '';
				?>
			<tr class="covid-style1-stats">
			<td class="covid-country-title"><?php echo esc_html( $country_name ); ?></td>
			<td class="covid-active_cases"><?php echo esc_html( $active_cases ); ?></td>
			<td class="covid-critical_cases"><?php echo esc_html( $critical_cases ); ?></td>
			<td class="covid-recovered_cases"><?php echo esc_html( $recovered_cases ); ?></td>
			<td class="covid-total_deaths"><?php echo esc_html( $total_deaths ); ?></td>
			<td class="covid-total_cases"><?php echo esc_html( $total_cases ); ?></td>
			</tr>
				<?php
				if ( intval( $number_entry ) === $count ) {
					break;
				}
				$count++;
			}
			?>
		</tbody></table>
		<?php } else { ?>
		<div>Something wrong With API</div> 
			<?php
		}
		return ob_get_clean();
	}
	/**
	 * Covid 19 marquee shortcode.
	 *
	 * @param atts $atts string.
	 */
	public function covid19_marquee_shortcode( $atts ) {
		$atts         = shortcode_atts(
			array(
				'number'    => 10,
				'direction' => 'down',
			),
			$atts,
			'covid19_marquee' 
		);
		$number_entry = ! empty( $atts['number'] ) ? $atts['number'] : 300;
		$direction    = ! empty( $atts['direction'] ) ? $atts['direction'] : 'down';
		$covid_data   = $this->get_covid19_data();
		if ( is_array( $covid_data ) && count( $covid_data ) > 0 ) {
			ob_start();
			?>
			<marquee class ='covid_19_marquee' behavior="scroll" direction="<?php esc_attr( $direction ); ?>" onmouseover="this.stop();" onmouseout="this.start();">
			<?php 
			$count           = 1;
			$active_cases    = 0;
			$critical_cases  = 0;
			$recovered_cases = 0;
			$total_cases     = 0;
			foreach ( $covid_data as $covid_stats ) {
				$country_name    = property_exists( $covid_stats, 'country' ) ? $covid_stats->country : '';
				$active_cases    = property_exists( $covid_stats, 'active' ) ? $covid_stats->active : '';
				$critical_cases  = property_exists( $covid_stats, 'critical' ) ? $covid_stats->critical : '';
				$recovered_cases = property_exists( $covid_stats, 'recovered' ) ? $covid_stats->recovered : '';
				$total_cases     = property_exists( $covid_stats, 'cases' ) ? $covid_stats->cases : '';
				$total_deaths    = property_exists( $covid_stats, 'deaths' ) ? $covid_stats->deaths : '';
				?>
			<div class ="single-item">
			<h2 class="h2-sm">Country</h2><?php echo esc_html( $country_name ); ?>
			<h2 class="h2-sm">Active</h2><?php echo esc_html( $active_cases ); ?>
			<h2 class="h2-sm">Critical</h2><?php echo esc_html( $critical_cases ); ?>
			<h2 class="h2-sm">Recovered</h2><?php echo esc_html( $recovered_cases ); ?>
			<h2 class="h2-sm">Deaths</h2><?php echo esc_html( $total_deaths ); ?>
			<h2 class="h2-sm">Total</h2><?php echo esc_html( $total_cases ); ?>
			</div>
				<?php
				if ( intval( $number_entry ) === $count ) {
					break;
				}
				$count++;
			}
			?>
		</marquee>
		<?php } else { ?>
		<div>Something wrong With API</div> 
			<?php
		}
		return ob_get_clean();
	}
	/**
	 * Check valid json.
	 *
	 * @param string $string json file.
	 */
	private function valid_json( $string ) {
		$result = '';
		$result = json_decode( $string );
		if ( json_last_error() === JSON_ERROR_NONE || json_last_error() === 0 ) {
			$result = $result;
		} else {
			$result = '';
		}
		return $result;
	}
	/**
	 * Add js and css for covid shortcode.
	 */
	public function covid_scripts() {
		wp_register_style(
			'covid_css',
			$this->pluginpath . 'css/covid.css',
			'',
			'02052020.12',
			''
		);
		wp_register_style(
			'datatables_css',
			$this->pluginpath . 'css/min/datatables.min.css',
			'',
			'02052020.13',
			''
		);
		wp_register_style(
			'bootstrap',
			$this->pluginpath . 'css/min/bootstrap.min.css',
			'',
			'02052020.12',
			''
		);
	
		wp_register_script(
			'datatables',
			$this->pluginpath . 'js/min/datatables.min.js',
			array( 'jquery' ),
			'02052020.1',
			true
		);
		wp_register_script(
			'covid_js',
			$this->pluginpath . 'js/covid.js',
			array( 'jquery' ),
			'02052020.1',
			true
		);
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_style( 'datatables_css' );
		wp_enqueue_style( 'covid_css' );
	
		wp_enqueue_script( 'datatables' );
		wp_enqueue_script( 'covid_js' );
	}
}
new covid19();
