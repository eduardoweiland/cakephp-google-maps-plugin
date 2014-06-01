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
     * Armazena o endereço utilizado para carregar a API do Google Maps para JavScript.
     * @var string
     */
    protected $_API_URL = "//maps.googleapis.com/maps/api/js";

    /**
     * Adiciona a tag para carregar o script da API em JavaScript do Google Maps.
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
     *     on map. You can use `latitude` and `longitude` separatedly or you can use `center` in the format returned by
     *     {@link GoogleMapsHelper::latLng}.
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

        $json = json_encode($options, JSON_FORCE_OBJECT);
        $script = "CakePHPGoogleMaps.create('{$this->_currentId}', $json)";

        return $div . $this->_outputScript($script);
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
}