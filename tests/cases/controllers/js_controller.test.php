<?php
/* Js Test cases generated on: 2010-10-15 14:10:00 : 1287149940*/
App::import('Controller', 'Minify.Js');

class TestJsController extends JsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class JsControllerTestCase extends CakeTestCase {
	function startTest() {
		$this->Js =& new TestJsController();
		$this->Js->constructClasses();
	}

	function endTest() {
		unset($this->Js);
		ClassRegistry::flush();
	}

}
?>