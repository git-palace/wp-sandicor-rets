<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'mclain-rets', 
		'update-config', 
		array(
			'methods' => 'post',
			'callback' => 'update_mclain_config',
		)
	);

	register_rest_route( 
		'mclain-rets', 
		'populate-db', 
		array(
			'methods' => 'post',
			'callback' => 'populate_database',
		)
	);
} );

function update_mclain_config() {
	if (
		isset( $_POST["login_url"] ) && !empty( $_POST["login_url"] ) &&
		isset( $_POST["username"] ) && !empty( $_POST["username"] ) &&
		isset( $_POST["password"] ) && !empty( $_POST["password"] ) &&
		isset( $_POST["brelicense"] ) && !empty( $_POST["brelicense"] ) &&
		class_exists( "MCRETS_Config" )
	) {
		return MCRETS_Config::saveConfig( array(
			"login_url" => $_POST["login_url"],
			"username"	=> $_POST["username"],
			"password"	=> $_POST["password"],
			"brelicense" => $_POST["brelicense"],
			"autosave"	=> $_POST["autosave"] 
		) );
	}

	return false;
}

function populate_database() {
	return MCR()->populateDB();
}

function MCR() {
	global $mcrets;
	$mcrets = MCRETS::getInstance();	

	return $mcrets;
}

function custom_cron_schedule( $schedules ) {
	$schedules['every_six_hours'] = array(
		'interval' => 21600, // Every 6 hours
		'display'  => __( 'Every 6 hours' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'custom_cron_schedule' );

///Hook into that action that'll fire every six hours
add_action( 'MCRETSCronJob', function() {
	wp_remote_post( home_url( '/wp-json/mclain-rets/populate-db' ) );
} );