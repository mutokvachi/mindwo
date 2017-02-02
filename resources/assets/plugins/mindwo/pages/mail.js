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
		url: {
			base: '/mail',
			sent: '/mail/sent',
			draft: '/mail/draft',
			scheduled: '/mail/scheduled',
			compose: '/mail/compose',
			store: '/mail/store',
			upload: '/mail/upload',
			toAutocomplete: '/mail/to_autocomplete',
			massDelete: '/mail/mass_delete'
		}
	};
	
	// Constructor
	$.AppInbox = function(root, opts)
	{
		$.data(root, 'AppInbox', this);
		
		this.options = opts;
		this.root = $(root);
		
		this.to = $('.inbox-input-to', this.root);
		this.subject = $('.inbox-input-subject', this.root);
		this.body = $('.inbox-wysihtml5', this.root);
		this.sendTime = $('.inbox-input-send_time', this.root);
		
		this.compose = $('.inbox-compose', this.root);
		this.id = (this.compose.length && this.compose.data('id') != undefined) ? this.compose.data('id') : null;
		
		this.initToInput();
		this.initDateInput();
		this.initEditor();
		//this.initFileUpload();
		this.initHandlers();
	};
	
	// Methods
	$.extend($.AppInbox.prototype, {
		initToInput: function()
		{
			this.to.select2({
				ajax: {
					url: this.options.url.toAutocomplete,
					dataType: 'json',
					delay: 700,
					data: function(params)
					{
						return {
							term: params.term,
							page: params.page
						}
					},
					cache: true
				},
				minimumInputLength: 2,
				placeholder: Lang.get('mail.to_placeholder'),
				tags: true,
				createTag: function()
				{
					return undefined;
				}
			});
			
		},
		initDateInput: function()
		{
			this.sendTime.datetimepicker({
				timepicker: true,
				format: 'Y-m-d H:i'
			});
		},
		initEditor: function()
		{
			$('.inbox-wysihtml5').wysihtml5({
				stylesheets: ["/metronic/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"],
				image: false
			});
		},
		initFileUpload: function()
		{
			$('#fileupload').fileupload({
				// Uncomment the following to send cross-domain cookies:
				//xhrFields: {withCredentials: true},
				url: this.options.url.upload,
				autoUpload: true
			});
			
			// Upload server status check for browsers with CORS support:
			if($.support.cors)
			{
				$.ajax({
					url: this.options.url.upload,
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
			
			// draft button handler
			this.root.on('click', '.inbox-discard-btn', function(e)
			{
				e.preventDefault();
				self.discard();
			});
			
			// view message handler
			this.root.on('click', '.folder-sent .view-message', function(e)
			{
				self.view($(this).parent().data('messageid'));
			});
			
			// edit message handler
			this.root.on('click', '.folder-draft .view-message', function(e)
			{
				self.edit($(this).parent().data('messageid'));
			});
			
			// (un)check all handler
			this.root.on('change', '.mail-group-checkbox', function()
			{
				var set = $('.mail-checkbox', self.root);
				var checked = $(this).is(":checked");
				set.each(function()
				{
					$(this).prop("checked", checked);
				});
			});
			
			// handle clicks on shortcuts in sidebar
			this.root.on('click', '.inbox-shortcut', function()
			{
				self.shortcut($(this));
			});
			
			// mass delete handler
			this.root.on('click', '.input-actions .inbox-delete', function()
			{
				self.massDelete();
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
				window.location = this.options.url.compose + '?to=' + el.data('id');
			}
		},
		send: function()
		{
			var self = this;
			
			if(!this.to.val() || !this.to.val().length)
			{
				toastr.error(Lang.get('mail.validate_to'));
				return;
			}
			
			if(!this.subject.val().length)
			{
				toastr.error(Lang.get('mail.validate_subject'));
				return;
			}
			
			if(!this.body.val().length)
			{
				toastr.error(Lang.get('mail.validate_body'));
				return;
			}
			
			var request = {
				to: this.to.val(),
				subject: this.subject.val(),
				sendTime: this.sendTime.val(),
				body: this.body.val(),
				folder: 'sent'
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: this.id ? this.options.url.base + '/' + this.id + '/update' : this.options.url.store,
				cache: false,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					window.location = self.options.url.sent;
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
			var self = this;
			
			var request = {
				to: this.to.val(),
				subject: this.subject.val(),
				sendTime: this.sendTime.val(),
				body: this.body.val(),
				folder: 'draft'
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: this.id ? this.options.url.base + '/' + this.id + '/update' : this.options.url.store,
				cache: false,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					self.id = data.id;
					toastr.success(Lang.get('mail.draft_saved'));
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
			if(!confirm(Lang.get('mail.confirm_discard')))
			{
				return;
			}
			
			if(!this.id)
			{
				window.location = this.options.url.base;
				return;
			}
			
			var self = this;
			
			var request = {
				_method: 'delete'
			};
			
			$.ajax({
				type: 'post',
				url: this.options.url.base + '/' + this.id,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					window.location = self.options.url.base + '/' + self.compose.data('folder');
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		massDelete: function()
		{
			var ids = [];
			
			$('input.mail-checkbox:checked', this.root).each(function()
			{
				ids.push($(this).val());
			});
			
			if(!ids.length)
			{
				toastr.error(Lang.get('mail.mass_delete_check'));
				return;
			}
			
			if(!confirm(Lang.get('mail.confirm_mass_delete')))
			{
				return;
			}
			
			var self = this;
			
			var request = {
				_method: 'delete',
				ids: ids
			};
			
			$.ajax({
				type: 'post',
				url: this.options.url.massDelete,
				dataType: 'json',
				cache: false,
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					window.location.reload();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		view: function(id)
		{
			window.location = this.options.url.base + '/' + id;
		},
		edit: function(id)
		{
			window.location = this.options.url.base + '/' + id + '/edit';
		}
	});
})(jQuery);

$(document).ready(function()
{
	$('.inbox').AppInbox();
});