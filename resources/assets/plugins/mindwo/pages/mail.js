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
		this.files = [];
		
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
			
			this.sendTime.on('focusout', function(e) {
				$(this).datetimepicker('hide');
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
			
			// handle click on 'Add files' button
			this.root.on('click', '.add-files-button', function()
			{
				self.addFileUpload();
			});
			
			// handle attachment deletion
			this.root.on('click', '.delete-attachment-button', function(e)
			{
				e.preventDefault();
				self.deleteAttachment($(this));
			});
			
			// handle deletion of a file input that haven't been uploaded yet
			this.root.on('click', '.cancel-upload-button', function(e)
			{
				e.preventDefault();
				
				var name = $(this).parent().prevAll('.name').children('span').text();
				
				for(var i = 0; i < self.files.length; i++)
				{
					if(self.files[i].name == name)
					{
						self.files.splice(i, 1);
					}
				}
				
				$(this).parent().parent().remove();
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
			if(!this.validateFiles())
			{
				return;
			}
			
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
			
			for(var i = 0; i < this.files.length; i++)
			{
				formData.append('files[]', this.files[i]);
			}
			
			var func = function()
			{
				show_page_splash(1);
				
				$.ajax({
					type: 'post',
					url: self.id ? self.options.url.base + '/' + self.id + '/update' : self.options.url.store,
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
			};
			
			PageMain.showConfirm(func, null,
				Lang.get('mail.confirm_action'),
				Lang.get('mail.confirm_send'),
				''
			);
		},
		/**
		 * Save draft.
		 */
		draft: function()
		{
			if(!this.validateFiles())
			{
				return;
			}
			
			var self = this;
			
			var formData = new FormData();
			formData.append('to', JSON.stringify(this.to.val()));
			formData.append('subject', this.subject.val());
			formData.append('sendTime', this.sendTime.val());
			formData.append('body', this.body.val());
			formData.append('folder', 'draft');
			
			for(var i = 0; i < this.files.length; i++)
			{
				formData.append('files[]', this.files[i]);
			}
			
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
						self.files = [];
						
						for(var i = 0; i < data.messages.length; i++)
						{
							toastr.error(data.messages[i]);
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
		/**
		 * Append a new file input.
		 */
		addFileUpload: function()
		{
			var self = this;
			var accept = '.' + inboxOptions.allowedExtensions.join(',.');
				//+ ', ' + inboxOptions.allowedMimeTypes.join(',');
			var input = $('<input style="height: 0; position: absolute;" type="file" name="files[]" accept="' + accept + '" multiple>');
			
			input.appendTo('body');
			
			input.change(function()
			{
				for(var i = 0; i < this.files.length; i++)
				{
					var html = $('<tr class="template-upload">' +
						'<td class="name" width="30%">' +
						'<span>' +
							this.files[i].name +
						'</span>' +
						'</td>' +
						'<td class="size" width="40%">' +
						'<span>' +
							numeral(this.files[i].size).format('0.00 ib') +
						'</span>' +
						'</td>' +
						'<td colspan="2"></td>' +
						'<td class="delete" width="10%" align="right">' +
						'<button class="btn default btn-sm bg-red-flamingo cancel-upload-button">' +
						'<i class="fa fa-times"></i>' +
						'</button>' +
						'</td>' +
						'</tr>');
					
					html.appendTo(self.filesTable);
					self.files.push(this.files[i]);
				}
			});
			
			input.click();
		},
		/**
		 * Check sizes of selected files against maximum allowed values.
		 * @returns {boolean}
		 */
		validateFiles: function()
		{
			var maxFileSize = DX_CORE.max_upload_size * 1024 * 1024;
			var maxPostSize = DX_CORE.post_max_size * 1024 * 1024;
			var valid = true;
			var totalSize = 0;
			
			// check size of individual files
			for(var i = 0; i < this.files.length; i++)
			{
				var file = this.files[i];
				
				if(file.size > maxFileSize)
				{
					toastr.error(Lang.get('mail.error_file_size', {
						file: file.name,
						size: DX_CORE.max_upload_size + ' MiB'
					}));
					
					valid = false;
				}
				
				totalSize += file.size;
			}
			
			// check total size of all selected files
			if(totalSize > maxPostSize)
			{
				toastr.error(Lang.get('mail.error_post_size', {
					size: DX_CORE.post_max_size + ' MiB'
				}));
				
				valid = false;
			}
			
			return valid;
		},
		/**
		 * Delete attached file from server.
		 * @param element
		 */
		deleteAttachment: function(element)
		{
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
