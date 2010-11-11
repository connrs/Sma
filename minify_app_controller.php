<?php
class MinifyAppController extends AppController {
	var $components = array('RequestHandler');
	var $helpers = null;
	var $uses = null;

	function __mergeVars() {}

	function beforeFilter() {
		if(isset($this->Auth)) $this->Auth->allow('*');
		Cache::config('minify', array(
			'engine' => 'File',
			'duration'=> '+2 days',
			'path' => CACHE.'views'.DS,
			'prefix' => 'cake_minify_'
		));
		return true;
	}

	function beforeRender() {return true;}
}