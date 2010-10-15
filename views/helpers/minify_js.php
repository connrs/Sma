<?php
class MinifyJsHelper extends AppHelper {
	function link($string,$html5=true,$compression_type='gz') {
		if(!$html5) $output = sprintf('<script type="text/javascript" src="/minify/js/%s/%s"></script>',$compression_type,$string);
		else $output = sprintf('<script src="/minify/js/%s/%s"></script>',$compression_type,$string);
		return $output;
	}
}