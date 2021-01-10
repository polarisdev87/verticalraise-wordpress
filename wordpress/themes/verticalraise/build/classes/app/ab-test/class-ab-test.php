<?php

namespace classes\app\ab_test;

/**
 * AB Test
 */
class AB_Test {

	/**
	 * Pass the variants into the randomizer
	 *
	 * @param array $options The options to test.
	 * @return array|string The results.
	 */
	public function run_test( $options ) {
		$variants   = count( $options );
		$result_key = $this->randomizer( $variants );

		return $options[ $result_key ];
	}

	/**
	 * Pick one of the variants at random.
	 *
	 * @param integer $num_variants The number of variants.
	 * @return integer The selected variant.
	 */
	private function randomizer( $num_variants ) {
		// Subtract by 1 to star the array index at 0.
		return wp_rand( 0, $num_variants - 1 );
	}
}
