<?php
// Call DBTest::main() if this source file is executed directly.
if (!defined("PHPUnit2_MAIN_METHOD")) {
    define("PHPUnit2_MAIN_METHOD", "DBTest::main");
}

require_once "PHPUnit2/Framework/TestCase.php";
require_once "PHPUnit2/Framework/TestSuite.php";

// You may remove the following line when all tests have been implemented.
require_once "PHPUnit2/Framework/IncompleteTestError.php";

require_once "../MDB/libs/DB.php";

/**
 * Test class for DB.
 * Generated by PHPUnit2_Util_Skeleton on 2010-01-07 at 14:40:38.
 */
class DBTest extends PHPUnit2_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit2/TextUI/TestRunner.php";

        $suite  = new PHPUnit2_Framework_TestSuite("DBTest");
        $result = PHPUnit2_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     * @todo Implement testConnect().
     */
    public function testConnect() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testSelect_db().
     */
    public function testSelect_db() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testQuery().
     */
    public function testQuery() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testFetch_assoc().
     */
    public function testFetch_assoc() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testEscape().
     */
    public function testEscape() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testClose().
     */
    public function testClose() {
        // Remove the following line when you implement this test.
        throw new PHPUnit2_Framework_IncompleteTestError;
    }
}

// Call DBTest::main() if this source file is executed directly.
if (PHPUnit2_MAIN_METHOD == "DBTest::main") {
    DBTest::main();
}
?>
