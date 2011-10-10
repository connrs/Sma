<?php
App::uses('Folder', 'Utility');
class SmaShell extends Shell {
    public $assetPath;

    public function main() {
        $this->help();
    }

    public function help() {
        $this->out('Små Clean:');
        $this->out("cake sma cleanAssets - Clean minified assets.");
        $this->hr();
    }

    public function initialize() {
        $this->assetPath = WWW_ROOT.DS.'assets';
        return true;
    }

    public function __clean($path) {
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

    public function cleanAssets() {
        $assetPath = $this->assetPath;
        $this->__clean($assetPath);
        $this->out('Assets cache cleaned.');
    }    
}
