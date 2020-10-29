<?php

$input        = 'E';
$output       = 'A';
$latencyLimit = 400;
$filepath     = 'sample.csv';

$file       = fopen( $filepath, 'r' );
$networkMap = [];

while ( ( $result = fgetcsv( $file ) ) !== false ) {
	$networkMap[] = $result;
}

$inverse = 0;

$startingDevices = find_devices_with_output( $output, $input );

$returnObj = [
	'path'    => [],
	'latency' => 0,
];

foreach ( $startingDevices as $value ) {
	$totalLatency  = 0;
	$device        = $networkMap[ $value ];
	$deviceInput   = $device[0];
	$deviceOutput  = $device[1];
	$deviceLatency = $device['2'];
	$returnObj     = [
		'path'    => [],
		'latency' => 0,
	];

	if ( $deviceInput === $input ) {
		array_push( $returnObj['path'], $deviceInput, $deviceOutput );
		$returnObj['latency'] = $deviceLatency;
	} else {
		$nextDevices = find_devices_with_output( $deviceInput, $input );

		$foundObj = find_paths_with_output( $nextDevices, $returnObj );

		if ( $foundObj['path'] ) {
			array_push( $returnObj['path'], $deviceOutput );
			$returnObj['latency'] += $deviceLatency;
		}
	}

	// If the latency is past the limit, then continue
	if ( $returnObj['latency'] <= $latencyLimit ) {
		break;
	} else {
		$returnObj = [
			'path'    => [],
			'latency' => 0,
		];
	}
}

if ( ! $returnObj['path'] ) {
	echo 'Path not found';
} else {
	echo implode( ' => ', $returnObj['path'] ) . ' => ' . $returnObj['latency'];
}

dd( $returnObj );

function find_devices_with_output( $output, $input, $inverse = 0 )
{
	global $networkMap;

	$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $output );

	if ( $inverse ) {
		// If we can't find paths ending with the $output, look in reverse
		$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $input );
	}

	return $devicesWithDesiredOutput;
}

function find_paths_with_output( $devices, &$returnObj )
{
	global $input, $networkMap;
	// Foreach devices, check if input is what we're looking for
	// Otherwise keep diving deeper

	foreach ( $devices as $value ) {
		$device        = $networkMap[ $value ];
		$deviceInput   = $device[0];
		$deviceOutput  = $device[1];
		$deviceLatency = $device['2'];

		echo $deviceInput . ' ' . $deviceOutput . '<br/>';

		if ( $deviceInput === $input ) {
			array_push( $returnObj['path'], $deviceInput, $deviceOutput );
			$returnObj['latency'] = $deviceLatency;
			break;
		} else {
			$nextDevices = find_devices_with_output( $deviceInput, $input );
			$foundObj    = find_paths_with_output( $nextDevices, $returnObj );

			if ( $foundObj['path'] ) {
				array_push( $returnObj['path'], $deviceOutput );
				$returnObj['latency'] += $deviceLatency;
				break;
			}
		}

	}

	return $returnObj;
}

function dd( $value )
{
	echo '<pre>';
	var_dump( $value );
	echo '</pre>';
	exit;
}