<?php
class JsController extends MinifyAppController {
	var $name = 'Js';
	var $uses = null;

	function beforeFilter() {
		parent::beforeFilter();
		$this->RequestHandler->respondAs('javascript');
	}

	function gz($cache_md5=null) {
		$cached_string = Cache::read($cache_md5.'_js','minify');
		if(empty($cached_string)) {
			$cached_string = '//Error loading minified Javascript';
		} else {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($cached_string));
			header('Content-Type: text/javascript');
		}
		$this->set('cached_js',$cached_string);
		$this->render('index');
	}
}