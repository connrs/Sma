<?php
class JsController extends MinifyAppController {
	var $name = 'Js';
	var $uses = null;

	function beforeFilter() {
		parent::beforeFilter();
		$this->RequestHandler->respondAs('javascript');
	}

	function gz($cache_md5=null) {
		$time_buffer = 2592000;
		$param_ts = (int) end(array_keys($_GET));
		$param_date = gmdate('D, d M Y H:i:s e',$param_ts);
		$f_param_date = gmdate('D, d M Y H:i:s e',$param_ts+$time_buffer);
		$cached_string = Cache::read($cache_md5.'_js','minify');
		if(empty($cached_string)) {
			$cached_string = '//Error loading minified Javascript';
		} else {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($cached_string));
			header('Content-Type: application/x-javascript');
			header('Cache-Control: public, max-age='.$time_buffer);
			header('Expires: '.$f_param_date);
			header('Last-Modified: '.$param_date);
			if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				$client_date = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
				$file_date = (integer) $param_ts;
				if ($client_date >= $file_date) {
					header('HTTP/1.1 304 Not Modified');
					exit;
				}
			}
		}
		$this->set('cached_js',$cached_string);
		$this->render('index');
	}
}
