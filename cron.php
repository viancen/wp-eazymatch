<?php
function emol_custom_cron_schedule($schedules)
{
	$schedules['every_six_hours'] = array(
		'interval' => 21600, // Every 6 hours
		'display' => __('Every 6 hours'),
	);
	return $schedules;
}

add_filter('cron_schedules', 'emol_custom_cron_schedule');

//Schedule an action if it's not already scheduled
if (!wp_next_scheduled('emol_cron_hook')) {
	wp_schedule_event(time(), 'every_six_hours', 'emol_cron_hook');
}

///Hook into that action that'll fire every six hours
add_action('emol_cron_hook', 'emol_cron_function');

//create your function, that runs on cron
function emol_cron_function()
{
	delete_option('emol_apihash');
	eazymatch_connect();
}