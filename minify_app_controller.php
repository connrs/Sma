<?php
class MinifyAppController extends Controller {
	var $components = array('RequestHandler');
	var $helpers = false;
	var $uses = false;
	
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
?>
