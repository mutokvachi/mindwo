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
			deleteMany: '/mail/mass_delete',
			attachment: '/mail/attachment'
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
		
		this.filesTable = $('tbody.files', this.root);
		
		this.initToInput();
		this.initDateInput();
		this.initEditor();
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
				format: inboxOptions.dateFormat
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
			
			this.root.on('click', '.add-files-button', function()
			{
				self.addFileUpload();
			});
			
			this.root.on('click', '.delete-attachment-button', function(e)
			{
				e.preventDefault();
				self.deleteAttachment($(this));
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
			
			var formData = new FormData();
			formData.append('to', JSON.stringify(this.to.val()));
			formData.append('subject', this.subject.val());
			formData.append('sendTime', this.sendTime.val());
			formData.append('body', this.body.val());
			formData.append('folder', 'sent');
			
			$('input[type="file"]', this.root).each(function()
			{
				for(var i = 0; i < this.files.length; i++)
				{
					formData.append('files[]', this.files[i]);
				}
			});
			
			show_page_splash(1);
			
			$.ajax({
				type: 'post',
				url: this.id ? this.options.url.base + '/' + this.id + '/update' : this.options.url.store,
				cache: false,
				dataType: 'json',
				data: formData,
				contentType: false,
				processData: false,
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

			var formData = new FormData();
			formData.append('to', JSON.stringify(this.to.val()));
			formData.append('subject', this.subject.val());
			formData.append('sendTime', this.sendTime.val());
			formData.append('body', this.body.val());
			formData.append('folder', 'draft');
			
			$('input[type="file"]', this.root).each(function()
			{
				for(var i = 0; i < this.files.length; i++)
				{
					formData.append('files[]', this.files[i]);
				}
			});
			
			var func = function()
			{
				show_page_splash(1);
				
				$.ajax({
					type: 'post',
					url: self.id ? self.options.url.base + '/' + self.id + '/update' : self.options.url.store,
					cache: false,
					data: formData,
					dataType: 'json',
					contentType: false,
					processData: false,
					success: function(data)
					{
						hide_page_splash(1);
						
						$('.inbox-nav .folder-draft .badge', self.root).text(data.count).show();
						
						if(self.folder == 'scheduled')
						{
							var badge = $('.inbox-nav .folder-scheduled .badge', self.root);
							var count = parseInt(badge.text());
							--count;
							badge.text(count);
							if(!count)
							{
								badge.hide();
							}
						}
						
						self.id = data.id;
						self.folder = data.folder;
						
						$('.template-upload').remove();
						self.filesTable.append(data.files);
						
						toastr.success(Lang.get('mail.draft_saved'));
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus);
						console.log(jqXHR);
						hide_page_splash(1);
					}
				});
			};
			
			if(this.folder == 'scheduled')
			{
				PageMain.showConfirm(func, null,
					Lang.get('mail.confirm_action'),
					Lang.get('mail.confirm_move_to_draft'),
					''
				);
			}
			else
			{
				func();
			}
		},
		/**
		 * Discard message. Close the compose form and delete draft if it was saved before.
		 */
		discard: function()
		{
			var self = this;
			
			var func = function()
			{
				if(!self.id)
				{
					window.location = self.options.url.base;
					return;
				}
				
				var request = {
					_method: 'delete'
				};
				
				$.ajax({
					type: 'post',
					url: self.options.url.base + '/' + self.id,
					dataType: 'json',
					data: request,
					success: function(data)
					{
						hide_page_splash(1);
						window.location = self.options.url.base + '/' + self.folder;
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus);
						console.log(jqXHR);
						hide_page_splash(1);
					}
				});
			};
			
			PageMain.showConfirm(func, null,
				Lang.get('mail.confirm_action'),
				Lang.get('mail.confirm_discard'),
				''
			);
		},
		/**
		 * Delete single message.
		 */
		deleteOne: function()
		{
			var self = this;
			
			var func = function()
			{
				var request = {
					_method: 'delete'
				};
				
				$.ajax({
					type: 'post',
					url: self.options.url.base + '/' + self.id,
					dataType: 'json',
					data: request,
					success: function(data)
					{
						hide_page_splash(1);
						window.location = self.options.url.base + '/' + self.folder;
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus);
						console.log(jqXHR);
						hide_page_splash(1);
					}
				});
			};
			
			PageMain.showConfirm(func, null,
				Lang.get('mail.confirm_action'),
				Lang.get('mail.confirm_delete'),
				''
			);
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
			
			var self = this;
			
			var func = function()
			{
				var request = {
					_method: 'delete',
					ids: ids
				};
				
				$.ajax({
					type: 'post',
					url: self.options.url.deleteMany,
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
			};
			
			PageMain.showConfirm(func, null,
				Lang.get('mail.confirm_action'),
				Lang.get('mail.confirm_mass_delete'),
				''
			);
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
		},
		
		addFileUpload: function()
		{
			var html = '<tr class="template-upload">' +
			'<td class="name" width="30%">' +
			'<span><input type="file" name="files[]" multiple></span>' +
			'</td>' +
			'<td class="size" width="40%">' +
			'<span></span>' +
			'</td>' +
			'<td colspan="2"></td>' +
			'<td class="delete" width="10%" align="right">' +
			'<button class="btn default btn-sm cancel-upload-button">' +
			'<i class="fa fa-times"></i>' +
			'</button>' +
			'</td>' +
			'</tr>';
			$(html).appendTo(this.filesTable);
		},
		
		deleteAttachment: function(element)
		{
			var self = this;
			
			var func = function()
			{
				var request = {
					_method: 'delete'
				};
				
				$.ajax({
					type: 'post',
					url: element.data('url'),
					dataType: 'json',
					data: request,
					success: function(data)
					{
						hide_page_splash(1);
						element.parent().parent().remove();
						toastr.success(Lang.get('mail.attachment_deleted'));
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						console.log(textStatus);
						console.log(jqXHR);
						hide_page_splash(1);
					}
				});
			};
			
			PageMain.showConfirm(func, null,
				Lang.get('mail.confirm_action'),
				Lang.get('mail.confirm_delete_attachment'),
				''
			);
		}
	});
})(jQuery);

$(document).ready(function()
{
	$('.inbox').AppInbox();
});