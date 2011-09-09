<?php
App::import('Vendor','Minify.jsmin');
App::import('Vendor','Minify.cssfast');
class SmaComponent extends Object {
	var $baseAssetCachePath;
	var $cacheConfiguration;
	var $cacheKey;
	var $cachedAssets;
	var $Controller;
	var $currentCacheInfo;
	var $customMinification = array(
		//'js' => array('command'=>''),
		//'css' => array('command'=>'')
	);
	var $errorLogFile;
	var $newCacheTag;

	function initialize(&$Controller, $settings = array()) {
		$options = array(
			'baseAssetCachePath' => WWW_ROOT . 'assets',
			'cacheConfiguration' => array(
				'engine' => 'File',
				'duration' => '+30 days',
				'path' => CACHE . 'sma' . DS,
				'prefix' => ''
			),
			'cacheKey' => 'Minify.minify',
			'errorLogFile' => LOGS . 'minification.log'
		);
		$settings = array_merge($options,$settings);
		$this->_set($settings);
		Cache::config('Minify.minify', $this->cacheConfiguration);
		$this->Controller =& $Controller;
		$this->newCacheTag = $this->genCacheTag();
		$this->currentCacheInfo = $this->getCurrentCacheInfo();
		return true;
	}
	function build($assetsList,$type) { //Take an array of file names with relative paths
		$assetListFilenames = array();
		if (is_array($assetsList)) {
			foreach ($assetsList as $assetName => $assetList) {
				if (is_array($assetList)) {
					if (!$this->isCurrentlyCached($assetName, $assetList, $type)) {
						$assetListContent = $this->processFiles($assetList, $type);
						$this->saveAssetList($assetName, $assetList, $assetListContent, $type);
					}
					$assetListFilenames[$assetName] = $this->getCurrentAssetFilename($assetName, $type);
				} else {
					trigger_error("Invalid asset list $assetName");
				}
			}
		} else {
			trigger_error("Assets list wasn't an array");
		}
		return $assetListFilenames;
	}

	private function genAssetListUID($assetList) {
		return md5(serialize($assetList));
	}
	private function genAssetFilename($assetName, $tag, $type) {
		return sprintf('%s.%s.%s', $assetName, $tag, $type);
	}
	private function genCacheTag() {
		return substr(md5(time()),0,4);
	}
	private function genFullPathToCachedAsset($assetFilename) {
		$baseAssetCachePath = $this->baseAssetCachePath;
		return $baseAssetCachePath . DS . $assetFilename;
	}
	private function getAssetContent($assetFilename) {
		$baseDir = WWW_ROOT;
		$fullPath = $baseDir . DS . $assetFilename;
		if (!file_exists($fullPath)) {
			trigger_error("File $fullPath not found when loading asset content.");
			return false;
		} else {
			$content = file_get_contents($fullPath);
			return trim($content);
		}
	}
	private function getCurrentAssetFilename($assetName,$type) {
		$cacheKey = $this->cacheKey;
		if (!empty($this->currentCacheInfo[$type][$assetName]['tag'])) {
			return $this->genAssetFilename($assetName, $this->currentCacheInfo[$type][$assetName]['tag'], $type);
		} else {
			return false;
		}
	}
	private function getCachedAssetsKeys() {
		$cacheKey = $this->cacheKey;
		return Cache::read('assetsKeys', $cacheKey);
	}
	private function getCurrentCacheInfo() {
		$cacheKey = $this->cacheKey;
		$currentCacheInfo = Cache::read('currentCacheInfo', $cacheKey);
		if (empty($currentCacheInfo)) {
			$currentCacheInfo = array('js'=>array(),'css'=>array());
		}
		return $currentCacheInfo;
	}
	private function hasCustomMinification($type) {
		$customMinification = $this->customMinification;
		return !empty($customMinification[$type]['command']);
	}
	private function isCurrentlyCached($assetName, $assetList, $type) {
		$cacheKey = $this->cacheKey;
		$assetListUID = $this->genAssetListUID($assetList);
		$isCachedAndUIDMatches = !(empty($this->currentCacheInfo[$type][$assetName]['tag']) || empty($this->currentCacheInfo[$type][$assetName]['uid'])) && $assetListUID == $this->currentCacheInfo[$type][$assetName]['uid'];
		if ($isCachedAndUIDMatches) {
			$currentCacheTag = $this->currentCacheInfo[$type][$assetName]['tag'];
			$cacheName = "$assetName.$currentCacheTag.$type";
			$cacheFilename = $this->genAssetFilename($assetName, $currentCacheTag, $type);
			$fullPathToCachedAsset = $this->genFullPathToCachedAsset($cacheFilename);
			return file_exists($fullPathToCachedAsset);
			// assetname tag type
		} else {
			return false;
		}
	}
	private function minifyAssetContent($content, $type) {
		if ($type == 'js') {
			return $this->_jsMinifyAssetContent($content);
		} else if ($type == 'css') {
			return $this->_cssMinifyAssetContent($content);
		}
	}
	private function _jsMinifyAssetContent($content) {
		if ($this->hasCustomMinification('js')) {
			$content = $this->runCustomMinificationCommand($content,'js');
		} else {
			$content = JSMin::minify($content);
		}
		return $content;
	}
	private function _cssMinifyAssetContent($content) {
		if ($this->hasCustomMinification('css')) {
			$content = $this->runCustomMinificationCommand($content,'css');
		} else {
			$cssFast = new CSSFast($content);
			$content = $cssFast->outputFast();
		}
		return $content;
	}
	private function noMinifyAsset($assetFilename) {
		return is_string($assetFilename) && preg_match('/\.min\./', $assetFilename);
	}
	private function processFiles($assetList, $type) {
		$content = "";
		foreach ($assetList as $key => $asset) {
			$assetIsMultipleFileList = is_array($asset);
			$isTemplateList = $assetIsMultipleFileList && is_string($key);
			$assetContent = "";
			if ($assetIsMultipleFileList) {
				if ($isTemplateList) {
					$variableName = Inflector::variable($key);
					$templateList = array();
					$head = $asset['_head'];
					$foot = $asset['_foot'];
					unset($asset['_head']);
					unset($asset['_foot']);
					foreach ($asset as $tKey => $mAsset) {
						$templateList[$tKey] = $this->getAssetContent($mAsset);
					}
					$assetContent .= $this->getAssetContent($head);
					$assetContent .= sprintf('var %s=%s;', $variableName, json_encode($templateList));
					$assetContent .= $this->getAssetContent($foot);
				} else {
					foreach ($asset as $mAsset) {
						$assetContent .= "\n" . $this->getAssetContent($mAsset);
					}
				}
			} else {
				$assetContent .= "\n" . $this->getAssetContent($asset);
			}
			if (!$this->noMinifyAsset($asset)) {
				$assetContent = $this->minifyAssetContent($assetContent, $type);
			}
			$content .= $assetContent;
		}
		return trim($content);
	}
	private function runCustomMinificationCommand($content, $type) {
		$command = $this->customMinification[$type]['command'];
		$logFile = $this->errorLogFile;
		$descriptorspec = array(
		   0 => array("pipe", "r"),
		   1 => array("pipe", "w"),
		   2 => array("file", $logFile, "a")
		);
		$cwd = TMP;
		$process = proc_open($command, $descriptorSpec, $pipes, $cwd);
		if (is_resource($process)) {
			fwrite($pipes[0], $content);
			fclose($pipes[0]);
			$content = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
		}
		return $content;
	}
	private function saveAssetList($assetName, $assetList, $assetListContent, $type) {
		$assetListUID = $this->genAssetListUID($assetList);
		$cacheKey = $this->cacheKey;
		$cacheTag = $this->newCacheTag;
		$assetListFilename = $this->genAssetFilename($assetName, $cacheTag, $type);
		if ($this->writeAssetContent($assetListFilename, $assetListContent)) {
			$this->currentCacheInfo[$type][$assetName] = array(
				'tag' => $cacheTag,
				'uid' => $assetListUID
			);
			$this->writeCurrentCacheInfo();
			return true;
		} else {
			return false;
		}
	}
	private function writeAssetContent($filename, $content) {
		$baseAssetCachePath = $this->baseAssetCachePath;
		$fullPath = $baseAssetCachePath . DS . $filename;
		return (bool) file_put_contents($fullPath, $content);
	}
	private function writeCurrentCacheInfo() {
		$cacheKey = $this->cacheKey;
		return Cache::write('currentCacheInfo', $this->currentCacheInfo, $cacheKey);
	}
}