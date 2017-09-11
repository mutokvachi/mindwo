(function($)
{
	/**
	 * CacheScripts - a jQuery plugin that loads in background JavaScript plugins so they are stored in cache
         * Next pages loaded should load faster because will use allready cached files
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.CacheScripts = function(opts)
	{
		var options = $.extend({}, $.fn.CacheScripts.defaults, opts);
		return this.each(function()
		{
			new $.CacheScripts(this, options);
		});
	};
	
	$.fn.CacheScripts.defaults = {
	};
	
	/**
	 * CacheScripts constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.CacheScripts = function(root, opts)
	{
            $.data(root, 'CacheScripts', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
            
            this.sources = $('.dx-source', this.root);
            
            
            var arr = [];
            
            this.sources.each(function()
            {
                var src = $(this).text();
                arr.push(src);
            });
                        
            var scripts = new getScripts(
                arr,
                function(src) {
                    /* Executed each time a script has loaded */
                    console.log("Script loaded: " + src);
                },
                function () {
                    /* Executed when the entire list of scripts has been loaded */
                    console.log("All scripts loaded");
                }
            );
    
            scripts.fetch();
            
	};
})(jQuery);

function getScripts( scripts, onScript, onComplete )
{
    var scriptHandler = this;
    this.async = true;
    this.cache = true;
    this.data = null;
    this.complete = function () { scriptHandler.loaded(); };
    this.scripts = scripts;
    this.onScript = onScript;
    this.onComplete = onComplete;
    this.total = scripts.length;
    this.progress = 0;
};

getScripts.prototype.fetch = function() {    
    var src = this.scripts[ this.progress ];
    console.log('%cFetching %s','color:#ffbc2e;', src);

    $.ajax({
        crossDomain:true,
        async:this.async,
        cache:this.cache,
        type:'GET',
        url: src,
        data:this.data,
        statusCode: {
            200: this.complete
        },
        dataType: 'text'
    });
};

getScripts.prototype.loaded = function () {    
    var src_loaded = this.scripts[ this.progress ];
    this.progress++;
    if( this.progress >= this.total ) {
        if(this.onComplete) this.onComplete();
    } else {
        this.fetch();
    };
    if(this.onScript) this.onScript(src_loaded);
};

$(document).ready(function() {
    setTimeout(function(){ $(".dx-cache-container").CacheScripts(); }, 200);    
});


//# sourceMappingURL=elix_login.js.map
