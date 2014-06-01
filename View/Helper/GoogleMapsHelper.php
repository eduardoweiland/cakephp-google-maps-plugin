<?php
/**
 * CakePHP Google Maps Plugin
 *
 * @copyright Copyright (c) 2014, Eduardo Weiand <eduardo@eduardoweiland.info>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * GoogleMapsHelper
 *
 * @property HtmlHelper $Html
 * @property JsHelper $Js
 */
class GoogleMapsHelper extends AppHelper {

    public $helpers = array('Html', 'Js');

    /**
     * Google Maps JavaScript API v3 URL
     * @var string
     */
    protected $_API_URL = '//maps.googleapis.com/maps/api/js';

    /**
     * JavaScript class name used by this plugin
     * @var type
     */
    protected $_JS_CLASS = 'CakePHPGoogleMaps';

    /**
     * Creates a <script> tag for loading Maps API. The tag is appended to `$scripts_for_layout` by default, but you
     * can change this behaviour passing 'inline' => true in the options.
     *
     * @param array $params Parameters added on API URL. Defaults to `array('sensor' => false)`.
     * @param array $options Options used for loading the script (@see HtmlHelper::script). Defaults to
     *        `array('inline' => false)`.
     * @return mixed Value returned by {@link HtmlHelper::script}.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::script
     * @link https://developers.google.com/maps/documentation/javascript/tutorial#Loading_the_Maps_API
     */
    public function loadAPI($params = array(), $options = array()) {
        $params = array_merge(array('sensor' => false), $params);
        $options = array_merge(array('inline' => false), $options);
        return $this->Html->script($this->_API_URL . '?' . http_build_query($params), $options);
    }

    /**
     * Create a new Google Maps map.
     *
     * ### Options
     *
     * - `map` Array of options for the map:
     *     - `latitude` and `longitude` or `center` REQUIRED. Use this option to specify the initial location displayed
     *     on map. You can use `latitude` and `longitude` separatedly or you can use `center` as an array with keys
     *     `lat` and `lng` (as accepted by Google Maps API literal LatLng object).
     *     - `zoom` Initial zoom applied on map. Defaults to 8.
     *     - any other options accepted by google.maps.Map constructor.
     * - `div` Attributes used for the <div> which is used for display the map. Will be passed to HtmlHelper::tag.
     *
     * @param array $options Array of options, as defined above.
     * @return string HTML string containing the <div> created for the map and some inline JavaScript.
     * @link https://developers.google.com/maps/documentation/javascript/reference#MapOptions
     */
    public function map($options = array()) {
        $options = array_merge(array('zoom' => 8, 'div' => array()), $options);

        // Create a <div> to hold the created map
        $this->_currentId = $options['div']['id'] = uniqid('CakePHPGoogleMaps');
        $div = $this->Html->tag('div', '', $options['div']);
        unset($options['div']);

        if (!isset($options['center'])) {
            $options['center'] = array('lat' => $options['latitude'], 'lng' => $options['longitude']);
            unset($options['latitude']);
            unset($options['longitude']);
        }

        $this->Html->script('GoogleMaps.googlemaps', array('inline' => false));

        $json = json_encode($options);
        // TODO: support for multiple maps
        $script = "window.map = new {$this->_JS_CLASS}('{$this->_currentId}', $json);";

        return $div . $this->_outputScript($script);
    }

    /**
     * Catch all method calls and check if they are a call to the JavaScript API.
     *
     * This method recognizes calls in the format (add|remove)[itemType]. This allows to execute addMarker, addCircle,
     * removeRectangle and removeInfoWindow.
     *
     * @param string $name Name of method called.
     * @param array $arguments Arguments passed when calling the method.
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (preg_match('/([^A-Z]*)(.*)/', $name, $matches)) {
            list(,$method,$type) = $matches;

            if (in_array($method, array('add', 'remove'))) {
                $args = $this->_parseJsArguments($arguments);
                return $this->_outputScript("window.map.{$method}('{$type}', $args);");
            }
        }

        parent::__call($name, $arguments);
    }

    /**
     * If JavaScript buffering is enabled, append the script on the buffer, or if it is disabled return it within
     * script tags.
     *
     * @param string $code JavaScript code
     */
    private function _outputScript($code) {
        if ($this->Js->bufferScripts) {
            return $this->Js->buffer($code);
        }
        return $this->Html->scriptBlock($code);
    }

    /**
     * Format list of arguments to be passed to a JavaScript call.
     *
     * @param array $arguments List of arguments.
     * @return string A string of all arguments separated by `,` ready to be passed as arguments to a JavaScript
     *   function call.
     */
    private function _parseJsArguments($arguments) {
        foreach ($arguments as &$arg) {
            $arg = $this->Js->object($arg);
        }

        return implode(',', $arguments);
    }
}