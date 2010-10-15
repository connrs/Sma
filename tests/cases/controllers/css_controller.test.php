<?php
/* Csses Test cases generated on: 2010-10-15 13:10:09 : 1287147009*/
App::import('Controller', 'Minify.Csses');

class TestCssesController extends CssesController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class CssesControllerTestCase extends CakeTestCase {
	function startTest() {
		$this->Csses =& new TestCssesController();
		$this->Csses->constructClasses();
	}

	function endTest() {
		unset($this->Csses);
		ClassRegistry::flush();
	}

}
?>