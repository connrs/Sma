<?php
class SmaShell extends Shell {   
    var $assetPath;

    function main() {
        $this->help();
    }

    function help() {
        $this->out('Små Clean:');
        $this->out("cake sma cleanAssets - Clean minified assets.");
        $this->hr();
    }

    function initialize() {
        $this->assetPath = WWW_ROOT.DS.'assets';
        return true;
    }

    function __clean($path) {
        $folder = new Folder($path);
        $tree = $folder->tree($path, false);
        foreach ($tree as $files) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $file = new File($file);
                    $file->delete();
                }
            }
        }
        return;
    }

    function cleanAssets() {
        $assetPath = $this->assetPath;
        $this->__clean($assetPath);
        $this->out('Assets cache cleaned.');
    }    
}
