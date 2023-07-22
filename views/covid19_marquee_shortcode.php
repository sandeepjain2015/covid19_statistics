<?php
if (is_array($covid_data) && count($covid_data) > 0) {
?>
            <marquee class='covid_19_marquee' behavior="scroll" direction="<?php esc_attr($direction); ?>" onmouseover="this.stop();" onmouseout="this.start();">
                <?php
                $count           = 1;
                $active_cases    = 0;
                $critical_cases  = 0;
                $recovered_cases = 0;
                $total_cases     = 0;

                foreach ($covid_data as $covid_stats) {
                    $country_name    = property_exists($covid_stats, 'country') ? $covid_stats->country : '';
                    $active_cases    = property_exists($covid_stats, 'active') ? $covid_stats->active : '';
                    $critical_cases  = property_exists($covid_stats, 'critical') ? $covid_stats->critical : '';
                    $recovered_cases = property_exists($covid_stats, 'recovered') ? $covid_stats->recovered : '';
                    $total_cases     = property_exists($covid_stats, 'cases') ? $covid_stats->cases : '';
                    $total_deaths    = property_exists($covid_stats, 'deaths') ? $covid_stats->deaths : '';
                ?>
                    <div class="single-item">
					<h2 class="h2-sm"><?php esc_html_e( 'Country', 'covid_statics' ); ?></h2><?php esc_html_e( $country_name ); ?>
					<h2 class="h2-sm"><?php esc_html_e( 'Active', 'covid_statics' ); ?></h2><?php esc_html_e( $active_cases ); ?>
					<h2 class="h2-sm"><?php esc_html_e( 'Critical', 'covid_statics' );?></h2><?php esc_html_e( $critical_cases ); ?>
					<h2 class="h2-sm"><?php esc_html_e( 'Recovered', 'covid_statics' ); ?></h2><?php esc_html_e( $recovered_cases ); ?>
					<h2 class="h2-sm"><?php esc_html_e( 'Deaths', 'covid_statics' ); ?></h2><?php esc_html_e( $total_deaths ); ?>
					<h2 class="h2-sm"><?php esc_html_e( 'Total', 'covid_statics' ); ?></h2><?php esc_html_e( $total_cases ); ?>
                    </div>
                <?php
                    if (intval($number_entry) === $count) {
                        break;
                    }
                    $count++;
                }
                ?>
            </marquee>
<?php
        } else {
?>
            <div><?php esc_html_e( 'Something wrong With API', 'covid_statics' ); ?></div> 
<?php
        }