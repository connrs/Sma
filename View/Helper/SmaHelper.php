<?php
/*
 *
 */
class SmaHelper extends AppHelper {
    var $cacheConfiguration;
    var $cacheKey = 'Sma';
    var $publicAssetPath = '/assets';

    function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->_set($settings);
    }
    function js_link($string, $options = array()) {
        $_options = array(
            'html5' => true
        );
        $options = array_merge($_options, $options);
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
        $_options = array(
            'html5' => true,
            'media' => ''
        );
        $options = array_merge($_options, $options);
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
            $output = sprintf('<link rel="stylesheet" href="%s"%s>', $url, $media);
        } else {
            $output = sprintf('<link rel="stylesheet" type="text/css" href="%s"%s />', $url, $media);
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
}
