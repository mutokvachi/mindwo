(function($)
{
	/**
	 * DxDocGenerator - a jQuery plugin that renders document (Word, PDF) generation UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.DxDocGenerator = function(opts)
	{
		var options = $.extend({}, $.fn.DxDocGenerator.defaults, opts);
		return this.each(function()
		{
			new $.DxDocGenerator(this, options);
		});
	};
	
	$.fn.DxDocGenerator.defaults = {
            root_url: getBaseUrl(),
            generator_url: "docgenerator/"
	};
	
	/**
	 * DxDocGenerator constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.DxDocGenerator = function(root, opts)
	{
            $.data(root, 'DxDocGenerator', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
            
            if(this.root.hasClass("is-init"))
            {
                return; // component is allready initialized
            }

            this.template_popup = $("#template_form_" + this.root.data('form-htm-id'));
            
            var generateDoc = function(template_id) {
                show_page_splash(1);
                $.getJSON( self.options.root_url + self.options.generator_url + self.root.data("list-id") + "/" + self.root.data("item-id") + "/" + template_id, function( data ) {
                    
                    if (!data.field_id) {
                        showTemplatesPopup(data.html);
                        var manage_url = self.options.root_url + "skats_" + data.templ_view_id;
                        self.template_popup.find(".dx-manage-templ-btn").attr("href", manage_url);

                        hide_page_splash(1);
                        return;
                    }

                    reload_grid(self.root.data("grid-htm-id"));
                                       
                    $("#list_item_view_form_" + self.root.data("form-htm-id") + " [dx_file_field_id=" + data['field_id'] + "]").html(data['html']);                          
                    self.template_popup.modal('hide');
                    notify_info(Lang.get('form.template.generation_ok'));

                    hide_page_splash(1);
                });
            };

            var showTemplatesPopup = function(htm) {
                self.template_popup.find('.dx-templates-list').empty().html(htm);
                initChooseHandlers();
                
                self.template_popup.modal('show');
            };

            var initChooseHandlers = function() {                
                self.template_popup.find(".dx-templ-choose-btn").click(function() {                    
                    generateDoc($(this).closest(".dx-templ-row").data('id'));
                });
            };

            this.root.click(function() {
                generateDoc(0);
            });
            
            this.root.addClass("is-init");
	};
})(jQuery);

$(document).ajaxComplete(function(event, xhr, settings)
{
	$(".dx-form-btn-word").DxDocGenerator();
});

$(document).ready(function()
{
	$(".dx-form-btn-word").DxDocGenerator();
});