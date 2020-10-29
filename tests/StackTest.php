<?php declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

final class StackTest extends TestCase
{

	/** @test
	 * @dataProvider sampleDataProvider
	 * Function can find devices with particular output from sample data
	 *
	 * @param $output
	 * @param $expected
	 */
	public function function_can_find_devices_with_particular_output_from_sample_data( $output, $expected )
	{
		include_once( 'helper.php' );

		$networkMap = [
			[ 'A', 'B', 10 ],
			[ 'A', 'C', 20 ],
			[ 'B', 'D', 100 ],
			[ 'C', 'D', 30 ],
			[ 'D', 'E', 10 ],
			[ 'E', 'F', 1000 ],
		];

		$this->assertEquals( $expected, find_devices_with_output( $output, $networkMap ) );

	}

	public function sampleDataProvider(): array
	{
		return [
			[ 'A', [] ],
			[ 'B', [ 0 ] ],
			[ 'C', [ 1 ] ],
			[ 'D', [ 2, 3 ] ],
			[ 'E', [ 4 ] ],
			[ 'F', [ 5 ] ],
		];
	}

	/** @test
	 * @dataProvider samplePathsProvider
	 * Function can find paths with a particular output
	 *
	 * @param $input
	 * @param $output
	 * @param $latencyLimit
	 * @param $expected
	 */
	public function function_can_find_paths_with_a_particular_output( $input, $output, $latencyLimit, $expected )
	{
		$networkMap = [
			[ 'A', 'B', 10 ],
			[ 'A', 'C', 20 ],
			[ 'B', 'D', 100 ],
			[ 'C', 'D', 30 ],
			[ 'D', 'E', 10 ],
			[ 'E', 'F', 1000 ],
		];

		$devices   = find_devices_with_output( $output, $networkMap );
		$returnObj = [
			'path'    => [],
			'latency' => 0,
		];

//		die(var_dump(find_paths_with_output( $devices, $returnObj, $input, $networkMap, $latencyLimit )));
		$this->assertEquals( $expected, find_paths_with_output( $devices, $returnObj, $input, $networkMap, $latencyLimit ) );
	}

	public function samplePathsProvider(): array
	{
		return [
			[
				'A',
				'B',
				100,
				[
					'path'    => [ 'A', 'B' ],
					'latency' => 10,
				],
			],
			[
				'A',
				'F',
				1000,
				[
					'path'    => [],
					'latency' => 0,
				],
			],
			[
				'A',
				'F',
				1200,
				[
					'path'    => [ 'A', 'B', 'D', 'E', 'F' ],
					'latency' => 1120,
				],
			],
			[
				'A',
				'D',
				100,
				[
					'path'    => [ 'A', 'C', 'D' ],
					'latency' => 50,
				],
			],
			[ // Reverse because input and output will be sorted prior
				'A',
				'E',
				400,
				[
					'path'    => [ 'A', 'B', 'D', 'E' ],
					'latency' => 120,
				],
			],
			[ // Reverse because input and output will be sorted prior
				'A',
				'E',
				80,
				[
					'path'    => [ 'A', 'C', 'D', 'E' ],
					'latency' => 60,
				],
			],
		];
	}
}