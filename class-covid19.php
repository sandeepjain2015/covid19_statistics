<?php 
/**
 * Plugin Name: Covid 19 Statics. 
 * Description: short code for Covid 19.
 * Author: Sandeep jain
 * Author URI:http://sandeepjain.me
 * Plugin URI:http://sandeepjain.me
 * Version:1.0
 * License: GPL
 * 
 * @package   covid 19
 */
class Covid19 {
	protected $pluginpath;
	protected $cachetime;
	/**
	 * Covid 19 shortcode ragister.
	 */
	public function __construct() {
		$this->pluginpath = plugin_dir_url( __FILE__ );
		$this->cachetime  = 20 * MINUTE_IN_SECONDS;
		add_shortcode( 'covid19', array( $this, 'covid19_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'covid_scripts' ) ); 
	}
	/**
	 * Covid 19 shortcode.
	 *
	 * @param atts $atts string.
	 */
	public function covid19_shortcode( $atts ) {
		$atts        = shortcode_atts(
			array(
				'show' => 10,
			),
			$atts,
			'covid19' 
		);
		$show_entry  = ! empty( $atts['show'] ) ? $atts['show'] : 300;
		$args        = array(
			'httpversion' => '1.1',
			'headers'     => array(
				'x-rapidapi-host' => 'covid-193.p.rapidapi.com',
				'x-rapidapi-key'  => 'b69a77a4cfmshcc4ce1699c9c707p1a1891jsnc506dda52625',
			),
		);
		$cache_name  = 'covid_key';
		$cache_group = 'covid_group';
		// get cache here.
		$covid_data = wp_cache_get( $cache_name, $cache_group );
		if ( empty( $covid_data ) ) {
			$content = wp_remote_get( 'https://covid-193.p.rapidapi.com/statistics', $args );
			if ( ! is_wp_error( $content ) ) {
				$contentbodydata = wp_remote_retrieve_body( $content );
				$covid_data_all  = $this->valid_json( $contentbodydata );
				if ( is_object( $covid_data_all ) && property_exists( $covid_data_all, 'response' ) ) {
					$covid_data = $covid_data_all->response;
				}
				// set cache here.
				wp_cache_set( $cache_name, $covid_data, $cache_group, $this->cachetime );
			}
		}
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
				$country_name = property_exists( $covid_stats, 'country' ) ? $covid_stats->country : '';
				if ( property_exists( $covid_stats, 'cases' ) ) {
					$covid_cases     = $covid_stats->cases;
					$active_cases    = property_exists( $covid_cases, 'active' ) ? $covid_cases->active : '';
					$critical_cases  = property_exists( $covid_cases, 'critical' ) ? $covid_cases->critical : '';
					$recovered_cases = property_exists( $covid_cases, 'recovered' ) ? $covid_cases->recovered : '';
					$total_cases     = property_exists( $covid_cases, 'total' ) ? $covid_cases->total : '';
				}
				if ( property_exists( $covid_stats, 'deaths' ) ) {
					$deaths       = $covid_stats->deaths;
					$total_deaths = property_exists( $deaths, 'total' ) ? $deaths->total : '';
				}
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
				if ( intval( $show_entry ) === $count ) {
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
			'02052020.12',
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
