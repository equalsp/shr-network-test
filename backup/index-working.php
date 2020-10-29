<?php

// Ask for CSV file path
// Check if CSV file exists || validate CSV
// Ask for input variables
// Validate input variables
// Parse CSV
// Ask for Input path, output path and time limit

if ( php_sapi_name() === 'cli' ) {
// Ask for CSV file path
	$filepath = readline( 'Please enter your CSV file path: ' );

// Check if CSV file exists || validate CSV
	if ( ! ( file_exists( $filepath ) || is_file( $filepath ) ) ) {
		echo "Sorry, the file path that you entered is not readable. Please ensure that the path to the file is correct and that PHP has permissions to access the file. \n";
		exit;
	}

// Ask for input variables
	$userInput       = readline( 'Please enter your desired start and end devices and latency limit: (Input Output Latency)' );
	$parsedUserInput = explode( ' ', $userInput );

// Validate input variables
	if ( ! count( $parsedUserInput ) === 3 ) {
		echo "Sorry, the input does not match the required format (Input Output Latency). Please try again. \n";
		exit;
	};

	$input        = $parsedUserInput[0];
	$output       = $parsedUserInput[1];
	$latencyLimit = $parsedUserInput[2];


// TESTING VARIABLES
//$input        = 'E';
//$output       = 'A';
//$latencyLimit = 80;
//$filepath     = 'sample.csv';
// END TESTING VARIABLES

	$file       = fopen( $filepath, 'r' );
	$networkMap = [];

	while ( ( $result = fgetcsv( $file ) ) !== false ) {
		$networkMap[] = $result;
	}

// Find network paths ending with the desired output
	function find_paths_with_output( $networkMap, $input, $output, $latencyLimit, &$returnObj = [ 'path' => [], 'latency' => 0, 'inverse' => 0 ] )
	{
		$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $output );
		if ( ! count( $devicesWithDesiredOutput ) ) {
			$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $input );
			$input                    = $output;
			$returnObj['inverse']     = 1;
		}

		foreach ( $devicesWithDesiredOutput as $value ) {
			$device = $networkMap[ $value ];

//		echo $device[0] . ' ' . $device[1];

			if ( $device[0] === $input ) {
				array_push( $returnObj['path'], $device[0], $device[1] );
				$returnObj['latency'] += $device[2];

				// If the input for this device matches the desired input, return the latency
				return $returnObj;
			}

			// If the total latency is over the latency limit, then skip this tree
			$totalLatency = $returnObj['latency'] + $device[2];
			if ( $totalLatency >= $latencyLimit ) {
				continue;
			}

			$found = find_paths_with_output( $networkMap, $input, $device[0], $latencyLimit, $returnObj );
			if ( $found['path'] ) {
				array_push( $returnObj['path'], $device[1] );
				$returnObj['latency'] += $device[2];

				return $returnObj;
			}

		}

		return $returnObj;
	}

	$output = find_paths_with_output( $networkMap, $input, $output, $latencyLimit );
	if ( ! $output['path'] ) {
		echo 'Path not found';
	} else {
		if ( $output['inverse'] ) {
			echo implode( ' => ', array_reverse( $output['path'] ) ) . ' => ' . $output['latency'];
		} else {
			echo implode( ' => ', $output['path'] ) . ' => ' . $output['latency'];
		}
	}

//$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $output );
//
//foreach ( $devicesWithDesiredOutput as $device ) {
//	$device = $networkMap[ $device ];
//
//	if ( $device[0] === $input ) {
//		// If the input for this device matches the desired input, return the latency
//		return $device[2];
//	}
//
//	// Find network paths with output = current path's input device
//	$devicesWithDesiredOutput2 = array_keys( array_column( $networkMap, 1 ), $device[0] );
//
//	foreach ( $devicesWithDesiredOutput2 as $value ) {
//		$device2 = $networkMap[ $value ];
//		if ( $device2[0] === $input ) {
//			// If the input for this device matches the desired input, return the latency
//
//			return $device2[2];
//		}
//	}
////	find_paths_with_output( $networkMap, $input, $device[1] );
//}


	exit;
}

function dd( $value )
{
	echo '<pre>';
	var_dump( $value );
	echo '</pre>';
	exit;
}
//echo "Finding the first network path. Please wait...\n";


//while ( ( $data = fgetcsv( $file, 1000, "," ) ) !== false ) {
//	var_dump( $data );
//}

//$nodes = [
//	// [ Device from , Device to, Latency(milliseconds) ]
//	[ 'A', 'B', 10 ],
//	[ 'A', 'C', 20 ],
//	[ 'B', 'D', 100 ],
//	[ 'C', 'D', 30 ],
//	[ 'D', 'E', 10 ],
//	[ 'E', 'F', 1000 ],
//];
//
