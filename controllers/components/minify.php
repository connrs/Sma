<?php
App::import('Vendor','Minify.jsmin');
App::import('Vendor','Minify.cssfast');
Cache::config('minify', array(
	'engine' => 'File',
	'duration'=> '+2 days',
	'path' => CACHE.'views'.DS,
	'prefix' => 'cake_minify_'
));
class MinifyComponent extends Object {
	function js($file_list) { //Take an array of file names with relative paths
		if(!is_array($file_list)) { //If you passed a single string, convert to an array
			if(is_string($file_list)) $file_list = array($file_list);
			else trigger_error('Invalid file list for JS minification');
		}
		$cache_md5 = md5(serialize($file_list));
		$cache_file_key = $cache_md5.'_js';
		if(Cache::read($cache_file_key,'minify')) { //If there is a file, return the md5 for use in the controller
			return $cache_md5;
		} else {
			$cache_string = '';
			foreach($file_list as $x=>$file) {
				$source_file_path = APP.'webroot'.DS.$file;
				if(file_exists($source_file_path)) { //If the file exists, minify it and add it to the string
					if(!preg_match('/[a-z]+/',$x)) {
						$minified_string = trim(JSMin::minify(file_get_contents($source_file_path)));
					} else {
						$minified_string = trim(file_get_contents($source_file_path));
					}
					if(!empty($minified_string)) {
						$cache_string .= $minified_string;
					} else {
						trigger_error(sprintf('File %s was empty while calling JSMin::minify()',$source_file_path));
					}
				} else {trigger_error(sprintf('File %s not found while calling Minify::js()',$source_file_path));}
			}
			if(!empty($cache_string)) { //Provided the string isn't empty, cache it and then return the md5 for use in the controller
				$cache_string = gzencode($cache_string,9);
				Cache::write($cache_file_key,$cache_string,'minify');
				return $cache_md5;
			} else {return false;}
		}
	}
	
	function css($file_list) {
		if(!is_array($file_list)) { //If you passed a single string, convert to an array
			if(is_string($file_list)) $file_list = array($file_list);
			else trigger_error('Invalid file list for CSS minification');
		}
		$cache_md5 = md5(serialize($file_list));
		$cache_file_key = $cache_md5.'_css';
		if(Cache::read($cache_file_key,'minify')) { //If there is a file, return the md5 for use in the controller
			return $cache_md5;
		} else {
			$cache_string = '';
			$css_string = '';
			foreach($file_list as $file) {
				$source_file_path = APP.'webroot'.DS.$file;
				if(file_exists($source_file_path)) { //If the file exists, minify it and add it to the string
					$css_string .= file_get_contents($source_file_path)."\n";
				} else {trigger_error(sprintf('File %s not found while calling Minify::js()',$source_file_path));}
			}
			$css_fast = new CSSFast($css_string);
			$css_string = $css_fast->outputFast();
			if(!empty($css_string)) {
				$cache_string = gzencode($css_string,9);
				Cache::write($cache_file_key,$cache_string,'minify');
				return $cache_md5;
			} else {return false;}
		}
	}
}
