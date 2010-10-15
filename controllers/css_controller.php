<?php
class CssController extends MinifyAppController {
	var $name = 'Css';
	var $uses = null;

	function beforeFilter() {
		parent::beforeFilter();
		$this->RequestHandler->respondAs('css');
	}

	function gz($cache_md5=null) {
		$cached_string = Cache::read($cache_md5.'_css','minify');
		if(empty($cached_string)) {
			$cached_string = '/*Error loading minified CSS*/';
		} else {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($cached_string));
		}
		$this->set('cached_js',$cached_string);
		$this->render('index');
	}
}