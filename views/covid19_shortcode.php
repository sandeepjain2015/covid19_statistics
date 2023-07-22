<?php  if (is_array($covid_data) && count($covid_data) > 0) { ?>
<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                <thead>
                    <tr>
					<th class="th-sm"><?php esc_html_e( 'Country', 'covid_statics' ); ?></th>
					<th class="th-sm"><?php esc_html_e( 'Active', 'covid_statics' ); ?></th>
					<th class="th-sm"><?php esc_html_e( 'Critical', 'covid_statics' ); ?></th>
					<th class="th-sm"><?php esc_html_e( 'Recovered', 'covid_statics' ); ?></th>
					<th class="th-sm"><?php esc_html_e( 'Deaths', 'covid_statics' ); ?> </th>
					<th class="th-sm"><?php esc_html_e( 'Total', 'covid_statics' ); ?>  </th>
                    </tr>
                </thead>
                <tbody>
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
                        <tr class="covid-style1-stats">
                            <td class="covid-country-title"><?php echo esc_html($country_name); ?></td>
                            <td class="covid-active_cases"><?php echo esc_html($active_cases); ?></td>
                            <td class="covid-critical_cases"><?php echo esc_html($critical_cases); ?></td>
                            <td class="covid-recovered_cases"><?php echo esc_html($recovered_cases); ?></td>
                            <td class="covid-total_deaths"><?php echo esc_html($total_deaths); ?></td>
                            <td class="covid-total_cases"><?php echo esc_html($total_cases); ?></td>
                        </tr>
                        <?php
                        if (intval($number_entry) === $count) {
                            break;
                        }
                        $count++;
                    }
                    ?>
                </tbody>
            </table>
<?php
        } else {
?>
           <div><?php esc_html_e( 'Something wrong With API', 'covid_statics' ); ?></div> 
<?php
        }