/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 17.01.17, 21:53
 */

(function($)
{
	/**
	 * Wrapper function which allows to run this plugin as jQuery method.
	 *
	 * @param opts
	 * @constructor
	 */
	$.fn.AppInbox = function(opts)
	{
		var options = $.extend({}, $.fn.AppInbox.defaults, opts);
		
		return this.each(function()
		{
			new $.AppInbox(this, options);
		});
	};
	
	/**
	 * Default values of options.
	 *
	 * @type {{url: {base: string, sent: string, draft: string, scheduled: string, compose: string, store: string, upload: string, toAutocomplete: string, deleteMany: string}}}
	 */
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
			deleteMany: '/mail/mass_delete'
		}
	};
	
	/**
	 * Constructor of Mail application.
	 *
	 * @param root
	 * @param opts
	 * @constructor
	 */
	$.AppInbox = function(root, opts)
	{
		// Save object instance along with root element. Get access to it using $(root).data('AppInbox').
		$.data(root, 'AppInbox', this);
		
		this.options = opts;
		this.root = $(root);
		
		this.to = $('.inbox-input-to', this.root);
		this.subject = $('.inbox-input-subject', this.root);
		this.body = $('.inbox-wysihtml5', this.root);
		this.sendTime = $('.inbox-input-send_time', this.root);
		
		this.wrapper = $('.inbox-wrapper', this.root);
		// Message ID
		this.id = (this.wrapper.length && this.wrapper.data('id') != undefined) ? this.wrapper.data('id') : null;
		// Current folder ('sent', 'draft' or 'scheduled')
		this.folder = (this.wrapper.length && this.wrapper.data('folder') != undefined) ? this.wrapper.data('folder') : null;
		
		this.initToInput();
		this.initDateInput();
		this.initEditor();
		//this.initFileUpload();
		this.initHandlers();
	};
	
	// Methods
	$.extend($.AppInbox.prototype, {
		/**
		 * Initialize Select2 plugin for recipients list, which provides autocompletion and tagged input.
		 */
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
				// disable entering arbitrary tags
				createTag: function()
				{
					return undefined;
				}
			});
			
		},
		/**
		 * Initialize dateTimePicker plugin for input field which contains scheduled time of sending email message.
		 */
		initDateInput: function()
		{
			this.sendTime.datetimepicker({
				timepicker: true,
				format: 'Y-m-d H:i'
			});
		},
		/**
		 * Initialize wysiwyg editor.
		 */
		initEditor: function()
		{
			$('.inbox-wysihtml5').wysihtml5({
				stylesheets: ["/metronic/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"],
				image: false
			});
		},
		/**
		 * Initialize file upload widget.
		 */
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
		/**
		 * Bind event handlers to buttons.
		 */
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
			
			// discard draft button handler
			this.root.on('click', '.inbox-discard-btn', function(e)
			{
				e.preventDefault();
				self.discard();
			});
			
			// delete message button handler
			this.root.on('click', '.inbox-edit-btn', function(e)
			{
				e.preventDefault();
				self.edit(self.id);
			});
			
			// delete message button handler
			this.root.on('click', '.inbox-delete-btn', function(e)
			{
				e.preventDefault();
				self.deleteOne();
			});
			
			// view message handler
			this.root.on('click', '.folder-sent .view-message', function(e)
			{
				self.view($(this).parent().data('id'));
			});
			
			// edit message handler
			this.root.on('click', '.folder-draft .view-message', function(e)
			{
				self.edit($(this).parent().data('id'));
			});
			
			// view scheduled message handler
			this.root.on('click', '.folder-scheduled .view-message', function(e)
			{
				self.view($(this).parent().data('id'));
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
				self.deleteMany();
			});
		},
		/**
		 * Handle click on department or team name in sidebar. Open compose form and/or add corresponding recipient to
		 * the To field.
		 *
		 * @param el
		 */
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
		/**
		 * Send message.
		 */
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
					window.location = self.options.url.base + '/' + data.folder;
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		/**
		 * Save draft.
		 */
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
			
			if(this.folder == 'scheduled' && !confirm(Lang.get('mail.confirm_move_to_draft')))
			{
				return;
			}
			
			show_page_splash(1);
			
			$.ajax({
				type: 'post',
				url: this.id ? this.options.url.base + '/' + this.id + '/update' : this.options.url.store,
				cache: false,
				dataType: 'json',
				data: request,
				success: function(data)
				{
					hide_page_splash(1);
					self.id = data.id;
					
					$('.inbox-nav .folder-draft .badge', self.root).text(data.count).show();
					
					if(self.folder == 'scheduled')
					{
						var badge = $('.inbox-nav .folder-scheduled .badge', self.root);
						var count = parseInt(badge.text());
						badge.text(--count);
						if(!count)
						{
							badge.hide();
						}
						self.folder = 'draft';
					}
					
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
		/**
		 * Discard message. Close the compose form and delete draft if it was saved before.
		 */
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
					window.location = self.options.url.base + '/' + self.wrapper.data('folder');
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		/**
		 * Delete single message.
		 */
		deleteOne: function()
		{
			if(!confirm(Lang.get('mail.confirm_delete')))
			{
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
					window.location = self.options.url.base + '/' + self.wrapper.data('folder');
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		},
		/**
		 * Delete arbitrary number of messages by their ids.
		 */
		deleteMany: function()
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
				url: this.options.url.deleteMany,
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
		/**
		 * Show message (redirect).
		 * @param id
		 */
		view: function(id)
		{
			window.location = this.options.url.base + '/' + id;
		},
		/**
		 * Edit message (redirect).
		 * @param id
		 */
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