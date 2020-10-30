<?php

// Ask for CSV file path
// Check if CSV file exists || validate CSV
// Ask for input variables
// Validate input variables
// Ask for Input path, output path and latency limit
// Parse CSV

// Load our helper functions
require_once( 'helper.php' );

if ( php_sapi_name() === 'cli' ) {
	//	 Ask for CSV file path
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
	}

	$input        = $parsedUserInput[0];
	$output       = $parsedUserInput[1];
	$latencyLimit = $parsedUserInput[2];
	$inverse      = false;

	// Check our Input and Output
	if ( strcmp( strtolower( $input ), strtolower( $output ) ) === 0 ) {
		echo 'Sorry, the Input and Output identifiers cannot be identical.';
		exit;
	} elseif ( strcmp( strtolower( $input ), strtolower( $output ) ) > 0 ) {
		$inverse = true;
		// Reverse the order of the input and output
		$input  = $parsedUserInput[1];
		$output = $parsedUserInput[0];
	}

	$file       = fopen( $filepath, 'r' );
	$networkMap = [];

	while ( ( $result = fgetcsv( $file ) ) !== false ) {
		$networkMap[] = $result;
	}

	// Find network paths ending with the desired output
	$startingDevices = find_devices_with_output( $output, $networkMap );
	$returnObj       = [
		'path'    => [],
		'latency' => 0,
	];

	$returnObj = find_paths_with_output( $startingDevices, $returnObj, $input, $networkMap, $latencyLimit );

	if ( ! $returnObj['path'] ) {
		echo 'Path not found';
	} else {
		$path = $inverse ? array_reverse( $returnObj['path'] ) : $returnObj['path'];
		echo implode( ' => ', $path ) . ' => ' . $returnObj['latency'];
	}

	exit;
}