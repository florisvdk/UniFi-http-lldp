<?php

// get client ip

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	
	$clientip = $_SERVER['HTTP_CLIENT_IP'];
	
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	
	$clientip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
} else {
	
	$clientip = $_SERVER['REMOTE_ADDR'];
	
}

// load required files

require_once ('../includes/php/settings.php');
require_once ('../includes/php/unifi-api-client/client.php');

// get data from unifi controller

$unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   = $unifi_connection->set_debug($debug);
$loginresults     = $unifi_connection->login();

// get client and device info

$clients_array = $unifi_connection->list_clients();
$devices_array = $unifi_connection->list_devices();

// loop trugh clients

foreach ($clients_array as $client) {
	
	// match ip to client ip
	
	if ($client->ip == $clientip ) {
		
		// list basic info
		
		echo '<div class="row"><div class="col">';
		echo '<div class="card"><div class="card-header">Client data</div><div class="card-body">';
		
		echo '<b>Client name:</b> ' . $client->name . '<br>';
		echo '<b>Client hostname:</b> ' . $client->hostname . '<br>';
		echo '<b>Client mac:</b> ' . $client->mac . '<br>';
		echo '<b>Client ip:</b> ' . $client->ip . '<br>';
		echo '<b>Client vlan:</b> ' . $client->vlan . '<br>';
		
		echo '</div></div><br></div><div class="col">';
		
		echo '<div class="card"><div class="card-header">Connection data</div><div class="card-body">';
		
		// check if wired or wireless
		
		if ($client->is_wired == true) {
			
			// loop trugh devices
			
			foreach ($devices_array as $device) {
				
				if ($device->mac == $client->sw_mac ) {
					
					echo '<b>Connection type: </b> <i class="fas fa-plug"></i><br>';
					echo '<b>Switch name:</b> ' . $device->name . '<br>';
					echo '<b>Switch port:</b> ' . $client->sw_port . '<br>';
					
					echo '<pre>';
					var_dump($client);
					echo '</pre>';
					
				}
				
			}
			
		} else {
			
			// loop trugh devices
			
			foreach ($devices_array as $device) {
				
				// match connected mac to device mac
				
				if ($device->mac == $client->ap_mac ) {
					
					// list wireless info
					
					echo '<b>Connection type: </b><i class="fas fa-wifi"></i><br>';
					echo '<b>AP name:</b> ' . $device->name . '<br>';
					echo '<b>SSID:</b> ' . $client->essid . '<br>';
					echo '<b>Channel:</b> ' . $client->channel . ' (' . $client->radio_proto . ')<br>';
					
				}
				
			}
			
		}
		
		echo '</div></div></div></div>';
		
	}
}

?>