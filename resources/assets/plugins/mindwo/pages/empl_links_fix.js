(function($)
{
	/**
	 * EmplLinksFix - a jQuery plugin that fix (re-init) links to other employees (manager, reporting manager)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.EmplLinksFix = function(opts)
	{
		var options = $.extend({}, $.fn.EmplLinksFix.defaults, opts);
		return this.each(function()
		{
			new $.EmplLinksFix(this, options);
		});
	};
	
	$.fn.EmplLinksFix.defaults = {
		profile_url: "/employee/profile/"
	};
	
	/**
	 * EmplLinksFix constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.EmplLinksFix = function(root, opts)
	{
		$.data(root, 'EmplLinksFix', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
                
		this.flds_managers = $('.dx-autocompleate-field[data-item-field=manager_id], .dx-autocompleate-field[data-item-field=reporting_manager_id]', this.root);
		
                this.flds_managers.each(function()
                {
                    if (!$(this).data("is-fix-init")) {
                        var fld = $(this);
                        fld.find(".dx-rel-id-add-btn").off("click"); //remove original event handler which will open CMS form
                        
                        fld.find(".dx-rel-id-add-btn").click(function() {
                            self.show_manager(fld);
                        });
                        
                        // Reset tooltip from edit to view hint
                        fld.find(".dx-rel-id-add-btn").tooltipster('destroy');
                        fld.find(".dx-rel-id-add-btn").attr("title", Lang.get('empl_profile.hint_view_profile'));
                        
                        fld.find(".dx-rel-id-add-btn").tooltipster({
                            theme: 'tooltipster-light',
                            animation: 'grow',
                            maxWidth: 300
                        });
        
                        $(this).data("is-fix-init", 1);
                    }
                });
	};
	
	/**
	 * EmplLinksFix methods
	 */
	$.extend($.EmplLinksFix.prototype, {
		/**
		 * Show calendar
		 */
		show_manager: function(fld)
		{
                    var item_id = fld.find(".dx-auto-input-id").val();
                    
                    if (item_id == 0) {
                        return;
                    }
                    show_page_splash(1);
                    window.location = this.options.profile_url + item_id;
		}
	});
})(jQuery);

$(document).ajaxComplete(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});

$(document).ready(function() {
    $(".dx-employee-profile.freeform").EmplLinksFix();
});