/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 17.01.17, 21:53
 */

(function($)
{
	// Plugin wrapper
	$.fn.AppInbox = function(opts)
	{
		var options = $.extend({}, $.fn.AppInbox.defaults, opts);
		
		return this.each(function()
		{
			new $.AppInbox(this, options);
		});
	};
	
	// default values
	$.fn.AppInbox.defaults = {
		url: '/mail',
		composeUrl: '/mail/compose',
		uploadUrl: '/mail/upload',
		editUrl: '/mail/update',
		toAutocompleteUrl: '/mail/to_autocomplete'
	};
	
	// Constructor
	$.AppInbox = function(root, opts)
	{
		$.data(root, 'AppInbox', this);
		
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.to = $('.inbox-input-to', this.root);
		
		this.initToInput();
		this.initEditor();
		this.initFileUpload();
		this.initHandlers();
	};
	
	// Methods
	$.extend($.AppInbox.prototype, {
		initToInput: function()
		{
			this.to.select2({
				ajax: {
					url: this.options.toAutocompleteUrl,
					dataType: 'json',
					delay: 500,
					data: function(params)
					{
						return {
							term: params.term,
							page: params.page
						}
					},
					cache: true
				},
				minimumInputLength: 1,
				placeholder: Lang.get('mail.to_placeholder'),
				tags: true
			});
			
		},
		initEditor: function()
		{
			$('.inbox-wysihtml5').wysihtml5({
				"stylesheets": ["/metronic/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
			});
		},
		initFileUpload: function()
		{
			$('#fileupload').fileupload({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: this.options.uploadUrl,
				autoUpload: true
			});
			
			// Upload server status check for browsers with CORS support:
			if($.support.cors)
			{
				$.ajax({
					url: this.options.uploadUrl,
					type: 'HEAD'
				}).fail(function()
				{
					$('<span class="alert alert-error"/>')
						.text(Lang.get('mail.upload_unavailable') + ' - ' +
							new Date())
						.appendTo('#fileupload');
				});
			}
		},
		initHandlers: function()
		{
			var self = this;
			
			// send button handler
			this.root.on('click', '.inbox-send-btn', function(e)
			{
				e.preventDefault();
				self.send();
			});
			// draft button handler
			this.root.on('click', '.inbox-draft-btn', function(e)
			{
				e.preventDefault();
				self.draft();
			});
			// view message handler
			this.root.on('click', '.view-message', function(e)
			{
				self.view($(this).parent().data('messageid'));
			});
			// (un)check all handler
			this.root.on('change', '.mail-group-checkbox', function()
			{
				var set = $('.mail-checkbox', self.root);
				var checked = $(this).is(":checked");
				set.each(function()
				{
					$(this).attr("checked", checked);
				});
				//$.uniform.update(set);
			});
			
			this.root.on('click', '.inbox-shortcut', function()
			{
				self.shortcut($(this));
			});
		},
		shortcut: function(el)
		{
			if(this.to.length)
			{
				if(!this.to.find('option[value="' + el.data('id') + '"]').length)
				{
					var option = $('<option value="' + el.data('id') + '">' + $('span', el).text().trim() + '</option>');
					this.to.append(option);
				}
				
				var val = this.to.val() || [];
				val.push(el.data('id'));
				
				this.to.val(val).trigger('change');
			}
			else
			{
				window.location = this.options.composeUrl + '?to=' + el.data('id');
			}
		},
		send: function()
		{
			var to = $('.inbox-input-to');
			var subject = $('.inbox-input-subject');
			var body = $('.inbox-wysihtml5');
			
			var re = /[^\s@]+@[^\s@]+\.[^\s@]+/;
			
			/*
			 if(!to.val().length || !re.test(to.val()))
			 {
			 return;
			 }
			 */
			
			var request = {
				to: to.val(),
				subject: subject.val(),
				body: body.val(),
				folder: 'sent'
			};
			
			console.log(request);
			return;
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: url,
				cache: false,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					$('.inbox-nav .folder-sent').click();
					hide_page_splash(1);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		draft: function()
		{
			var to = $('.inbox-input-to');
			var subject = $('.inbox-input-subject');
			var body = $('.inbox-wysihtml5');
			
			var re = /[^\s@]+@[^\s@]+\.[^\s@]+/;
			
			if(!to.val().length || !re.test(to.val()))
			{
				return;
			}
			
			var request = {
				to: to.val(),
				subject: subject.val(),
				body: body.val(),
				folder: 'draft'
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: url,
				cache: false,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		discard: function()
		{
			
		},
		view: function(id)
		{
			window.location = this.options.url + '/' + id;
		}
	});
})(jQuery);

$(document).ready(function()
{
	$('.inbox').AppInbox();
});