#What is this?

##Some speed improvements

I've added a page to the wiki regarding how to [take advantage of If-Modified-Since headers](https://github.com/connrs/minify/wiki/Take-advantage-of-faster-recache-refresh-responses-with-If-Modified-Since-headers) to get every last bit of performance from minified resources

This is a simple Cake 1.3 plugin that you can drop in to your plugins directory (or git submodule it baby) and automatically start combining, minifying and compressing your Javascript and CSS.

At this stage, I only have javascript functionality but I will get this sorted in no time.

To use this from within a controller (or AppController if you want to define it globally), you need to add:

* 'Minify.Minify' to $components
* 'Minify.Minify' to $helpers

You're using the component to prepare the files in the controller and then passing a variable to the view so that the helper can output the HTML that you'll place within your template.

In your *_controller.php:

    $this->set('minified_javascript',$this->Minify->js(array(
      'js/path/lib.js','javascript/path/shiny.js
    )));

The only requirement here is that the javascript files must be within your webroot; I didn't want to presume everyone uses the js/ folder so it's open to any path within the webroot.

In your view *.ctp:

    $this->Minify->link($minified_javascript);
		or $minify->link($minified_javascript);

This will output something like:

    <script src="/minify/js/gz/12341234123412341234123412341234"></script>

As long as you don't have any insane routes configured, this will automatically function and browsers pulling that URL will get a minified and gzipped piece of content.

Caching is used to ensure that the script isn't continually minifying and compressing; the only thing returned from Minify::js() is the token used by the Minify helper that outputs the HTML.

#Coming soon

CSSFast plugin does some limited compression but I hope to extend the capabilities of thos plugin itself. I'm looking into Less and OOCSS and hope to come to some sort of decision soon. Although I honestly prefer to optimise my own CSS myself.

#Acknowledgements

Thanks to [rgrove](http://github.com/rgrove) for porting [jsmin-php](http://github.com/rgrove/jsmin-php/) that is used for javascript minification.

#Licence

