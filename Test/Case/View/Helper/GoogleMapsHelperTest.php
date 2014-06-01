<?php

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('GoogleMapsHelper', 'GoogleMaps.View/Helper');

/**
 * GoogleMapsHelper Test Case
 *
 * @property GoogleMapsHelper $GoogleMaps
 */
class GoogleMapsHelperTest extends CakeTestCase {

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->GoogleMaps = new GoogleMapsHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->GoogleMaps);
        parent::tearDown();
    }

    /**
     * testLoadAPI method
     *
     * @return void
     */
    public function testLoadAPI() {
        // TEST 1: without parameters, should include Google Maps script in $scripts_for_layout and return null
        $result = $this->GoogleMaps->loadAPI();
        $this->assertEquals($result, null);

        // TEST 2: include some additional libraries and force script to be inline
        $result = $this->GoogleMaps->loadAPI(array('libraries' => 'places'), array('inline' => true));
        $this->assertTags($result, array(
            'script' => array(
                'type' => 'text/javascript',
                'src' => 'preg:/.*libraries=places.*/'
            )
        ));
    }

}
