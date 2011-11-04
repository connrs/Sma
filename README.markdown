#Små

This is an asset packager plugin for CakePHP. It is a rewrite of the earlier Minify with more readable code, a different method and tests on the way.

The main difference between Minify and Sma is that Sma places makes assets accessible from inside the webroot as opposed to Minify's controllers serving cached content and creating headers. This means that it's down to you to configure caching and zipping for the assets on your particular server. This reduces the number of CakePHP app instances for visitors with uncached assets (what was I thinking) and means that I can focus on tests rather than HTTP caching.

At present I've only seen it running in CakePHP 1.3 and I will eventually get around to testing it in CakePHP 2

To use this from within a controller (or AppController if you want to define it globally), you need to add:

* 'Sma.Sma' to $components
* 'Sma.Sma' to $helpers

##Building

The Sma component build method takes an associative array (where each key represents one packaged asset file) with sub arrays containing file path strings detailing the location of individual files. If the files are considered to be already cached, the method adds the asset filename to the return array; if it isn't cached, each file is loaded, minified and concatenated to a an asset file and saved to the asset directory in WWW_ROOT. The build method will return an array of asset filenames based upon the associative array.

###Pre-concatenation

Additionally, build can recognise an array of files for pre-concatenation before minification. This is useful if you have a JS file that's split into multiple files for easy editing, but it actually needs joining as you have an anonymous function wrapper when fully built. For example:

    _top.js_
    (function(window,document){
      var potato = {};
      
    _middle.js_
    potato.king = \[1,2,3\];
    
    _bottom.js_
    })(this,this.document);

Then in your controller beforeRender/beforeFilter/wherever method:

    $js = array(
        'example' => array(
            'sample1.js',
            'sample1.js',
            array(
                'top.js', 'middle.js', 'bottom.js'
            )
        )
    );

###Prebuilding templates

You can also build templates and combine them in to an object assigned to a specified variable. The method is similar to concatenating multiple files except the array of files is given an alphanumeric key string (used to create a variable for the templates eg. var key={//template object}). The array needs to be an associative array as the keys are used to create the template object key name. In addition, the keys \['_head'\] and \['_foot'\] are used to create a wrapper around the final object. Useful if you want to create an anonymous function wrapper. The head and foot keys must exist, but they may be blank files.

    $js = array(
        'stuff' => array(
            'sample1.js',
            'anothernormaljsfile.js',
            array(
                'tplVariable' => array(
                    '_head' => 'head.js'
                    '_foot' => 'foot.js'
                    'noteView' => 'note_view.js',
                    'tagView' => 'tag_view.js'
                )
            )
        )
    );

The stuff file will contain a templates var like this:

    var tplVariable = {
        noteView: "/* template file as string here */",
        tagView: "/* template file as string here */"
    };

##Using it in your view templates

In your view templates you need to use the Sma Helper. Passing, from your controller, the build array, you can then use the SmaHelper::link method to create the HTML elements in your views:

    _In your controller:_
    $js = $this->Sma->build($jsArray);
    $this->set('js_asset', $js);
    
    _In your template:_
    <?php $this->Sma->link($js_asset['default'], 'js') ?>
    
    _Output:_
    <script src="/assets/default.xxxx.js"></script>

##Configuration of helper & component

It is possible to set custom paths and cache configurations. I will document it in this README soon but, for now, please check out the source to see which declared class variables can be customised.

##Installation requirements

Just drop it in as a gitsubmodule to your app/plugins directory or download the zipfile from the download link here on Github. There are 2 directories which you need to create: app/tmp/sma and app/webroot/assets

There is a sample.htaccess included that you should adapt and potentially place inside your assets folder. It is a stripped down version of the HTML5 Boilerplate .htaccess file. To be honest, you should consider adding the HTML5 boilerplate generally to your webroot or your server configuration so that it is less of a performance hit on the HTTP server.

#Coming soon

CSSFast plugin does some limited compression but I hope to extend the capabilities of thos plugin itself. I'm looking into Less and OOCSS and hope to come to some sort of decision soon. Although I honestly prefer to optimise my own CSS myself.

#Acknowledgements

Thanks to [rgrove](http://github.com/rgrove) for porting [jsmin-php](http://github.com/rgrove/jsmin-php/) that is used for javascript minification.

#Licence

Copyright (c) 2011 Paul Connolley

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
