<?php
/*
 *
 */
class MinifyHelper extends AppHelper {
//	var $helpers = array('Html');

	function js_link($string,$html5=true,$compression_type='gz') {
		if(empty($string)) return '';
		$url = $this->url(array('plugin'=>'minify','controller'=>'js','action'=>$compression_type,$string));
		if(!$html5) $output = sprintf('<script type="text/javascript" src="%s"></script>',$url);
		else $output = sprintf('<script src="%s"></script>',$url);
		return $output;
	}

	function css_link($string,$media='',$html5=true,$compression_type='gz') {
		if(empty($string)) return '';
		if(!empty($media)) $media = " media=\"$media\"";
		$url = $this->url(array('plugin'=>'minify','controller'=>'css','action'=>$compression_type,$string));
		if($html5) $output = sprintf('<link rel="stylesheet" href="%s"%s>',$url,$media);
		else $output = sprintf('<link rel="stylesheet" type="text/css" href="%s"%s />',$url,$media);
		return $output;
	}
	
	function url($url) {
		$prefix_array = !empty($this->params['prefix'])?array($this->params['prefix']=>false):array();
		return parent::url(array_merge($prefix_array,$url));
	}
}
