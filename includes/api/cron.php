<?php
// Cron API
function interval_cron_mirabel ($schedules_mirabel){
    $schedules_mirabel ['once_a_day'] = array(
        'interval' => 86400,
        'display' => esc_html__('Une fois par jour'),
    );
    return $schedules_mirabel;
}
add_filter ('cron_schedules', 'interval_cron_mirabel');

function schedule_mirabel(){
    if ( !wp_next_scheduled ('mirabel_api_cron') ):
        wp_schedule_event ( time(), 'once_a_day', 'mirabel_api_cron' );
    endif;
}
add_action('init','schedule_mirabel');

//Attache le cron à l'api
add_action ('mirabel_api_cron', 'mirabel_import_api');