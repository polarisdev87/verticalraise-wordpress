<?php
/**
 * Class JoinCode
 *
 * @package Vertical_Raise_Theme_Buildout
 */

/**
 * Sample test case.
 */
class JoinCodesTest extends WP_UnitTestCase {

    /**
     * test join codes work
     */
    public function test_code() {

        $j = new Join_Codes();
        $code = $j->generate_code();
        $this->assertTrue( is_int($code) );
    }
}
