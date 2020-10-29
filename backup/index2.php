<?php

$input   = 'A';
$output  = 'F';
$latencyLimit = 1200;
$inverse = false;

// Sort letters by alphabetical order
if ( strcmp( strtolower( $input ), strtolower( $output ) ) === 0 ) {
	echo 'Letters are the same';
} elseif ( strcmp( strtolower( $input ), strtolower( $output ) ) > 0 ) {
	$inverse = true;
	$temp    = $input;
	$input   = $output;
	$output  = $temp;
}

$filepath     = 'sample.csv';

$file       = fopen( $filepath, 'r' );
$networkMap = [];

while ( ( $result = fgetcsv( $file ) ) !== false ) {
	$networkMap[] = $result;
}

$startingDevices = find_devices_with_output( $output, $input );

$returnObj = [
	'path'    => [],
	'latency' => 0,
];

foreach ( $startingDevices as $value ) {
	$device        = $networkMap[ $value ];
	$deviceInput   = $device[0];
	$deviceOutput  = $device[1];
	$deviceLatency = $device['2'];
	$returnObj     = [
		'path'    => [],
		'latency' => $deviceLatency,
	];

	if ( $deviceInput === $input ) {
		array_push( $returnObj['path'], $deviceInput, $deviceOutput );
//		$returnObj['latency'] = $deviceLatency;
	} else {
		$nextDevices = find_devices_with_output( $deviceInput, $input );
		$foundObj    = find_paths_with_output( $nextDevices, $returnObj );

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
	$path = $inverse ? array_reverse( $returnObj['path'] ) : $returnObj['path'];
	echo implode( ' => ', $path ) . ' => ' . $returnObj['latency'];
}

//dd( $returnObj );

function find_devices_with_output( $output, $input = 0 )
{
	global $networkMap;

	return array_keys( array_column( $networkMap, 1 ), $output );
}

function find_paths_with_output( $devices, &$returnObj )
{
	global $input, $networkMap, $latencyLimit;
	// Foreach devices, check if input is what we're looking for
	// Otherwise keep diving deeper

	$returnObjCopy = $returnObj;

	foreach ( $devices as $value ) {
		$device        = $networkMap[ $value ];
		$deviceInput   = $device[0];
		$deviceOutput  = $device[1];
		$deviceLatency = $device['2'];

//		echo $deviceInput . ' ' . $deviceOutput . '<br/>';

		if ( $deviceInput === $input ) {
			array_push( $returnObj['path'], $deviceInput, $deviceOutput );
			$returnObj['latency'] = $deviceLatency;
//			break;
		} else {
			$nextDevices = find_devices_with_output( $deviceInput, $input );
			$foundObj    = find_paths_with_output( $nextDevices, $returnObj );

			if ( $foundObj['path'] ) {
				array_push( $returnObj['path'], $deviceOutput );
				$returnObj['latency'] += $deviceLatency;
//				break;
			}
		}

		if ( $returnObj['latency'] <= $latencyLimit ) {
			break;
		} else {
			$returnObj = $returnObjCopy;
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