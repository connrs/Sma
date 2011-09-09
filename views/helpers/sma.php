<?php
/*
 *
 */
class SmaHelper extends AppHelper {
	var $cacheConfiguration;
	var $cacheKey;
	var $publicAssetPath;

	function __construct($options = null) {
		if ($options == null) {
			$options = array();
		}
		$__options = array(
			'publicAssetPath' => '/assets',
			'cacheConfiguration' => array(
				'engine' => 'File',
				'duration' => '+30 days',
				'path' => CACHE . 'sma' . DS,
				'prefix' => ''
			),
			'cacheKey' => 'Minify.minify'
		);
		$options = array_merge($__options, $options);
		$this->_set($options);
	}
	function js_link($string, $options = array()) {
		$__options = array(
			'html5' => true
		);
		$options = array_merge($__options, $options);
		if (empty($string)) {
			return '';
		}
		$url = $this->url($this->publicAssetPath . DS . $string);
		if (!$options['html5']) {
			$output = sprintf('<script type="text/javascript" src="%s"></script>',$url);
		} else {
			$output = sprintf('<script src="%s"></script>',$url);
		}
		return $output;
	}

	function css_link($string, $options = array()) {
		$__options = array(
			'html5' => true,
			'media' => ''
		);
		$options = array_merge($__options, $options);
		if (empty($string)) {
			return '';
		}
		if (!empty($options['media'])) {
			$media = " media=\"{$options['media']}\"";
		} else {
			$media = '';
		}
		$url = $this->url($this->publicAssetPath . DS . $string);
		if ($options['html5']) {
			$output = sprintf('<link rel="stylesheet" href="%s"%s>',$url,$media);
		} else {
			$output = sprintf('<link rel="stylesheet" type="text/css" href="%s"%s />',$url,$media);
		}
		return $output;
	}

	function link($filename, $type, $options = array()) {
		if ($type == 'js') {
			return $this->js_link($filename);
		} else if ($type == 'css') {
			return $this->css_link($filename);
		}
	}
	
	function _url($url) {
		$prefix_array = !empty($this->params['prefix'])?array($this->params['prefix']=>false):array();
		return parent::url(array_merge($prefix_array,$url));
	}
}
