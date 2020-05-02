<?php 
/**
 * Plugin Name: Covid 19 Statics. 
 * Description: Covid 19 Statics shortcode.
 * Author: Sandeep jain
 * Version:1.0
 * License: GPL
 * 
 * @package   covid 19
 */

define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! function_exists( 'covid19_shortcode' ) ) {
	/**
	 * Covid 19 shortcode.
	 *
	 * @param atts $atts string.
	 */
	function covid19_shortcode( $atts ) {
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
				$covid_data_all  = valid_json( $contentbodydata );
				if ( is_object( $covid_data_all ) && property_exists( $covid_data_all, 'response' ) ) {
					$covid_data = $covid_data_all->response;
				}
				// set cache here.
				wp_cache_set( $cache_name, $covid_data, $cache_group, 300 );
			}
		}
		$covid_html = '';
		if ( is_array( $covid_data ) && count( $covid_data ) > 0 ) {
			$covid_html     .= '<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
			<thead>
			<tr>
            <th class="th-sm" > Country</th>
            <th class="th-sm" >Active</th>
            <th class="th-sm">Critical</th>
			<th class="th-sm">Recovered</th>
			<th class="th-sm">deaths</th>
			<th class="th-sm">Total</th>
			</tr>
			</thead>
			<tbody>';
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
				$covid_html .= '<tr class="covid-style1-stats">';
				$covid_html .= sprintf( '<td class="covid-country-title">%s</td>', esc_html( $country_name ) );
				$covid_html .= sprintf( '<td class="covid-active_cases">%s</td>', esc_html( $active_cases ) );
				$covid_html .= sprintf( '<td class="covid-critical_cases">%s</td>', esc_html( $critical_cases ) );
				$covid_html .= sprintf( '<td class="covid-recovered_cases">%s</td>', esc_html( $recovered_cases ) );
				$covid_html .= sprintf( '<td class="covid-total_deaths">%s</td>', esc_html( $total_deaths ) );
				$covid_html .= sprintf( '<td class="covid-total_cases">%s</td>', esc_html( $total_cases ) );
				$covid_html .= '</tr>';
				if ( intval( $show_entry ) === $count ) {
					break;
				}
				$count++;
			}
			$covid_html .= '</tbody></table>';
		} else {
			$covid_html .= '<div>' . __( 'Something wrong With API' ) . '</div>'; 
		}
		return $covid_html;
	}
	add_shortcode( 'covid19', 'covid19_shortcode' );
}
if ( ! function_exists( 'valid_json' ) ) {
	/**
	 * Check valid json.
	 *
	 * @param string $string json file.
	 */
	function valid_json( $string ) {
		$result = '';
		$result = json_decode( $string );
		if ( json_last_error() === JSON_ERROR_NONE || json_last_error() === 0 ) {
			$result = $result;
		} else {
			$result = '';
		}
		return $result;
	}
}
add_action(
	'wp_enqueue_scripts',
	function() {
			wp_register_style(
				'covid_css',
				PLUGIN_URL . 'css/covid.css',
				'',
				'02052020.12',
				''
			);
			wp_register_style(
				'datatables_css',
				PLUGIN_URL . 'css/min/datatables.min.css',
				'',
				'02052020.12',
				''
			);
			wp_register_style(
				'bootstrap',
				PLUGIN_URL . 'css/min/bootstrap.min.css',
				'',
				'02052020.12',
				''
			);
			
			wp_register_script(
				'datatables',
				PLUGIN_URL . 'js/min/datatables.min.js',
				array( 'jquery' ),
				'02052020.1',
				true
			);
			wp_register_script(
				'covid_js',
				PLUGIN_URL . 'js/covid.js',
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
);
