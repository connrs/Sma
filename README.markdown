#What is this?

This is a simple Cake 1.3 plugin that you can drop in to your plugins directory (or git submodule it baby) and automatically start combining, minifying and compressing your Javascript and CSS.

At this stage, I only have javascript functionality but I will get this sorted in no time.

To use this from within a controller (or AppController if you want to define it globally), you need to add:

* 'Minify.Minify' to $components
* 'Minify.MinifyJs' (& eventually 'Minify.MinifyCss') to $helpers

You're using the component to prepare the files in the controller and then passing a variable to the view so that the helper can output the HTML that you'll place within your template.

In your *_controller.php:

    $this->set('minified_javascript',$this->Minify->js(array(
      'js/path/lib.js','javascript/path/shiny.js
    )));

The only requirement here is that the javascript files must be within your webroot; I didn't want to presume everyone uses the js/ folder so it's open to any path within the webroot.

In your view *.ctp:

    $this->minifyJs->link('minified_javascript');

This will output something like:

    <script src="/minify/js/gz/12341234123412341234123412341234"></script>

As long as you don't have any insane routes configured, this will automatically function and browsers pulling that URL will get a minified and gzipped piece of content.

Caching is used to ensure that the script isn't continually minifying and compressing; the only thing returned from Minify::js() is the token used by the MinifyJs helper that outputs the HTML.

#Coming soon

CSS support with possible SASS? I need to research where I think it will go and if it's worth the work.


#Acknowledgements

Thanks to [rgrove](http://github.com/rgrove) for porting [jsmin-php](http://github.com/rgrove/jsmin-php/) that is used for javascript minification.

#Licence

