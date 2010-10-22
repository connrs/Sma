<?php
class MinifyHelper extends AppHelper {
	function js_link($string,$html5=true,$compression_type='gz') {
		if(!$html5) $output = sprintf('<script type="text/javascript" src="/minify/js/%s/%s"></script>',$compression_type,$string);
		else $output = sprintf('<script src="/minify/js/%s/%s"></script>',$compression_type,$string);
		return $output;
	}
	function css_link($string,$media='',$html5=true,$compression_type='gz') {
		if(!empty($media)) $media = " media=\".$media.\"";
		if($html5) $output = sprintf('<link rel="stylesheet" href="/minify/css/%s/%s"%s>',$compression_type,$string,$media);
		else $output = sprintf('<link rel="stylesheet" type="text/css" href="/minify/css/%s/%s"%s />',$compression_type,$string,$media);
		return $output;
	}
}