<?php

class AllTestsTest extends CakeTestSuite {

    public static function suite() {
        $suite = new CakeTestSuite('All Tests');
		$path = dirname(__FILE__);
		$suite->addTestDirectory($path . DS . 'View' . DS . 'Helper');
        return $suite;
    }

}
