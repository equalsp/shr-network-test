<?php

function find_devices_with_output( $output, $networkMap )
{
	return array_keys( array_column( $networkMap, 1 ), $output );
}

function find_paths_with_output( $devices, &$returnObj, $input, $networkMap, $latencyLimit )
{
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
			$nextDevices = find_devices_with_output( $deviceInput, $networkMap );
			$foundObj    = find_paths_with_output( $nextDevices, $returnObj, $input, $networkMap, $latencyLimit );

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