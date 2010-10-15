<?php
class MinifyCssHelper extends AppHelper {
	function link($string,$html5=true,$compression_type='gz') {
		if($html5) $output = sprintf('<link rel="stylesheet" href="/minify/js/%s/%s">',$compression_type,$string);
		else $output = sprintf('<link rel="stylesheet" type="text/css" href="/minify/js/%s/%s" />',$compression_type,$string);
		return $output;
	}
}