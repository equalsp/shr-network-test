<?php

// Ask for CSV file path
// Check if CSV file exists || validate CSV
// Ask for input variables
// Validate input variables
// Parse CSV
// Ask for Input path, output path and time limit

//if ( php_sapi_name() === 'cli' ) {
// Ask for CSV file path
//	$filepath = readline( 'Please enter your CSV file path: ' );
//
//	// Check if CSV file exists || validate CSV
//	if ( ! ( file_exists( $filepath ) || is_file( $filepath ) ) ) {
//		echo "Sorry, the file path that you entered is not readable. Please ensure that the path to the file is correct and that PHP has permissions to access the file. \n";
//		exit;
//	}
//	// Ask for input variables
//	$parsedUserInputPassesValidation = null;
//	do {
//		if ( $parsedUserInputPassesValidation === false ) {
//			$userInput = readline( 'Sorry, the input does not match the required format (Input Output Latency). Please try again.' );
//		} else {
//			$userInput = readline( 'Please enter your desired start and end devices and latency limit: (Input Output Latency)' );
//		}
//
//		$parsedUserInput = explode( ' ', $userInput );
//
//		// Validate input variables
//		$parsedUserInputPassesValidation = count( $parsedUserInput ) === 3;
//	} while ( $parsedUserInputPassesValidation === false );
//
//
//	$input        = $parsedUserInput[0];
//	$output       = $parsedUserInput[1];
//	$latencyLimit = $parsedUserInput[2];


//$sampleData = [
//  [ Device from , Device to, Latency(milliseconds) ]
//	[ 'A', 'B', 10 ],
//	[ 'A', 'C', 20 ],
//	[ 'B', 'D', 100 ],
//	[ 'C', 'D', 30 ],
//	[ 'D', 'E', 10 ],
//	[ 'E', 'F', 1000 ],
//];

// TESTING VARIABLES
$input        = 'A';
$output       = 'D';
$latencyLimit = 100;
$filepath     = 'sample.csv';
// END TESTING VARIABLES

$file       = fopen( $filepath, 'r' );
$networkMap = [];

while ( ( $result = fgetcsv( $file ) ) !== false ) {
	$networkMap[] = $result;
}

if ( ! $networkMap ) {
	echo "Sorry, it seems the CSV you've selected has no valid data. Please try again \n";
	exit;
}

// Find network paths ending with the desired output
function find_paths_with_output( $networkMap, $input, $output, $latencyLimit, &$returnObj = [ 'path' => [], 'latency' => 0, 'inverse' => 0 ] )
{
	if ( $returnObj['inverse'] ) {
		$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $input );
	} else {
		$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $output );
	}

	foreach ( $devicesWithDesiredOutput as $value ) {
		$device = $networkMap[ $value ];

		echo 'PAIR: ' . $device[0] . ' ' . $device[1] . '<br/>';

		if ( $device[0] === $input ) {
			echo 'MATCH<br/>';
//			$totalLatency = $returnObj['latency'] + $device[2];
//			if ( $totalLatency > $latencyLimit ) {
//				continue;
//			}

			array_push( $returnObj['path'], $device[0], $device[1] );
			$returnObj['latency'] += $device[2];
//			echo 'LATENCY2 - ' . $returnObj['latency'] . ' ' . $device[0] . ' ' . $device[1] . '</br>';

			// If the input for this device matches the desired input, return the latency
			return $returnObj;
		}

		// If the total latency is over the latency limit, then skip this tree
//		$totalLatency = $returnObj['latency'] + $device[2];
//		if ( $totalLatency > $latencyLimit ) {
//			continue;
//		}

		echo 'FINDING<br/>';
		$found = find_paths_with_output( $networkMap, $input, $device[0], $latencyLimit, $returnObj );
		if ( $found['path'] ) {
			echo 'FOUND ' . $device[0] . $device[1] . '<br/>';
//			$totalLatency = $returnObj['latency'] + $device[2];
//			if ( $totalLatency > $latencyLimit ) {
//				continue;
//			}

			array_push( $returnObj['path'], $device[1] );
			$returnObj['latency'] += $device[2];

//			echo 'LATENCY3 - ' . $returnObj['latency'] . ' ' . $device[0] . ' ' . $device[1] . '</br>';

			return $returnObj;
		}

	}

	return $returnObj;
}

$returnObj = [
	'path'    => [],
	'latency' => 0,
	'inverse' => 0,
];

$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $output );
if ( ! count( $devicesWithDesiredOutput ) ) {
	// If we can't find paths ending with the $output, look in reverse
	$devicesWithDesiredOutput = array_keys( array_column( $networkMap, 1 ), $input );
	$input                    = $output;
	$returnObj['inverse']     = 1;
}

foreach ( $devicesWithDesiredOutput as $value ) {
	$device = $networkMap[ $value ];

	$output = find_paths_with_output( $networkMap, $input, $device[0], $latencyLimit, $returnObj );
	if ( $output['path'] && $output['latency'] <= $latencyLimit ) {
		continue;
	} else {
		$returnObj['path'] = [];
	}
}


if ( ! $output['path'] ) {
	echo 'Path not found';
} else {
	if ( $output['inverse'] ) {
		echo implode( ' => ', array_reverse( $output['path'] ) ) . ' => ' . $output['latency'];
	} else {
		echo implode( ' => ', $output['path'] ) . ' => ' . $output['latency'];
	}
}

exit;
//}

function dd( $value )
{
	echo '<pre>';
	var_dump( $value );
	echo '</pre>';
	exit;
}