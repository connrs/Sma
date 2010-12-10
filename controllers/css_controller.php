<?php
class CssController extends MinifyAppController {
	var $name = 'Css';
	var $uses = null;

	function beforeFilter() {
		parent::beforeFilter();
		$this->RequestHandler->respondAs('css');
	}

	function gz($cache_md5=null) {
		$time_buffer = 2592000;
		$param_ts = end(array_keys($_GET));
		$param_date = gmdate('D, d M Y H:i:s e',$param_ts);
		$f_param_date = gmdate('D, d M Y H:i:s e',$param_ts+$time_buffer);
		$cached_string = Cache::read($cache_md5.'_css','minify');
		if(empty($cached_string)) {
			$cached_string = '/*Error loading minified CSS*/';
		} else {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($cached_string));
			header('Content-Type: text/css');
			header('Cache-Control: public, max-age='.$time_buffer);
			header('Expires: '.$f_param_date);
			header('Last-Modified: '.$param_date);
			if(($_SERVER['REQUEST_METHOD']=='HEAD') || (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE']==$param_date)) {
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}
		$this->set('cached_js',$cached_string);
		$this->render('index');
	}
}
