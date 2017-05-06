(function($)
{
	/**
	 * FreeForm - a jQuery plugin for working with arbitrary forms
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.FreeForm = function(opts)
	{
		var options = $.extend({}, $.fn.FreeForm.defaults, opts);
		
		return this.each(function()
		{
			new $.FreeForm(this, options);
		});
	};
	
	$.fn.FreeForm.defaults = {
		editBtnSelector: '.dx-edit-general',
		saveBtnSelector: '.dx-save-general',
		cancelBtnSelector: '.dx-cancel-general',
		names: []
	};
	
	/**
	 * FreeForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.FreeForm = function(root, opts)
	{
		// store object instance along with a root DOM element
		$.data(root, 'FreeForm', this);
		
		var self = this;
		this.root = $(root);
		this.options = opts;
		this.fields = $('[data-name]', this.root).filter(function()
		{
			if(self.options.names.length == 0 || self.options.names.indexOf($(this).data('name')) != -1)
				return true;
		});
		this.originalData = {};
		this.editButton = $(this.options.editBtnSelector, this.root);
		this.saveButton = $(this.options.saveBtnSelector, this.root);
		this.cancelButton = $(this.options.cancelBtnSelector, this.root);
		
		// Bind callbacks to buttons
		this.editButton.click(function() { self.edit(); });
		this.saveButton.click(function() { self.save(); });
		this.cancelButton.click(function() { self.cancel(); });
	};
	
	/**
	 * FreeForm methods
	 */
	$.extend($.FreeForm.prototype, {
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				model: this.root.data('model'),
				item_id: this.root.data('item_id'),
				list_id: this.root.data('list_id'),
				fields: []
			};
			
			// collect names of input fields marked with data-name attribute
			this.fields.each(function()
			{
				self.originalData[$(this).data('name')] = $(this).html();
				request.fields.push({
					name: $(this).data('name'),
					display: ($(this).data('display') ? $(this).data('display') : 'raw')
				});
			});
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + request.item_id + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.hide();
					self.saveButton.show();
					self.cancelButton.show();
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < data.fields.length; i++)
					{
						var name = data.fields[i].name;
						var input = data.fields[i].input;
						var elem = $('[data-name="' + name + '"]', self.root);
						if(elem.length)
							elem.html(input);
					}
					hide_page_splash(1);                                        
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Submit input field values to the server
		 */
		save: function()
		{
			var self = this;
			var fieldMetadata = {};
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('model', this.root.data('model'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			
			// collect metadata of input fields
			this.fields.each(function()
			{
				fieldMetadata[$(this).data('name')] = {
					display: $(this).data('display') ? $(this).data('display') : 'raw'
				};
			});
			
			formData.append('field_metadata', JSON.stringify(fieldMetadata));
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'freeform/' + this.root.data('item_id') + '?_method=PUT',
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.show();
					self.saveButton.hide();
					self.cancelButton.hide();
					
					// replace input fields with html data from server response
					for(var i = 0; i < data.fields.length; i++)
					{
						var name = data.fields[i].name;
						var html = data.fields[i].html;
						var elem = $('[data-name="' + name + '"]', self.root);
						if(elem.length)
							elem.html(html);
					}
					hide_page_splash(1);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			var self = this;
			this.editButton.show();
			this.saveButton.hide();
			this.cancelButton.hide();
			this.fields.each(function()
			{
				$(this).html(self.originalData[$(this).data('name')]);
			});
		}
	});
})(jQuery);
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:33
 */
(function($)
{
	/**
	 * InlineForm - a jQuery plugin that provides a way to work with AJAX form embedded into a page
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.InlineForm = function(opts)
	{
		var options = $.extend({}, $.fn.InlineForm.defaults, opts);
		
		return this.each(function()
		{
			new $.InlineForm(this, options);
		});
	};
	
	$.fn.InlineForm.defaults = {
		beforeSave: null,
		afterSave: null,
		empl_search_page_url: "/search"
	};
	
	/**
	 * InlineForm constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.InlineForm = function(root, opts)
	{
		$.data(root, 'InlineForm', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.tabs = $('.tab-pane', this.root).not(".dx-grid-tab-pane");
		
		// detached fields, that are custom placed outside of main form (e.g. avatar in employee profile)
		this.fields = $('[data-name]', this.root);
		this.fieldNames = [];
		
		this.fields.each(function()
		{
			self.fieldNames.push($(this).data('name'));
		});
		
		this.originalTabs = {};
		this.originalFields = {};
		this.editButton = $('.dx-edit-profile', this.root);
		this.saveButton = $('.dx-save-profile', this.root);
		this.cancelButton = $('.dx-cancel-profile', this.root);
		this.deleteButton = $('.dx-delete-profile', this.root);
		this.requests = {};
		this.onRequestSuccess = [];
		this.onRequestFailed = [];
		
		// Bind callbacks to buttons
		this.editButton.click(function()
		{
			self.edit();
		});
		this.saveButton.click(function()
		{
			self.save();
		});
		this.cancelButton.click(function()
		{
			self.cancel();
		});
		this.deleteButton.click(function()
		{
			self.destroy();
		});
	};
	
	/**
	 * InlineForm methods
	 */
	$.extend($.InlineForm.prototype, {
		/**
		 * Resets and initializes all async request processing parameters
		 * @param {integer} total Total count of processes which will be processed asynchronously
		 */
		initRequest: function(total)
		{
			this.requests = {
				total: total,
				succeeded: 0,
				failed: 0
			};
			
			this.onRequestSuccess = [];
			this.onRequestFailed = [];
		},
		/**
		 * Saves completed request status. If all request are finished, then execute success commands
		 * @param {boolean} is_success Parmeter if process was successful
		 */
		setRequestStatus: function(is_success)
		{
			if(is_success)
			{
				this.requests.succeeded++;
			}
			else
			{
				this.requests.failed++;
			}
			
			if(this.requests.total === (this.requests.succeeded + this.requests.failed))
			{
				if(this.requests.failed === 0)
				{
					for(var i = 0; i < this.onRequestSuccess.length; i++)
					{
						this.onRequestSuccess[i].func(this.onRequestSuccess[i].args);
					}
				}
				else
				{
					for(var i = 0; i < this.onRequestFailed.length; i++)
					{
						this.onRequestFailed[i].func(this.onRequestFailed[i].args);
					}
				}
				
				hide_page_splash(1);
			}
		},
		
		storeOriginalData: function()
		{
			var self = this;
			
			this.fields = $('[data-name]', this.root);
			
			this.tabs.each(function()
			{
				self.originalTabs[$(this).data('tabTitle')] = $(this).html();
			});
			
			this.fields.each(function()
			{
				self.originalFields[$(this).data('name')] = $(this).html();
			});
			
		},
		
		/**
		 * Replace HTML with form input fields
		 */
		edit: function()
		{
			var self = this;
			
			// a structure for JSON request
			var request = {
				list_id: this.root.data('list_id'),
				tab_list: [],
				field_list: this.fieldNames
			};
			
			this.storeOriginalData();
			
			show_page_splash(1);
			
			// perform a request to the server
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '/edit',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					self.editButton.hide();
					
					var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
					
					// replace original html content of marked elements with input fields
					for(var i = 0; i < tabs.length; i++)
					{
						var tab = $(tabs[i]);
						var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
						if(elem.length)
							elem.html(tab.html());
					}
					
					for(var name in data.fields)
					{
						$('[data-name="' + name + '"]').html(data.fields[name]);
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						window.DxEmpPersDocs.toggleDisable(false);
					}
					
					hide_page_splash(1);
					
					$('.dx-stick-footer').show();
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
		 * Submit input field values to the server
		 */
		save: function(event)
		{
			var self = this;
                        
                        // Calls encryption function which encryptes data and on callback it executes again save function
                        if(!event || !event.encryptionFinished || event.encryptionFinished == false){
                            var cryptoFields = $('input.dx-crypto-field,textarea.dx-crypto-field,input.dx-crypto-field-file', this.root);

                            if(!event || event == undefined){
                                event = {};
                            }

                            window.DxCrypto.encryptFields(cryptoFields, event, function(event){
                                self.save(event);
                            });

                            return;
                        }
                        
			var formData = process_data_fields(this.root.attr('id'));
			formData.append('item_id', this.root.data('item_id'));
			formData.append('list_id', this.root.data('list_id'));
			formData.append('edit_form_id', this.root.data('form_id'));
			formData.append('redirect_url', this.root.data('redirect_url'));
			formData.append('field_list', JSON.stringify(this.fieldNames));
			
			var url = DX_CORE.site_url + 'inlineform';
			if(this.root.data('mode') != 'create')
			{
				url += '/' + this.root.data('item_id') + '?_method=PUT';
			}
			
			show_page_splash(1);
			
			// submit a request
			$.ajax({
				type: 'POST',
				url: url,
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					if(self.root.data('mode') == 'create')
					{
						if(self.root.data('has_users_documents_access') == 1)
						{
							window.DxEmpPersDocs.userId = data.item_id;
							
							// Custom tab
							window.DxEmpPersDocs.onClickSaveDocs(function()
							{
								hide_page_splash(1);
								$('.dx-stick-footer').hide();
								window.location = data.redirect;
							});
						}
						else
						{
							hide_page_splash(1);
							$('.dx-stick-footer').hide();
							window.location = data.redirect;
						}
						
						return;
					}
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						// Custom tab
						window.DxEmpPersDocs.onClickSaveDocs(function()
						{
							window.DxEmpPersDocs.toggleDisable(true);
							
							self.editButton.show();
							
							var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
							
							// replace original html content of marked elements with input fields
							for(var i = 0; i < tabs.length; i++)
							{
								var tab = $(tabs[i]);
								var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
								if(elem.length)
									elem.html(tab.html());
							}
							
							if(self.options.afterSave)
							{
								self.options.afterSave();
							}
							
							hide_page_splash(1);
							$('.dx-stick-footer').hide();
							
						});
					}
					else
					{
						self.editButton.show();
						
						var tabs = $($.parseHTML('<div>' + data.tabs + '</div>')).find('.tab-pane').not(".dx-grid-tab-pane");
						
						// replace original html content of marked elements with input fields
						for(var i = 0; i < tabs.length; i++)
						{
							var tab = $(tabs[i]);
							var elem = $('[data-tab-title="' + tab.data('tabTitle') + '"]', self.root);
							if(elem.length)
								elem.html(tab.html());
						}
						
						for(var name in data.fields)
						{
							$('[data-name="' + name + '"]').html(data.fields[name]);
						}
						
						if(self.options.afterSave)
						{
							self.options.afterSave();
						}
						
						hide_page_splash(1);
						$('.dx-stick-footer').hide();
					}
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(jqXHR);
					console.log(errorThrown);
				}
			});
                        
                        /*var cryptoFields = $('.dx-crypto-field', this.root);
                        window.DxCrypto.decryptFields(cryptoFields); */       
		},
		
		/**
		 * Remove input fields and display original HTML
		 */
		cancel: function()
		{
			if(this.root.data('mode') == 'create')
			{
				show_page_splash(1);
				window.location = this.options.empl_search_page_url;
				return;
			}
			var self = this;
			
			show_page_splash(1);
			
			$.ajax({
				type: 'GET',
				url: DX_CORE.site_url + 'form/unlock_item/' + $('.dx-employee-profile').data('list_id') + '/' + $('.dx-employee-profile').data('item_id'),
				dataType: 'json',
				success: function(data)
				{
					// item unlocked
					self.editButton.show();
					
					for(var k in self.originalTabs)
					{
						self.tabs.filter('[data-tab-title="' + k + '"]').html(self.originalTabs[k]);
					}
					
					for(var name in self.originalFields)
					{
						self.fields.filter('[data-name="' + name + '"]').html(self.originalFields[name]);
					}
					
					$('.dx-stick-footer').hide();
					
					if(self.root.data('has_users_documents_access') == 1)
					{
						window.DxEmpPersDocs.cancelEditMode();
					}
					hide_page_splash(1);
				}
			});
		},
		
		/**
		 * Delete an item from storage.
		 */
		destroy: function()
		{
			if(!confirm(Lang.get('frame.confirm_delete')))
				return;
			
			var request = {
				edit_form_id: this.root.data('form_id'),
				item_id: this.root.data('item_id')
			};
			
			show_page_splash(1);
			
			$.ajax({
				type: 'POST',
				url: DX_CORE.site_url + 'inlineform/' + this.root.data('item_id') + '?_method=DELETE',
				dataType: 'json',
				data: request,
				success: function(data)
				{
					if(typeof data.success != "undefined" && data.success == 0)
					{
						notify_err(data.error);
						hide_page_splash(1);
						return;
					}
					
					window.history.back();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					console.log(textStatus);
					console.log(jqXHR);
					hide_page_splash(1);
				}
			});
		}
	});
})(jQuery);

/**
 * Contains logic for viewing and editing employee's documents
 * @type Window.DxEmpPersDocs|window.DxEmpPersDocs 
 */
window.DxEmpPersDocs = window.DxEmpPersDocs || {
    /**
     * Row counter, used to identify rows
     */
    rowCount: 0,
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    /**
     * Locale used in system. This format is used to initializes date picker
     */
    locale: 'en',
    /**
     * Registers ID for table where documents are saved
     */
    empDocListId: 0,
    /**
     * Registers field ID where documents are saved
     */
    empDocFldId: 0,
    /**
     * Parameter if component is initialized
     */
    isInit: false,
    /**
     * Clone of view which contains view state with data saved in database.
     * If edit mode is canceled, then this view is replaced with edit view and all made changes are lost
     */
    viewClone: '',
    /**
     * Callback function which is called after successful components initialization
     * @param {type} data Data which is sent to callback function
     */
    callbackOnInitiSuccess: function (data) {},
    /**
     * Callback function which is called after successful data save
     * @param {type} data Data which is sent to callback function
     */
    callbackOnSaveSuccess: function (data) {},
    /**
     * Callback function which is called after process is exited with error
     * @param {type} data Data which is sent to callback function
     */
    callbackOnError: function (data) {},
    /**
     * Initializes component
     */
    init: function (callbackOnInitiSuccess) {
        if (callbackOnInitiSuccess) {
            window.DxEmpPersDocs.callbackOnInitiSuccess = callbackOnInitiSuccess;
        } else {
            window.DxEmpPersDocs.callbackOnInitiSuccess = function () {};
        }

        window.DxEmpPersDocs.userId = ($('#dx-emp-pers-docs-panel').attr('data-user-id') == '' ? 0 : $('#dx-emp-pers-docs-panel').attr('data-user-id'));
        window.DxEmpPersDocs.dateFormat = $('#dx-emp-pers-docs-panel').attr('data-date-format');
        window.DxEmpPersDocs.locale = $('#dx-emp-pers-docs-panel').attr('data-locale');
        window.DxEmpPersDocs.empDocListId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-list-id');
        window.DxEmpPersDocs.empDocFldId = $('#dx-emp-pers-docs-panel').attr('data-emp-docs-fld-id');
        $("#dx-emp-pers-docs-country").change(window.DxEmpPersDocs.onChangeCountry);
        window.DxEmpPersDocs.loadEmployeeData();
    },
    /**
     * Enter edit mode by saving view state in memory. 
     * It is needed to revert changes if edit mode is canceled
     */
    enterEditMode: function () {
        window.DxEmpPersDocs.viewClone = $('#dx-emp-pers-docs-panel').clone(true, true);
    },
    /**
     * Cancels edit mode by loading previous view state
     */
    cancelEditMode: function () {
        $('#dx-emp-pers-docs-panel').replaceWith(window.DxEmpPersDocs.viewClone);
        window.DxEmpPersDocs.viewClone = null;
        window.DxEmpPersDocs.toggleDisable(true);
    },
    /**
     * Loads employee document data from server
     */
    loadEmployeeData: function () {
        $.ajax({
            url: '/employee/personal_docs/get/employee_docs/' + window.DxEmpPersDocs.userId,
            type: "get",
            success: window.DxEmpPersDocs.onSuccessLoadEmployeeData,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Evenet ahndler on successful employee data retrieval
     * @param {array} data Employee document data which ir retrieved
     */
    onSuccessLoadEmployeeData: function (data) {
        if (data != '') {
            var data_rows = JSON.parse(data);
            // Prepares dropdown list options
            for (var i = 0; i < data_rows.length; i++) {
                window.DxEmpPersDocs.createNewDocRow(false, data_rows[i]);
            }
        }

        $("#dx-emp-pers-docs-country").trigger('change');
    },
    /**
     * Draws document row
     * @param {boolean} is_new Argument if row is new and doesnt contain any data
     * @param {array} data Data which will be used to draw row. Can contains saved data or if new then document type
     */
    createNewDocRow: function (is_new, data) {
        // Gets template for row and converts it as jquery object
        var new_row_html = $($('#dx-emp-pers-docs-new-row').html());
        if (is_new) {
            new_row_html = window.DxEmpPersDocs.setDocTypeValue(new_row_html, data);
        } else {
            new_row_html = window.DxEmpPersDocs.setValues(new_row_html, data);
        }

        // Append row to table
        if (is_new) {
            $('#dx-emp-pers-docs-table').append(new_row_html);
        } else {
            $('#dx-emp-pers-docs-table-history').append(new_row_html);
        }

        // Bind all rquired events for row elements
        window.DxEmpPersDocs.bindDocRowEvenets(new_row_html);
        // Increase row counter
        window.DxEmpPersDocs.rowCount++;
    },
    /**
     * Initiates date picker control in row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {string} value Date which will be set in date picker
     * @returns {DOMElement} Edited row with initialized date picker
     */
    initValidToDatePicker: function (new_row_html, value) {
        var picker = new_row_html.find('.dx-emp-pers-docs-validto-input');
        picker.attr('id', 'dx-emp-pers-docs-validto-input-' + window.DxEmpPersDocs.rowCount);
        picker.val(value);
        picker.datetimepicker({
            lang: window.DxEmpPersDocs.locale,
            format: window.DxEmpPersDocs.dateFormat,
            timepicker: 0,
            dayOfWeekStart: 1,
            closeOnDateSelect: true
        });
        new_row_html.find('.dx-emp-pers-docs-validto-input-calc').click({picker_num: window.DxEmpPersDocs.rowCount}, function (e) {
            jQuery('#dx-emp-pers-docs-validto-input-' + e.data.picker_num).datetimepicker('show');
        });
        return new_row_html;
    },
    /**
     * Sets data for new document row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {array} data_row Data array for input values
     * @returns {DOMElement} Row containing values
     */
    setDocTypeValue: function (new_row_html, data_row) {
        // Prepare "valid to" date picker            
        new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, '');
        new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.name);
        return new_row_html;
    },
    /**
     * Sets data for already saved document row
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {array} data_row Data array for input values
     * @returns {DOMElement} Row containing values
     */
    setValues: function (new_row_html, data_row) {
        new_row_html = window.DxEmpPersDocs.initValidToDatePicker(new_row_html, data_row.valid_to);
        new_row_html.attr('id', 'dx-emp-pers-docs-row-' + data_row.doc_id);
        new_row_html.find('.dx-emp-pers-docs-id-input').val(data_row.id);
        new_row_html.find('.dx-emp-pers-docs-type-input').val(data_row.doc_id);
        new_row_html.find('.dx-emp-pers-docs-type-label').html(data_row.personal_document.name);
        new_row_html.find('.dx-emp-pers-docs-docnr-input').val(data_row.doc_nr);
        new_row_html.find('.dx-emp-pers-docs-publisher-input').val(data_row.publisher);
        window.DxEmpPersDocs.setFileValue(new_row_html, data_row.id, data_row.file_name);

        return new_row_html;
    },
    /**
     * Sets saved document link in file input box
     * @param {DOMElement} new_row_html Row's DOM elemenet
     * @param {integer} row_id ID for document row in database
     * @param {string} file_name Saved name for the file
     */
    setFileValue: function (new_row_html, row_id, file_name) {
        if (file_name && file_name != null) {
            var file_link = "<a href='JavaScript: download_file(" + row_id + " , " + window.DxEmpPersDocs.empDocListId + ", " + window.DxEmpPersDocs.empDocFldId + ");'>" + file_name + "</a>";
            new_row_html.find('.dx-emp-pers-docs-file-input-download').html(file_link);
            new_row_html.find('.dx-emp-pers-docs-file-input-isset').val(1);
        }
    },
    /**
     * Clears documents row data
     * @param {object} e Event arguments which contains event caller
     */
    clearDocRow: function (e) {
        var row = $(e.target).parents('.dx-emp-pers-docs-row');
        row.find('.dx-emp-pers-docs-id-input').val(0);
        row.find('.dx-emp-pers-docs-docnr-input').val('');
        row.find('.dx-emp-pers-docs-validto-input').val('');
        row.find('.dx-emp-pers-docs-publisher-input').val('');
        row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
    },
    /**
     * Gets data from inputs for data saving
     * @returns {FormData} Data retrieved from input fields
     */
    getDataForSave: function () {
        var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
        var data = {
            user_id: window.DxEmpPersDocs.userId,
            rows: []
        };
        var formData = new FormData();
        for (var i = 0; i < rows.length; i++) {
            var row = $(rows[i]);
            var row_data = {};
            row_data.id = row.find('.dx-emp-pers-docs-id-input').val();
            row_data.document_type = row.find('.dx-emp-pers-docs-type-input').val();
            row_data.publisher = row.find('.dx-emp-pers-docs-publisher-input').val();
            row_data.valid_to = row.find('.dx-emp-pers-docs-validto-input').val();
            row_data.doc_nr = row.find('.dx-emp-pers-docs-docnr-input').val();
            row_data.file_remove = $.trim(row.find('.dx-emp-pers-docs-file-input-download').html()) === '';
            var file = row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0];
            formData.append('file' + i, file);
            data.rows.push(row_data);
        }


        formData.append('doc_country_id', $('#dx-emp-pers-docs-country').val());
        formData.append('data', JSON.stringify(data));
        return formData;
    },
    /**
     * Binds click event for clear button
     * @param {DOMElement} new_row_html Row's DOM elemenet
     */
    bindDocRowEvenets: function (new_row_html) {
        new_row_html.find('.dx-emp-pers-docs-clear-btn').click(window.DxEmpPersDocs.clearDocRow);
    },
    /**
     * Event when changing country from dropdown which requests document types associated with selected country
     * @param {object} e Event arguments which contains event caller
     */
    onChangeCountry: function (e) {
        var country_id = $(e.target).val();
        $.ajax({
            url: DX_CORE.site_url + 'employee/personal_docs/get/docs_by_country/' + country_id,
            type: "get",
            success: window.DxEmpPersDocs.onSuccessChangeCountry,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Event on successful document type retrieval when changing country
     * @param {array} data Document types associated with country
     */
    onSuccessChangeCountry: function (data) {
        var docs = JSON.parse(data);
        window.DxEmpPersDocs.drawRows(docs);
        window.DxEmpPersDocs.finishInit();
    },
    /**
     * Draws rows when country is changed
     * @param {array} docs Document types associated with selected country
     */
    drawRows: function (docs) {
        // Moves all existing rows to hidden history div
        $('#dx-emp-pers-docs-table').contents().appendTo('#dx-emp-pers-docs-table-history');
        // Iterates through all the document types
        for (var d = 0; d < docs.length; d++) {
            var doc = docs[d];
            var existing_row = $('#dx-emp-pers-docs-row-' + doc.id);
            // Check if row exist in history div
            if (existing_row.length > 0) {
                // Move existing document type row into visible view
                existing_row.appendTo('#dx-emp-pers-docs-table');
            } else {
                // Creates new row if it doesn't exist for document type
                window.DxEmpPersDocs.createNewDocRow(true, doc);
            }
        }
        // Initiates tooltips because they are not initiated because rows are created dynamicaly
        $('#dx-emp-pers-docs-table').find('[data-tooltip-title]').each(function (i, obj) {
            $(obj).attr('title', $(obj).data('tooltip-title'));
            $(obj).tooltip();
        });
    },
    /**
     * Finishes initialization
     */
    finishInit: function () {
        if (!window.DxEmpPersDocs.isInit) {
            window.DxEmpPersDocs.isInit = true;
            if (window.DxEmpPersDocs.userId == 0) {
                window.DxEmpPersDocs.toggleDisable(false);
            } else {
                window.DxEmpPersDocs.toggleDisable(true);
            }
            window.DxEmpPersDocs.callbackOnInitiSuccess();
        }
    },
    /**
     * Saves data
     * @param {function} callbackOnSaveSuccess Callback function for successful saving
     * @param {function} callbackOnError Callback function when error happens on data save
     */
    onClickSaveDocs: function (callbackOnSaveSuccess, callbackOnError) {
        if (callbackOnSaveSuccess) {
            window.DxEmpPersDocs.callbackOnSaveSuccess = callbackOnSaveSuccess;
        } else {
            window.DxEmpPersDocs.callbackOnSaveSuccess = function () {};
        }
        if (callbackOnError) {
            window.DxEmpPersDocs.callbackOnError = callbackOnError;
        } else {
            window.DxEmpPersDocs.callbackOnError = function () {};
        }

        var form_data = window.DxEmpPersDocs.getDataForSave();
        $.ajax({
            url: DX_CORE.site_url + 'employee/personal_docs/save',
            data: form_data,
            type: "post",
            processData: false,
            dataType: "json",
            contentType: false,
            success: window.DxEmpPersDocs.onSuccessSave,
            error: window.DxEmpPersDocs.onAjaxError
        });
    },
    /**
     * Event on successful data save
     * @param {array} data_rows Data returned about saved document rows
     */
    onSuccessSave: function (data_rows) {
        // Set id for rows and update file input control value
        for (var i = 0; i < data_rows.length; i++) {
            var data_row = data_rows[i];
            var row = $('#dx-emp-pers-docs-row-' + data_row.doc_id);

            if (row.length > 0) {
                row.find('.dx-emp-pers-docs-id-input').val(data_row.id);
                row.find('.dx-emp-pers-docs-file-input-remove-btn').trigger('click');
                window.DxEmpPersDocs.setFileValue(row, data_row.id, data_row.file_name);
            }
        }

        $('#dx-emp-pers-docs-table-history').empty();
        window.DxEmpPersDocs.callbackOnSaveSuccess();
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     */
    onAjaxError: function (data) {
        window.DxEmpPersDocs.finishInit();
        window.DxEmpPersDocs.callbackOnError();
    },
    /**
     * Swicthed edit and view modes
     * @param {boolean} is_disabled If true then view mode is set else edit mode is set
     */
    toggleDisable: function (is_disabled) {
        if (!is_disabled) {
            window.DxEmpPersDocs.enterEditMode();
        }

        var rows = $('#dx-emp-pers-docs-table .dx-emp-pers-docs-row');
        for (var i = 0; i < rows.length; i++) {
            var row = $(rows[i]);
            row.find('.dx-emp-pers-docs-publisher-input').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-validto-input').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-validto-input-calc').prop('disabled', is_disabled);
            row.find('.dx-emp-pers-docs-docnr-input').prop('disabled', is_disabled);
            if (is_disabled) {
                row.find('.dx-emp-pers-docs-file-input-set-btn').hide();
                if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                    row.find('.dx-emp-pers-docs-file-input-remove-btn').hide();
                }

                row.find('.dx-emp-pers-docs-clear-btn').hide();
            } else {
                row.find('.dx-emp-pers-docs-validto-input').datetimepicker('destroy');
                row.find('.dx-emp-pers-docs-validto-input').datetimepicker({
                    lang: window.DxEmpPersDocs.locale,
                    format: window.DxEmpPersDocs.dateFormat,
                    timepicker: 0,
                    dayOfWeekStart: 1,
                    closeOnDateSelect: true
                });

                row.find('.dx-emp-pers-docs-file-input-set-btn').show();
                if (row.find('.dx-emp-pers-docs-file-input-file').prop("files")[0]) {
                    row.find('.dx-emp-pers-docs-file-input-remove-btn').show();
                }

                row.find('.dx-emp-pers-docs-clear-btn').show();
            }
        }

        $('#dx-emp-pers-docs-country').prop('disabled', is_disabled);
    }
};
/**
 * Contains logic for viewing and editing employee's notes
 * @type Window.DxEmpNotes|window.DxEmpNotes 
 */
window.DxEmpNotes = window.DxEmpNotes || {
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Parameter if control is loaded
     */
    isLoaded: false,
    /**
     * Parameter if note is sending to server
     */
    isSending: false,
    /**
     * Default color for chat form background
     */
    chatFormColorDefault: 'white',
    /**
     * Initializes component
     */
    init: function (userId) {
        window.DxEmpNotes.userId = userId;

    },
    /**
     * Loads view
     * @returns {undefined}
     */
    loadView: function () {
        if (window.DxEmpNotes.isLoaded) {
            return;
        }

        show_page_splash(1);

        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/get/view/' + window.DxEmpNotes.userId,
            type: "get",
            success: window.DxEmpNotes.onLoadViewSuccess,
            error: function (data) {
                hide_page_splash(1);
            }
        });
    },
    /**
     * Evnet handler when view is successfully loaded
     * @returns {string} View's HTML
     */
    onLoadViewSuccess: function (data) {
        $('#dx-tab_notes').html(data);

        window.DxEmpNotes.chatFormColorDefault = $('.dx-emp-notes-chat-form').css("background-color");

        $('.dx-emp-notes-btn').click(window.DxEmpNotes.onNoteEnter);
        $('.dx-emp-notes-input-text').keyup(function (e) {
            if (e.keyCode == 13) {
                window.DxEmpNotes.onNoteEnter();
            }
        });

        $('.dx-emp-notes-chat').on('click', '.dx-emp-notes-btn-link-edit', {}, window.DxEmpNotes.onEditClick);
        $('.dx-emp-notes-chat').on('click', '.dx-emp-notes-btn-link-delete', {}, window.DxEmpNotes.onDeleteClick);
        
        $('.dx-emp-notes-btn-whosee').popover();

        window.DxEmpNotes.isLoaded = true;

        hide_page_splash(1);
    },
    /**
     * Retrieve data for saving
     * @returns {object} Prepared data
     */
    getDataForSave: function () {
        var data = {};

        data.note_id = $('.dx-emp-notes-input-id').val();
        data.user_id = window.DxEmpNotes.userId;
        data.note_text = $('.dx-emp-notes-input-text').val();

        return data;
    },
    /**
     * Event handler when note saving is initiated
     * @returns {undefined}
     */
    onNoteEnter: function () {
        if (window.DxEmpNotes.isSending) {
            return;
        }

        window.DxEmpNotes.showLoading();

        var data = window.DxEmpNotes.getDataForSave();
        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/save',
            data: data,
            type: "post",
            success: window.DxEmpNotes.onSuccessSave,
            error: window.DxEmpNotes.onAjaxError
        });
    },
    /**
     * Load selected note's data into note input boxes
     * @param {object} e Evenet caller
     * @returns {undefined}
     */
    onEditClick: function (e) {
        var edit_btn = $(e.target);

        var note_id = edit_btn.closest('.message').find('.dx-emp-notes-edit-id').val();
        var note_text = edit_btn.closest('.message').find('.dx-emp-notes-edit-body').html();

        $('.dx-emp-notes-input-id').val(note_id);
        $('.dx-emp-notes-input-text').val(note_text);
        $('.dx-emp-notes-input-text').focus();

        // Animate 
        var chat_form = $('.dx-emp-notes-chat-form');

        chat_form.animate({backgroundColor: '#7bb6de'}, 'slow', function () {
            chat_form.animate({backgroundColor: window.DxEmpNotes.chatFormColorDefault}, 'slow');
        });

    },
    /**
     * Event handler for delete click. Opens modal confirmation window
     * @param {object} e Event arguments
     * @returns {undefined}
     */
    onDeleteClick: function (e) {
        var del_btn = $(e.target);

        var note_id = del_btn.closest('.message').find('.dx-emp-notes-edit-id').val();

        PageMain.showConfirm(window.DxEmpNotes.onDeleteConfirm,
                note_id,
                Lang.get('empl_profile.notes.delete_note_title'),
                Lang.get('empl_profile.notes.delete_note_text'),
                Lang.get('form.btn_delete'),
                '');
    },
    /**
     * Event handler when delete operation is confirmed
     * @param {integer} id Note's ID which will be deleted
     * @returns {undefined}
     */
    onDeleteConfirm: function (id) {
        if (window.DxEmpNotes.isSending) {
            return;
        }

        window.DxEmpNotes.showLoading();
        
         var data = {
             note_id: id
         };
        
        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/delete',
            data: data,
            type: "delete",
            success: window.DxEmpNotes.onSuccessDelete,
            error: window.DxEmpNotes.onAjaxError
        });
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxEmpNotes.isSending = true;
        show_page_splash(1);
    },
    /**
     * Hides loading box
     * @returns {undefined}
     */
    hideLoading: function () {
        window.DxEmpNotes.isSending = false;
        hide_page_splash(1);
    },
    /**
     * Event on successful note delete
     * @param {integer} note_id Note id which was deleted
     */
    onSuccessDelete: function (note_id) {
        if (note_id) {
            $('.dx-emp-notes-edit-id[value=' + note_id + ']').closest('li').remove();
        }

        window.DxEmpNotes.hideLoading();
    },
    /**
     * Event on successful data save
     * @param {array} data Data returned about saved noted. Contains view for new note
     */
    onSuccessSave: function (data) {
        // Removes old noted if existed, because it will be moved to top of the list as latest note
        var note_id = $('.dx-emp-notes-input-id').val();
        if (note_id) {
            $('.dx-emp-notes-edit-id[value=' + note_id + ']').closest('li').remove();
        }
        $('.dx-emp-notes-input-id').val('');
        $('.dx-emp-notes-input-text').val('');

        window.DxEmpNotes.hideLoading();

        if (data.view) {
            $('.dx-emp-notes-chat').prepend($(data.view).fadeIn());
            $('.dx-emp-notes-btn-whosee').popover();
        }
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     */
    onAjaxError: function (data) {
        window.DxEmpNotes.hideLoading();
    }
};
/**
 * Contains logic for viewing and editing employee's time off data
 * @type Window.DxEmpTimeoff|window.DxEmpTimeoff 
 */
window.DxEmpTimeoff = window.DxEmpTimeoff || {
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Parameter if control is loaded
     */
    isLoaded: false,
    /**
     * Parameter if data is sending to server
     */
    isSending: false,
    /**
     * Parameter if table has been initialized
     */
    isTableInit: false,
    /**
     * Parameter if chart has been initialized
     */
    isChartInit: false,
    /**
     * Parameter if table needs to be reloaded when gets focus
     */
    doTableRefresh: true,
    /**
     * Parameter if chart needs to be reloaded when gets focus
     */
    doChartRefresh: true,
    /**
     * Current tab index. 0 - focus on chart tab, 1 - focus on table tab
     */
    currentTab: 0,
    /**
     * Current filter's date from value
     */
    dateFrom: '',
    /**
     * Current filter's date to value
     */
    dateTo: '',
    /**
     * Current filter's time off value
     */
    timeoff: 1,
    /**
     * Current filter's time off types title
     */
    timeoffTitle: '',
    /**
     * Parameter if current filter's time off type is in hours or days
     */
    timeoffIsAccrualHours: '',
    /**
     * Date format used in system. This format is used to initialize date picker
     */
    dateFormat: '',
    /**
     * Working days length in hours
     */
    workingDayH: '',
    /**
     * Initializes component
     * @param {integer} userId User's ID which is opened
     * @returns {undefined}
     */
    init: function (userId) {
        window.DxEmpTimeoff.userId = userId;
    },
    /**
     * Get filter parameters for adding to request when getting table or  chart
     * @returns {String} Reteived URL part containign parameters
     */
    getFilterParams: function () {
        return window.DxEmpTimeoff.userId + '/' + window.DxEmpTimeoff.timeoff + '/' + (window.DxEmpTimeoff.dateFrom / 1000) + '/' + (window.DxEmpTimeoff.dateTo / 1000);
    },
    /**
     * Loads view
     * @returns {undefined}
     */
    loadView: function () {
        if (window.DxEmpTimeoff.isLoaded) {
            return;
        }

        window.DxEmpTimeoff.showLoading();

        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/view/' + window.DxEmpTimeoff.userId,
            type: "get",
            success: window.DxEmpTimeoff.onLoadViewSuccess,
            error: function (data) {
                window.DxEmpTimeoff.hideLoading();
            }
        });
    },
    /**
     * Evnet handler when view is successfully loaded
     * @returns {string} View's HTML
     */
    onLoadViewSuccess: function (data) {
        $('#dx-tab_timeoff').html(data);

        window.DxEmpTimeoff.workingDayH = $('#dx-emp-timeoff-panel').data('working_day_h');
        window.DxEmpTimeoff.dateFormat = $('#dx-emp-timeoff-panel').data('date_format').toUpperCase();
        window.DxEmpTimeoff.dateFrom = new Date($('#dx-emp-timeoff-panel').data('date_from'));
        window.DxEmpTimeoff.dateTo = new Date($('#dx-emp-timeoff-panel').data('date_to'));
        window.DxEmpTimeoff.timeoff = $('#dx-emp-timeoff-panel').data('timeoff');
        window.DxEmpTimeoff.timeoffTitle = $('#dx-emp-timeoff-panel').data('timeoff_title');
        window.DxEmpTimeoff.timeoffIsAccrualHours = $('#dx-emp-timeoff-panel').data('is_accrual_hours');

        $("#dx-tab_timeoff [data-counter='counterup']").counterUp({delay: 10, time: 700});

        $(".dx-accrual-calc").click(function () {
            window.DxEmpTimeoff.showLoading();
            var a_elem = $(this);
            $.ajax({
                url: DX_CORE.site_url + 'employee/timeoff/get/calculate/' + window.DxEmpTimeoff.userId + "/" + a_elem.data('timeoff'),
                type: "get",
                success: function (data) {
                    window.DxEmpTimeoff.onCalculateSuccess(a_elem, data);
                }
            });
        });

        $(".dx-accrual-delete").click(function () {
            PageMain.showConfirm(window.DxEmpTimeoff.deleteCalculation, $(this), Lang.get('form.modal_confirm_title'), Lang.get('empl_profile.timeoff.delete_confirm'), Lang.get('form.btn_delete'), Lang.get('form.btn_cancel'));
        });

        $(".dx-accrual-policy").click(function () {
            window.DxEmpTimeoff.showLoading();
            view_list_item("form", $(this).data('policy-id'), $(this).data('policy-list-id'), $(this).data('policy-user-field-id'), window.DxEmpTimeoff.userId, "", "");
        });

        $('.dx-emp-timeoff-sel-timeoff').click(window.DxEmpTimeoff.timeoffSelect);

        $('.dx-emp-timeoff-tab-chart-btn').click(function () {
            window.DxEmpTimeoff.switchTab(0);
        });
        $('.dx-emp-timeoff-tab-table-btn').click(function () {
            window.DxEmpTimeoff.switchTab(1);
        });

        window.DxEmpTimeoff.initFilterDatePicker();

        // Initializes current tab by loadings its data (by default chart tab)
        window.DxEmpTimeoff.reloadTabData();

        window.DxEmpTimeoff.isLoaded = true;
    },
    /**
     * Event handler on successful calculation
     * @param {DOMObject} a_elem Button which triggered event
     * @param {array} data Data returned from server
     * @returns {undefined}
     */
    onCalculateSuccess: function (a_elem, data) {
        var thumb = a_elem.closest("div.widget-thumb");
        var cnt_elem = thumb.find(".widget-thumb-body-stat").first();

        cnt_elem.attr("data-value", data.balance);

        cnt_elem.html(data.balance);

        thumb.find(".widget-thumb-subtitle").first().html(data.unit + ' <span style="font-size: 10px">' + Lang.get('empl_profile.timeoff.available') + '</span>');
        cnt_elem.counterUp({delay: 10, time: 700});

        if (window.DxEmpTimeoff.timeoff == a_elem.data('timeoff')) {
            window.DxEmpTimeoff.setDataRefreshRequest();
        } else {
            window.DxEmpTimeoff.hideLoading();
        }
    },
    /**
     * Deletes calculation
     * @param {DOMObject} a_elem Button which triggered event
     * @returns {undefined}
     */
    deleteCalculation: function (a_elem) {
        window.DxEmpTimeoff.showLoading();
        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/delete_calculated/' + window.DxEmpTimeoff.userId + "/" + a_elem.data('timeoff'),
            type: "get",
            success: function (data) {
                window.DxEmpTimeoff.onCalculateSuccess(a_elem, data);
            }
        });
    },
    /**
     * Sets parameters that data in tabs must be reloaded
     * @returns {undefined}
     */
    setDataRefreshRequest: function () {
        window.DxEmpTimeoff.doChartRefresh = true;
        window.DxEmpTimeoff.doTableRefresh = true;

        // After setting refresh request, refreshes current tab data
        window.DxEmpTimeoff.reloadTabData();
    },
    /**
     * Switches tab and reload data if needed
     */
    switchTab: function (tab_index) {
        window.DxEmpTimeoff.currentTab = tab_index;

        // Bug fix for chart tooltip which sometimes doesnt dissapear
        $("#dx-emp-timeoff-chart-tooltip").hide();

        // After changing tab, chech if data must be resfreshed in current tab
        window.DxEmpTimeoff.reloadTabData();
    },
    /**
     * Reload cureent tab after filter has been changed
     * @returns {undefined}
     */
    reloadTabData: function () {
        if (window.DxEmpTimeoff.currentTab === 0) {
            window.DxEmpTimeoff.loadChart();
        } else {
            window.DxEmpTimeoff.loadTable();
        }
    },
    /**
     * Reload table data. Initializes component if needed
     * @returns {undefined}
     */
    loadTable: function () {
        if (!window.DxEmpTimeoff.isTableInit) {
            window.DxEmpTimeoff.initDataTable();
            window.DxEmpTimeoff.isTableInit = true;
        } else if (window.DxEmpTimeoff.doTableRefresh) {
            window.DxEmpTimeoff.refreshDataTable();
        }

        // Resets parameter that table has been refreshed
        window.DxEmpTimeoff.doTableRefresh = false;
    },
    /**
     * Reload chart data. Initializes component if needed
     * @returns {undefined}
     */
    loadChart: function () {
        if (!window.DxEmpTimeoff.isChartInit) {
            window.DxEmpTimeoff.initChart();
            window.DxEmpTimeoff.isChartInit = true;
        } else if (window.DxEmpTimeoff.doChartRefresh) {
            window.DxEmpTimeoff.refreshChart();
        }

        // Resets parameter that chart has been refreshed
        window.DxEmpTimeoff.doChartRefresh = false;
    },
    /**
     * Initializes data table
     * @returns {undefined}
     */
    initDataTable: function () {
        if (!window.DxEmpTimeoff.dataTable) {
            window.DxEmpTimeoff.dataTable = $('#dx-empt-datatable-timeoff').DataTable({
                serverSide: true,
                searching: false,
                order: [[0, "desc"]],
                ajax: DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.getFilterParams(),
                columns: [
                    {data: 'calc_date', name: 'calc_date'},
                    {data: 'from_date', name: 'from_date'},
                    {data: 'to_date', name: 'to_date'},
                    {data: 'timeoff_record_type.title', name: 'timeoffRecordType.title'},
                    {data: 'notes', name: 'notes'},
                    {data: 'amount', name: 'amount'},
                    {data: 'balance', name: 'balance'}
                ],
                fnPreDrawCallback: function () {
                    window.DxEmpTimeoff.showLoading();
                },
                fnDrawCallback: function () {
                    window.DxEmpTimeoff.hideLoading();
                }
            });
        }
    },
    /**
     * Refreshes data table
     * @returns {undefined}
     */
    refreshDataTable: function () {
        var url = DX_CORE.site_url + 'employee/timeoff/get/table/' + window.DxEmpTimeoff.getFilterParams();

        window.DxEmpTimeoff.dataTable.ajax.url(url).load();
    },
    /**
     * Initializes chart
     * @returns {undefined}
     */
    initChart: function () {
        window.DxEmpTimeoff.showLoading();

        $("<div id='dx-emp-timeoff-chart-tooltip'></div>").appendTo("body");

        $("#dx-emp-timeoff-chart").bind("plothover", window.DxEmpTimeoff.onPlotHover);

        window.DxEmpTimeoff.refreshChart();
    },
    /**
     * Refreshes chart data
     * @returns {undefined}
     */
    refreshChart: function () {
        window.DxEmpTimeoff.showLoading();

        $.ajax({
            url: DX_CORE.site_url + 'employee/timeoff/get/chart/' + window.DxEmpTimeoff.getFilterParams(),
            type: "get",
            success: window.DxEmpTimeoff.onGetChartDataSuccess,
            error: window.DxEmpTimeoff.onAjaxError
        });
    },
    /**
     * Gets unit name for current filtered value
     * @returns {string} Units name
     */
    getUnit: function () {
        if (window.DxEmpTimeoff.timeoffIsAccrualHours == 1) {
            return Lang.get('calendar.hours');
        } else {
            return Lang.get('calendar.days');
        }
    },
    /**
     * Load total values for specified period into panel
     * @param {array} data Data contaning period total data
     * @returns {undefined}
     */
    loadTotalData: function (data) {
        if (!data) {
            return;
        }

        if (data[0]) {
            window.DxEmpTimeoff.timeoffIsAccrualHours = data[0].is_accrual_hours;
            var unit = window.DxEmpTimeoff.getUnit();
            $('#dx-emp-timeoff-period-balance').html(window.DxEmpTimeoff.calculateChartHours(data[0].balance) + ' ' + unit);
            $('#dx-emp-timeoff-period-accrued').html(window.DxEmpTimeoff.calculateChartHours(data[0].accrued) + ' ' + unit);
            $('#dx-emp-timeoff-period-used').html(window.DxEmpTimeoff.calculateChartHours(data[0].used) + ' ' + unit);
        } else {
            $('#dx-emp-timeoff-period-balance').html(0);
            $('#dx-emp-timeoff-period-accrued').html(0);
            $('#dx-emp-timeoff-period-used').html(0);
        }
    },
    /**
     * If needed convert hours to days
     * @param {Number} value Input hours
     * @returns {Number} Output value, could be in days or hours
     */
    calculateChartHours: function (value) {
        if (window.DxEmpTimeoff.timeoffIsAccrualHours == 1) {
            return value;
        } else {
            // Adding 0.00001 removes problem in javascript floating round problem
            return Math.round(((value / window.DxEmpTimeoff.workingDayH) + 0.00001) * 100) / 100;
        }
    },
    /**
     * Event on usccessful data retrieval for chart 
     * @param {object} data Data retrieved form server
     * @returns {undefined}
     */
    onGetChartDataSuccess: function (data) {
        window.DxEmpTimeoff.loadTotalData(data.total);

        var barsUsed = [];
        var barsAccrued = [];
        var lineBalance = [];
        var categories = [];

        for (var i = 0; i < data.res.length; i++) {
            var row = data.res[i];

            categories.push([i, row.calc_date_year + '/' + row.calc_date_month]);

            if (Number(row.used) > 0) {
                barsUsed.push([i, window.DxEmpTimeoff.calculateChartHours(row.used)]);
            }

            if (Number(row.accrued) > 0) {
                barsAccrued.push([i, window.DxEmpTimeoff.calculateChartHours(row.accrued)]);
            }

            lineBalance.push([i, window.DxEmpTimeoff.calculateChartHours(row.balance)]);
        }

        $.plot("#dx-emp-timeoff-chart", [
            {
                data: barsUsed,
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "left"
                },
                color: '#E7505A',
                label: Lang.get('empl_profile.timeoff.used')
            },
            {
                data: barsAccrued,
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "right"
                },
                color: '#26C281',
                label: Lang.get('empl_profile.timeoff.accrued')
            }, {
                data: lineBalance,
                lines: {show: true},
                points: {show: true},
                color: '#3598DC',
                label: Lang.get('empl_profile.timeoff.balance'),
                animator: {start: 100, steps: data.res.length, duration: 1000, direction: "right"}
            }],
                {
                    axisLabels: {
                        show: true
                    },
                    yaxes: [{
                            axisLabel: window.DxEmpTimeoff.getUnit()
                        }],
                    xaxis: {
                        ticks: categories
                    },
                    grid: {
                        hoverable: true
                    }
                });

        window.DxEmpTimeoff.hideLoading();
    },
    /**
     * Shows tooltip on chart hover
     * @param {object} event Event caller
     * @param {object} pos Mouse position
     * @param {object} item Hovered item
     * @returns {undefined}
     */
    onPlotHover: function (event, pos, item) {
        if (item) {
            var y = item.datapoint[1].toFixed(2);

            $("#dx-emp-timeoff-chart-tooltip").html(item.series.label + ": " + y + ' ' + window.DxEmpTimeoff.getUnit())
                    .css({top: pos.pageY + 20, left: pos.pageX + 5})
                    .fadeIn(200);
        } else {
            $("#dx-emp-timeoff-chart-tooltip").hide();
        }
    },
    /**
     * Initiates date picker for filter
     * @returns {undefined}
     */
    initFilterDatePicker: function () {
        $('.dx-emp-timeoff-filter-year-btn').click(function (event) {
            $('#dx-emp-timeoff-filter-year-input').data('daterangepicker').toggle();
        });

        $('#dx-emp-timeoff-filter-year-input').daterangepicker({
            locale: {
                "format": window.DxEmpTimeoff.dateFormat,
                "separator": " - ",
                "applyLabel": Lang.get('date_range.btn_set'),
                "cancelLabel": Lang.get('date_range.btn_cancel'),
                "fromLabel": Lang.get('date_range.lbl_from'),
                "toLabel": Lang.get('date_range.lbl_to'),
                "customRangeLabel": Lang.get('date_range.lbl_interval'),
                "daysOfWeek": [
                    Lang.get('date_range.d_7'),
                    Lang.get('date_range.d_1'),
                    Lang.get('date_range.d_2'),
                    Lang.get('date_range.d_3'),
                    Lang.get('date_range.d_4'),
                    Lang.get('date_range.d_5'),
                    Lang.get('date_range.d_6')
                ],
                "monthNames": [Lang.get('date_range.m_jan'), Lang.get('date_range.m_feb'), Lang.get('date_range.m_mar'), Lang.get('date_range.m_apr'), Lang.get('date_range.m_may'), Lang.get('date_range.m_jun'), Lang.get('date_range.m_jul'), Lang.get('date_range.m_aug'), Lang.get('date_range.m_sep'), Lang.get('date_range.m_oct'), Lang.get('date_range.m_nov'), Lang.get('date_range.m_dec')],
                "firstDay": 1
            },
            "startDate": window.DxEmpTimeoff.dateFrom,
            "endDate": window.DxEmpTimeoff.dateTo,
            "showDropdowns": true,
            "linkedCalendars": false
        }, window.DxEmpTimeoff.yearSelect);
    },
    /**
     * Event callback when filter's year value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    yearSelect: function (start, end, label) {
        window.DxEmpTimeoff.dateFrom = start;
        window.DxEmpTimeoff.dateTo = end;

        $('.dx-emp-timeoff-curr-year').html(start.format(window.DxEmpTimeoff.dateFormat) + ' - ' + end.format(window.DxEmpTimeoff.dateFormat));

        window.DxEmpTimeoff.setDataRefreshRequest();
    },
    /**
     * Event callback when filter's time off type value is selected
     * @param {object} e Event caller
     * @returns {undefined}
     */
    timeoffSelect: function (e) {
        var btn = $(e.target);

        window.DxEmpTimeoff.timeoff = btn.data('value');
        window.DxEmpTimeoff.timeoffTitle = btn.data('title');
        window.DxEmpTimeoff.timeoffIsAccrualHours = btn.data('is_accrual_hours');

        $('.dx-emp-timeoff-curr-timeoff').html(window.DxEmpTimeoff.timeoffTitle);

        window.DxEmpTimeoff.setDataRefreshRequest();
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxEmpTimeoff.inProgressCount++;
        show_page_splash(1);
    },
    /**
     * Hides loading box
     * @returns {undefined}
     */
    hideLoading: function () {
        window.DxEmpTimeoff.isSending = false;
        hide_page_splash(1);
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     * @returns {undefined}
     */
    onAjaxError: function (data) {
        window.DxEmpTimeoff.hideLoading();
    }
};
/*!
* jquery.counterup.js 1.0
*
* Copyright 2013, Benjamin Intal http://gambit.ph @bfintal
* Released under the GPL v2 License
*
* Date: Nov 26, 2013
*/
!function(t){"use strict";t.fn.counterUp=function(e){var n=t.extend({time:400,delay:10},e);return this.each(function(){var e=t(this),u=n,a=function(){var t=u.time/u.delay,n=e.attr("data-value"),a=[n],r=/[0-9]+,[0-9]+/.test(n);n=n.replace(/,/g,"");for(var o=(/^[0-9]+$/.test(n),/^[0-9]+\.[0-9]+$/.test(n)),c=o?(n.split(".")[1]||[]).length:0,d=t;d>=1;d--){var i=parseInt(n/t*d);if(o&&(i=parseFloat(n/t*d).toFixed(c)),r)for(;/(\d+)(\d{3})/.test(i.toString());)i=i.toString().replace(/(\d+)(\d{3})/,"$1,$2");a.unshift(i)}e.data("counterup-nums",a),e.text("0");var s=function(){e.text(e.data("counterup-nums").shift()),e.data("counterup-nums").length?setTimeout(e.data("counterup-func"),u.delay):(delete e.data("counterup-nums"),e.data("counterup-nums",null),e.data("counterup-func",null))};e.data("counterup-func",s),setTimeout(e.data("counterup-func"),u.delay)};e.waypoint(a,{offset:"100%",triggerOnce:!0})})}}(jQuery);
// Generated by CoffeeScript 1.6.2
/*
jQuery Waypoints - v2.0.3
Copyright (c) 2011-2013 Caleb Troughton
Dual licensed under the MIT license and GPL license.
https://github.com/imakewebthings/jquery-waypoints/blob/master/licenses.txt
*/
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,a,c,h,d,p,y,v,w,g,m;i=n(r);c=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;a={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};t.data(u,this.id);a[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||c)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(c&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete a[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=t.data(w))!=null?o:[];i.push(this.id);t.data(w,i)}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=n(t).data(w);if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=a[i.data(u)];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke(this,"disable")},enable:function(){return d._invoke(this,"enable")},destroy:function(){return d._invoke(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t,e){t.each(function(){var t;t=l.getWaypointsByElement(this);return n.each(t,function(t,n){n[e]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(a,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=a[n(t).data(u)])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=a[n(t).data(u)];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.load(function(){return n[m]("refresh")})})}).call(this);
/* Javascript plotting library for jQuery, version 0.8.3.

Copyright (c) 2007-2014 IOLA and Ole Laursen.
Licensed under the MIT license.

*/
(function($){$.color={};$.color.make=function(r,g,b,a){var o={};o.r=r||0;o.g=g||0;o.b=b||0;o.a=a!=null?a:1;o.add=function(c,d){for(var i=0;i<c.length;++i)o[c.charAt(i)]+=d;return o.normalize()};o.scale=function(c,f){for(var i=0;i<c.length;++i)o[c.charAt(i)]*=f;return o.normalize()};o.toString=function(){if(o.a>=1){return"rgb("+[o.r,o.g,o.b].join(",")+")"}else{return"rgba("+[o.r,o.g,o.b,o.a].join(",")+")"}};o.normalize=function(){function clamp(min,value,max){return value<min?min:value>max?max:value}o.r=clamp(0,parseInt(o.r),255);o.g=clamp(0,parseInt(o.g),255);o.b=clamp(0,parseInt(o.b),255);o.a=clamp(0,o.a,1);return o};o.clone=function(){return $.color.make(o.r,o.b,o.g,o.a)};return o.normalize()};$.color.extract=function(elem,css){var c;do{c=elem.css(css).toLowerCase();if(c!=""&&c!="transparent")break;elem=elem.parent()}while(elem.length&&!$.nodeName(elem.get(0),"body"));if(c=="rgba(0, 0, 0, 0)")c="transparent";return $.color.parse(c)};$.color.parse=function(str){var res,m=$.color.make;if(res=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(str))return m(parseInt(res[1],10),parseInt(res[2],10),parseInt(res[3],10));if(res=/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(str))return m(parseInt(res[1],10),parseInt(res[2],10),parseInt(res[3],10),parseFloat(res[4]));if(res=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(str))return m(parseFloat(res[1])*2.55,parseFloat(res[2])*2.55,parseFloat(res[3])*2.55);if(res=/rgba\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(str))return m(parseFloat(res[1])*2.55,parseFloat(res[2])*2.55,parseFloat(res[3])*2.55,parseFloat(res[4]));if(res=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(str))return m(parseInt(res[1],16),parseInt(res[2],16),parseInt(res[3],16));if(res=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(str))return m(parseInt(res[1]+res[1],16),parseInt(res[2]+res[2],16),parseInt(res[3]+res[3],16));var name=$.trim(str).toLowerCase();if(name=="transparent")return m(255,255,255,0);else{res=lookupColors[name]||[0,0,0];return m(res[0],res[1],res[2])}};var lookupColors={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]}})(jQuery);(function($){var hasOwnProperty=Object.prototype.hasOwnProperty;if(!$.fn.detach){$.fn.detach=function(){return this.each(function(){if(this.parentNode){this.parentNode.removeChild(this)}})}}function Canvas(cls,container){var element=container.children("."+cls)[0];if(element==null){element=document.createElement("canvas");element.className=cls;$(element).css({direction:"ltr",position:"absolute",left:0,top:0}).appendTo(container);if(!element.getContext){if(window.G_vmlCanvasManager){element=window.G_vmlCanvasManager.initElement(element)}else{throw new Error("Canvas is not available. If you're using IE with a fall-back such as Excanvas, then there's either a mistake in your conditional include, or the page has no DOCTYPE and is rendering in Quirks Mode.")}}}this.element=element;var context=this.context=element.getContext("2d");var devicePixelRatio=window.devicePixelRatio||1,backingStoreRatio=context.webkitBackingStorePixelRatio||context.mozBackingStorePixelRatio||context.msBackingStorePixelRatio||context.oBackingStorePixelRatio||context.backingStorePixelRatio||1;this.pixelRatio=devicePixelRatio/backingStoreRatio;this.resize(container.width(),container.height());this.textContainer=null;this.text={};this._textCache={}}Canvas.prototype.resize=function(width,height){if(width<=0||height<=0){throw new Error("Invalid dimensions for plot, width = "+width+", height = "+height)}var element=this.element,context=this.context,pixelRatio=this.pixelRatio;if(this.width!=width){element.width=width*pixelRatio;element.style.width=width+"px";this.width=width}if(this.height!=height){element.height=height*pixelRatio;element.style.height=height+"px";this.height=height}context.restore();context.save();context.scale(pixelRatio,pixelRatio)};Canvas.prototype.clear=function(){this.context.clearRect(0,0,this.width,this.height)};Canvas.prototype.render=function(){var cache=this._textCache;for(var layerKey in cache){if(hasOwnProperty.call(cache,layerKey)){var layer=this.getTextLayer(layerKey),layerCache=cache[layerKey];layer.hide();for(var styleKey in layerCache){if(hasOwnProperty.call(layerCache,styleKey)){var styleCache=layerCache[styleKey];for(var key in styleCache){if(hasOwnProperty.call(styleCache,key)){var positions=styleCache[key].positions;for(var i=0,position;position=positions[i];i++){if(position.active){if(!position.rendered){layer.append(position.element);position.rendered=true}}else{positions.splice(i--,1);if(position.rendered){position.element.detach()}}}if(positions.length==0){delete styleCache[key]}}}}}layer.show()}}};Canvas.prototype.getTextLayer=function(classes){var layer=this.text[classes];if(layer==null){if(this.textContainer==null){this.textContainer=$("<div class='flot-text'></div>").css({position:"absolute",top:0,left:0,bottom:0,right:0,"font-size":"smaller",color:"#545454"}).insertAfter(this.element)}layer=this.text[classes]=$("<div></div>").addClass(classes).css({position:"absolute",top:0,left:0,bottom:0,right:0}).appendTo(this.textContainer)}return layer};Canvas.prototype.getTextInfo=function(layer,text,font,angle,width){var textStyle,layerCache,styleCache,info;text=""+text;if(typeof font==="object"){textStyle=font.style+" "+font.variant+" "+font.weight+" "+font.size+"px/"+font.lineHeight+"px "+font.family}else{textStyle=font}layerCache=this._textCache[layer];if(layerCache==null){layerCache=this._textCache[layer]={}}styleCache=layerCache[textStyle];if(styleCache==null){styleCache=layerCache[textStyle]={}}info=styleCache[text];if(info==null){var element=$("<div></div>").html(text).css({position:"absolute","max-width":width,top:-9999}).appendTo(this.getTextLayer(layer));if(typeof font==="object"){element.css({font:textStyle,color:font.color})}else if(typeof font==="string"){element.addClass(font)}info=styleCache[text]={width:element.outerWidth(true),height:element.outerHeight(true),element:element,positions:[]};element.detach()}return info};Canvas.prototype.addText=function(layer,x,y,text,font,angle,width,halign,valign){var info=this.getTextInfo(layer,text,font,angle,width),positions=info.positions;if(halign=="center"){x-=info.width/2}else if(halign=="right"){x-=info.width}if(valign=="middle"){y-=info.height/2}else if(valign=="bottom"){y-=info.height}for(var i=0,position;position=positions[i];i++){if(position.x==x&&position.y==y){position.active=true;return}}position={active:true,rendered:false,element:positions.length?info.element.clone():info.element,x:x,y:y};positions.push(position);position.element.css({top:Math.round(y),left:Math.round(x),"text-align":halign})};Canvas.prototype.removeText=function(layer,x,y,text,font,angle){if(text==null){var layerCache=this._textCache[layer];if(layerCache!=null){for(var styleKey in layerCache){if(hasOwnProperty.call(layerCache,styleKey)){var styleCache=layerCache[styleKey];for(var key in styleCache){if(hasOwnProperty.call(styleCache,key)){var positions=styleCache[key].positions;for(var i=0,position;position=positions[i];i++){position.active=false}}}}}}}else{var positions=this.getTextInfo(layer,text,font,angle).positions;for(var i=0,position;position=positions[i];i++){if(position.x==x&&position.y==y){position.active=false}}}};function Plot(placeholder,data_,options_,plugins){var series=[],options={colors:["#edc240","#afd8f8","#cb4b4b","#4da74d","#9440ed"],legend:{show:true,noColumns:1,labelFormatter:null,labelBoxBorderColor:"#ccc",container:null,position:"ne",margin:5,backgroundColor:null,backgroundOpacity:.85,sorted:null},xaxis:{show:null,position:"bottom",mode:null,font:null,color:null,tickColor:null,transform:null,inverseTransform:null,min:null,max:null,autoscaleMargin:null,ticks:null,tickFormatter:null,labelWidth:null,labelHeight:null,reserveSpace:null,tickLength:null,alignTicksWithAxis:null,tickDecimals:null,tickSize:null,minTickSize:null},yaxis:{autoscaleMargin:.02,position:"left"},xaxes:[],yaxes:[],series:{points:{show:false,radius:3,lineWidth:2,fill:true,fillColor:"#ffffff",symbol:"circle"},lines:{lineWidth:2,fill:false,fillColor:null,steps:false},bars:{show:false,lineWidth:2,barWidth:1,fill:true,fillColor:null,align:"left",horizontal:false,zero:true},shadowSize:3,highlightColor:null},grid:{show:true,aboveData:false,color:"#545454",backgroundColor:null,borderColor:null,tickColor:null,margin:0,labelMargin:5,axisMargin:8,borderWidth:2,minBorderMargin:null,markings:null,markingsColor:"#f4f4f4",markingsLineWidth:2,clickable:false,hoverable:false,autoHighlight:true,mouseActiveRadius:10},interaction:{redrawOverlayInterval:1e3/60},hooks:{}},surface=null,overlay=null,eventHolder=null,ctx=null,octx=null,xaxes=[],yaxes=[],plotOffset={left:0,right:0,top:0,bottom:0},plotWidth=0,plotHeight=0,hooks={processOptions:[],processRawData:[],processDatapoints:[],processOffset:[],drawBackground:[],drawSeries:[],draw:[],bindEvents:[],drawOverlay:[],shutdown:[]},plot=this;plot.setData=setData;plot.setupGrid=setupGrid;plot.draw=draw;plot.getPlaceholder=function(){return placeholder};plot.getCanvas=function(){return surface.element};plot.getPlotOffset=function(){return plotOffset};plot.width=function(){return plotWidth};plot.height=function(){return plotHeight};plot.offset=function(){var o=eventHolder.offset();o.left+=plotOffset.left;o.top+=plotOffset.top;return o};plot.getData=function(){return series};plot.getAxes=function(){var res={},i;$.each(xaxes.concat(yaxes),function(_,axis){if(axis)res[axis.direction+(axis.n!=1?axis.n:"")+"axis"]=axis});return res};plot.getXAxes=function(){return xaxes};plot.getYAxes=function(){return yaxes};plot.c2p=canvasToAxisCoords;plot.p2c=axisToCanvasCoords;plot.getOptions=function(){return options};plot.highlight=highlight;plot.unhighlight=unhighlight;plot.triggerRedrawOverlay=triggerRedrawOverlay;plot.pointOffset=function(point){return{left:parseInt(xaxes[axisNumber(point,"x")-1].p2c(+point.x)+plotOffset.left,10),top:parseInt(yaxes[axisNumber(point,"y")-1].p2c(+point.y)+plotOffset.top,10)}};plot.shutdown=shutdown;plot.destroy=function(){shutdown();placeholder.removeData("plot").empty();series=[];options=null;surface=null;overlay=null;eventHolder=null;ctx=null;octx=null;xaxes=[];yaxes=[];hooks=null;highlights=[];plot=null};plot.resize=function(){var width=placeholder.width(),height=placeholder.height();surface.resize(width,height);overlay.resize(width,height)};plot.hooks=hooks;initPlugins(plot);parseOptions(options_);setupCanvases();setData(data_);setupGrid();draw();bindEvents();function executeHooks(hook,args){args=[plot].concat(args);for(var i=0;i<hook.length;++i)hook[i].apply(this,args)}function initPlugins(){var classes={Canvas:Canvas};for(var i=0;i<plugins.length;++i){var p=plugins[i];p.init(plot,classes);if(p.options)$.extend(true,options,p.options)}}function parseOptions(opts){$.extend(true,options,opts);if(opts&&opts.colors){options.colors=opts.colors}if(options.xaxis.color==null)options.xaxis.color=$.color.parse(options.grid.color).scale("a",.22).toString();if(options.yaxis.color==null)options.yaxis.color=$.color.parse(options.grid.color).scale("a",.22).toString();if(options.xaxis.tickColor==null)options.xaxis.tickColor=options.grid.tickColor||options.xaxis.color;if(options.yaxis.tickColor==null)options.yaxis.tickColor=options.grid.tickColor||options.yaxis.color;if(options.grid.borderColor==null)options.grid.borderColor=options.grid.color;if(options.grid.tickColor==null)options.grid.tickColor=$.color.parse(options.grid.color).scale("a",.22).toString();var i,axisOptions,axisCount,fontSize=placeholder.css("font-size"),fontSizeDefault=fontSize?+fontSize.replace("px",""):13,fontDefaults={style:placeholder.css("font-style"),size:Math.round(.8*fontSizeDefault),variant:placeholder.css("font-variant"),weight:placeholder.css("font-weight"),family:placeholder.css("font-family")};axisCount=options.xaxes.length||1;for(i=0;i<axisCount;++i){axisOptions=options.xaxes[i];if(axisOptions&&!axisOptions.tickColor){axisOptions.tickColor=axisOptions.color}axisOptions=$.extend(true,{},options.xaxis,axisOptions);options.xaxes[i]=axisOptions;if(axisOptions.font){axisOptions.font=$.extend({},fontDefaults,axisOptions.font);if(!axisOptions.font.color){axisOptions.font.color=axisOptions.color}if(!axisOptions.font.lineHeight){axisOptions.font.lineHeight=Math.round(axisOptions.font.size*1.15)}}}axisCount=options.yaxes.length||1;for(i=0;i<axisCount;++i){axisOptions=options.yaxes[i];if(axisOptions&&!axisOptions.tickColor){axisOptions.tickColor=axisOptions.color}axisOptions=$.extend(true,{},options.yaxis,axisOptions);options.yaxes[i]=axisOptions;if(axisOptions.font){axisOptions.font=$.extend({},fontDefaults,axisOptions.font);if(!axisOptions.font.color){axisOptions.font.color=axisOptions.color}if(!axisOptions.font.lineHeight){axisOptions.font.lineHeight=Math.round(axisOptions.font.size*1.15)}}}if(options.xaxis.noTicks&&options.xaxis.ticks==null)options.xaxis.ticks=options.xaxis.noTicks;if(options.yaxis.noTicks&&options.yaxis.ticks==null)options.yaxis.ticks=options.yaxis.noTicks;if(options.x2axis){options.xaxes[1]=$.extend(true,{},options.xaxis,options.x2axis);options.xaxes[1].position="top";if(options.x2axis.min==null){options.xaxes[1].min=null}if(options.x2axis.max==null){options.xaxes[1].max=null}}if(options.y2axis){options.yaxes[1]=$.extend(true,{},options.yaxis,options.y2axis);options.yaxes[1].position="right";if(options.y2axis.min==null){options.yaxes[1].min=null}if(options.y2axis.max==null){options.yaxes[1].max=null}}if(options.grid.coloredAreas)options.grid.markings=options.grid.coloredAreas;if(options.grid.coloredAreasColor)options.grid.markingsColor=options.grid.coloredAreasColor;if(options.lines)$.extend(true,options.series.lines,options.lines);if(options.points)$.extend(true,options.series.points,options.points);if(options.bars)$.extend(true,options.series.bars,options.bars);if(options.shadowSize!=null)options.series.shadowSize=options.shadowSize;if(options.highlightColor!=null)options.series.highlightColor=options.highlightColor;for(i=0;i<options.xaxes.length;++i)getOrCreateAxis(xaxes,i+1).options=options.xaxes[i];for(i=0;i<options.yaxes.length;++i)getOrCreateAxis(yaxes,i+1).options=options.yaxes[i];for(var n in hooks)if(options.hooks[n]&&options.hooks[n].length)hooks[n]=hooks[n].concat(options.hooks[n]);executeHooks(hooks.processOptions,[options])}function setData(d){series=parseData(d);fillInSeriesOptions();processData()}function parseData(d){var res=[];for(var i=0;i<d.length;++i){var s=$.extend(true,{},options.series);if(d[i].data!=null){s.data=d[i].data;delete d[i].data;$.extend(true,s,d[i]);d[i].data=s.data}else s.data=d[i];res.push(s)}return res}function axisNumber(obj,coord){var a=obj[coord+"axis"];if(typeof a=="object")a=a.n;if(typeof a!="number")a=1;return a}function allAxes(){return $.grep(xaxes.concat(yaxes),function(a){return a})}function canvasToAxisCoords(pos){var res={},i,axis;for(i=0;i<xaxes.length;++i){axis=xaxes[i];if(axis&&axis.used)res["x"+axis.n]=axis.c2p(pos.left)}for(i=0;i<yaxes.length;++i){axis=yaxes[i];if(axis&&axis.used)res["y"+axis.n]=axis.c2p(pos.top)}if(res.x1!==undefined)res.x=res.x1;if(res.y1!==undefined)res.y=res.y1;return res}function axisToCanvasCoords(pos){var res={},i,axis,key;for(i=0;i<xaxes.length;++i){axis=xaxes[i];if(axis&&axis.used){key="x"+axis.n;if(pos[key]==null&&axis.n==1)key="x";if(pos[key]!=null){res.left=axis.p2c(pos[key]);break}}}for(i=0;i<yaxes.length;++i){axis=yaxes[i];if(axis&&axis.used){key="y"+axis.n;if(pos[key]==null&&axis.n==1)key="y";if(pos[key]!=null){res.top=axis.p2c(pos[key]);break}}}return res}function getOrCreateAxis(axes,number){if(!axes[number-1])axes[number-1]={n:number,direction:axes==xaxes?"x":"y",options:$.extend(true,{},axes==xaxes?options.xaxis:options.yaxis)};return axes[number-1]}function fillInSeriesOptions(){var neededColors=series.length,maxIndex=-1,i;for(i=0;i<series.length;++i){var sc=series[i].color;if(sc!=null){neededColors--;if(typeof sc=="number"&&sc>maxIndex){maxIndex=sc}}}if(neededColors<=maxIndex){neededColors=maxIndex+1}var c,colors=[],colorPool=options.colors,colorPoolSize=colorPool.length,variation=0;for(i=0;i<neededColors;i++){c=$.color.parse(colorPool[i%colorPoolSize]||"#666");if(i%colorPoolSize==0&&i){if(variation>=0){if(variation<.5){variation=-variation-.2}else variation=0}else variation=-variation}colors[i]=c.scale("rgb",1+variation)}var colori=0,s;for(i=0;i<series.length;++i){s=series[i];if(s.color==null){s.color=colors[colori].toString();++colori}else if(typeof s.color=="number")s.color=colors[s.color].toString();if(s.lines.show==null){var v,show=true;for(v in s)if(s[v]&&s[v].show){show=false;break}if(show)s.lines.show=true}if(s.lines.zero==null){s.lines.zero=!!s.lines.fill}s.xaxis=getOrCreateAxis(xaxes,axisNumber(s,"x"));s.yaxis=getOrCreateAxis(yaxes,axisNumber(s,"y"))}}function processData(){var topSentry=Number.POSITIVE_INFINITY,bottomSentry=Number.NEGATIVE_INFINITY,fakeInfinity=Number.MAX_VALUE,i,j,k,m,length,s,points,ps,x,y,axis,val,f,p,data,format;function updateAxis(axis,min,max){if(min<axis.datamin&&min!=-fakeInfinity)axis.datamin=min;if(max>axis.datamax&&max!=fakeInfinity)axis.datamax=max}$.each(allAxes(),function(_,axis){axis.datamin=topSentry;axis.datamax=bottomSentry;axis.used=false});for(i=0;i<series.length;++i){s=series[i];s.datapoints={points:[]};executeHooks(hooks.processRawData,[s,s.data,s.datapoints])}for(i=0;i<series.length;++i){s=series[i];data=s.data;format=s.datapoints.format;if(!format){format=[];format.push({x:true,number:true,required:true});format.push({y:true,number:true,required:true});if(s.bars.show||s.lines.show&&s.lines.fill){var autoscale=!!(s.bars.show&&s.bars.zero||s.lines.show&&s.lines.zero);format.push({y:true,number:true,required:false,defaultValue:0,autoscale:autoscale});if(s.bars.horizontal){delete format[format.length-1].y;format[format.length-1].x=true}}s.datapoints.format=format}if(s.datapoints.pointsize!=null)continue;s.datapoints.pointsize=format.length;ps=s.datapoints.pointsize;points=s.datapoints.points;var insertSteps=s.lines.show&&s.lines.steps;s.xaxis.used=s.yaxis.used=true;for(j=k=0;j<data.length;++j,k+=ps){p=data[j];var nullify=p==null;if(!nullify){for(m=0;m<ps;++m){val=p[m];f=format[m];if(f){if(f.number&&val!=null){val=+val;if(isNaN(val))val=null;else if(val==Infinity)val=fakeInfinity;else if(val==-Infinity)val=-fakeInfinity}if(val==null){if(f.required)nullify=true;if(f.defaultValue!=null)val=f.defaultValue}}points[k+m]=val}}if(nullify){for(m=0;m<ps;++m){val=points[k+m];if(val!=null){f=format[m];if(f.autoscale!==false){if(f.x){updateAxis(s.xaxis,val,val)}if(f.y){updateAxis(s.yaxis,val,val)}}}points[k+m]=null}}else{if(insertSteps&&k>0&&points[k-ps]!=null&&points[k-ps]!=points[k]&&points[k-ps+1]!=points[k+1]){for(m=0;m<ps;++m)points[k+ps+m]=points[k+m];points[k+1]=points[k-ps+1];k+=ps}}}}for(i=0;i<series.length;++i){s=series[i];executeHooks(hooks.processDatapoints,[s,s.datapoints])}for(i=0;i<series.length;++i){s=series[i];points=s.datapoints.points;ps=s.datapoints.pointsize;format=s.datapoints.format;var xmin=topSentry,ymin=topSentry,xmax=bottomSentry,ymax=bottomSentry;for(j=0;j<points.length;j+=ps){if(points[j]==null)continue;for(m=0;m<ps;++m){val=points[j+m];f=format[m];if(!f||f.autoscale===false||val==fakeInfinity||val==-fakeInfinity)continue;if(f.x){if(val<xmin)xmin=val;if(val>xmax)xmax=val}if(f.y){if(val<ymin)ymin=val;if(val>ymax)ymax=val}}}if(s.bars.show){var delta;switch(s.bars.align){case"left":delta=0;break;case"right":delta=-s.bars.barWidth;break;default:delta=-s.bars.barWidth/2}if(s.bars.horizontal){ymin+=delta;ymax+=delta+s.bars.barWidth}else{xmin+=delta;xmax+=delta+s.bars.barWidth}}updateAxis(s.xaxis,xmin,xmax);updateAxis(s.yaxis,ymin,ymax)}$.each(allAxes(),function(_,axis){if(axis.datamin==topSentry)axis.datamin=null;if(axis.datamax==bottomSentry)axis.datamax=null})}function setupCanvases(){placeholder.css("padding",0).children().filter(function(){return!$(this).hasClass("flot-overlay")&&!$(this).hasClass("flot-base")}).remove();if(placeholder.css("position")=="static")placeholder.css("position","relative");surface=new Canvas("flot-base",placeholder);overlay=new Canvas("flot-overlay",placeholder);ctx=surface.context;octx=overlay.context;eventHolder=$(overlay.element).unbind();var existing=placeholder.data("plot");if(existing){existing.shutdown();overlay.clear()}placeholder.data("plot",plot)}function bindEvents(){if(options.grid.hoverable){eventHolder.mousemove(onMouseMove);eventHolder.bind("mouseleave",onMouseLeave)}if(options.grid.clickable)eventHolder.click(onClick);executeHooks(hooks.bindEvents,[eventHolder])}function shutdown(){if(redrawTimeout)clearTimeout(redrawTimeout);eventHolder.unbind("mousemove",onMouseMove);eventHolder.unbind("mouseleave",onMouseLeave);eventHolder.unbind("click",onClick);executeHooks(hooks.shutdown,[eventHolder])}function setTransformationHelpers(axis){function identity(x){return x}var s,m,t=axis.options.transform||identity,it=axis.options.inverseTransform;if(axis.direction=="x"){s=axis.scale=plotWidth/Math.abs(t(axis.max)-t(axis.min));m=Math.min(t(axis.max),t(axis.min))}else{s=axis.scale=plotHeight/Math.abs(t(axis.max)-t(axis.min));s=-s;m=Math.max(t(axis.max),t(axis.min))}if(t==identity)axis.p2c=function(p){return(p-m)*s};else axis.p2c=function(p){return(t(p)-m)*s};if(!it)axis.c2p=function(c){return m+c/s};else axis.c2p=function(c){return it(m+c/s)}}function measureTickLabels(axis){var opts=axis.options,ticks=axis.ticks||[],labelWidth=opts.labelWidth||0,labelHeight=opts.labelHeight||0,maxWidth=labelWidth||(axis.direction=="x"?Math.floor(surface.width/(ticks.length||1)):null),legacyStyles=axis.direction+"Axis "+axis.direction+axis.n+"Axis",layer="flot-"+axis.direction+"-axis flot-"+axis.direction+axis.n+"-axis "+legacyStyles,font=opts.font||"flot-tick-label tickLabel";for(var i=0;i<ticks.length;++i){var t=ticks[i];if(!t.label)continue;var info=surface.getTextInfo(layer,t.label,font,null,maxWidth);labelWidth=Math.max(labelWidth,info.width);labelHeight=Math.max(labelHeight,info.height)}axis.labelWidth=opts.labelWidth||labelWidth;axis.labelHeight=opts.labelHeight||labelHeight}function allocateAxisBoxFirstPhase(axis){var lw=axis.labelWidth,lh=axis.labelHeight,pos=axis.options.position,isXAxis=axis.direction==="x",tickLength=axis.options.tickLength,axisMargin=options.grid.axisMargin,padding=options.grid.labelMargin,innermost=true,outermost=true,first=true,found=false;$.each(isXAxis?xaxes:yaxes,function(i,a){if(a&&(a.show||a.reserveSpace)){if(a===axis){found=true}else if(a.options.position===pos){if(found){outermost=false}else{innermost=false}}if(!found){first=false}}});if(outermost){axisMargin=0}if(tickLength==null){tickLength=first?"full":5}if(!isNaN(+tickLength))padding+=+tickLength;if(isXAxis){lh+=padding;if(pos=="bottom"){plotOffset.bottom+=lh+axisMargin;axis.box={top:surface.height-plotOffset.bottom,height:lh}}else{axis.box={top:plotOffset.top+axisMargin,height:lh};plotOffset.top+=lh+axisMargin}}else{lw+=padding;if(pos=="left"){axis.box={left:plotOffset.left+axisMargin,width:lw};plotOffset.left+=lw+axisMargin}else{plotOffset.right+=lw+axisMargin;axis.box={left:surface.width-plotOffset.right,width:lw}}}axis.position=pos;axis.tickLength=tickLength;axis.box.padding=padding;axis.innermost=innermost}function allocateAxisBoxSecondPhase(axis){if(axis.direction=="x"){axis.box.left=plotOffset.left-axis.labelWidth/2;axis.box.width=surface.width-plotOffset.left-plotOffset.right+axis.labelWidth}else{axis.box.top=plotOffset.top-axis.labelHeight/2;axis.box.height=surface.height-plotOffset.bottom-plotOffset.top+axis.labelHeight}}function adjustLayoutForThingsStickingOut(){var minMargin=options.grid.minBorderMargin,axis,i;if(minMargin==null){minMargin=0;for(i=0;i<series.length;++i)minMargin=Math.max(minMargin,2*(series[i].points.radius+series[i].points.lineWidth/2))}var margins={left:minMargin,right:minMargin,top:minMargin,bottom:minMargin};$.each(allAxes(),function(_,axis){if(axis.reserveSpace&&axis.ticks&&axis.ticks.length){if(axis.direction==="x"){margins.left=Math.max(margins.left,axis.labelWidth/2);margins.right=Math.max(margins.right,axis.labelWidth/2)}else{margins.bottom=Math.max(margins.bottom,axis.labelHeight/2);margins.top=Math.max(margins.top,axis.labelHeight/2)}}});plotOffset.left=Math.ceil(Math.max(margins.left,plotOffset.left));plotOffset.right=Math.ceil(Math.max(margins.right,plotOffset.right));plotOffset.top=Math.ceil(Math.max(margins.top,plotOffset.top));plotOffset.bottom=Math.ceil(Math.max(margins.bottom,plotOffset.bottom))}function setupGrid(){var i,axes=allAxes(),showGrid=options.grid.show;for(var a in plotOffset){var margin=options.grid.margin||0;plotOffset[a]=typeof margin=="number"?margin:margin[a]||0}executeHooks(hooks.processOffset,[plotOffset]);for(var a in plotOffset){if(typeof options.grid.borderWidth=="object"){plotOffset[a]+=showGrid?options.grid.borderWidth[a]:0}else{plotOffset[a]+=showGrid?options.grid.borderWidth:0}}$.each(axes,function(_,axis){var axisOpts=axis.options;axis.show=axisOpts.show==null?axis.used:axisOpts.show;axis.reserveSpace=axisOpts.reserveSpace==null?axis.show:axisOpts.reserveSpace;setRange(axis)});if(showGrid){var allocatedAxes=$.grep(axes,function(axis){return axis.show||axis.reserveSpace});$.each(allocatedAxes,function(_,axis){setupTickGeneration(axis);setTicks(axis);snapRangeToTicks(axis,axis.ticks);measureTickLabels(axis)});for(i=allocatedAxes.length-1;i>=0;--i)allocateAxisBoxFirstPhase(allocatedAxes[i]);adjustLayoutForThingsStickingOut();$.each(allocatedAxes,function(_,axis){allocateAxisBoxSecondPhase(axis)})}plotWidth=surface.width-plotOffset.left-plotOffset.right;plotHeight=surface.height-plotOffset.bottom-plotOffset.top;$.each(axes,function(_,axis){setTransformationHelpers(axis)});if(showGrid){drawAxisLabels()}insertLegend()}function setRange(axis){var opts=axis.options,min=+(opts.min!=null?opts.min:axis.datamin),max=+(opts.max!=null?opts.max:axis.datamax),delta=max-min;if(delta==0){var widen=max==0?1:.01;if(opts.min==null)min-=widen;if(opts.max==null||opts.min!=null)max+=widen}else{var margin=opts.autoscaleMargin;if(margin!=null){if(opts.min==null){min-=delta*margin;if(min<0&&axis.datamin!=null&&axis.datamin>=0)min=0}if(opts.max==null){max+=delta*margin;if(max>0&&axis.datamax!=null&&axis.datamax<=0)max=0}}}axis.min=min;axis.max=max}function setupTickGeneration(axis){var opts=axis.options;var noTicks;if(typeof opts.ticks=="number"&&opts.ticks>0)noTicks=opts.ticks;else noTicks=.3*Math.sqrt(axis.direction=="x"?surface.width:surface.height);var delta=(axis.max-axis.min)/noTicks,dec=-Math.floor(Math.log(delta)/Math.LN10),maxDec=opts.tickDecimals;if(maxDec!=null&&dec>maxDec){dec=maxDec}var magn=Math.pow(10,-dec),norm=delta/magn,size;if(norm<1.5){size=1}else if(norm<3){size=2;if(norm>2.25&&(maxDec==null||dec+1<=maxDec)){size=2.5;++dec}}else if(norm<7.5){size=5}else{size=10}size*=magn;if(opts.minTickSize!=null&&size<opts.minTickSize){size=opts.minTickSize}axis.delta=delta;axis.tickDecimals=Math.max(0,maxDec!=null?maxDec:dec);axis.tickSize=opts.tickSize||size;if(opts.mode=="time"&&!axis.tickGenerator){throw new Error("Time mode requires the flot.time plugin.")}if(!axis.tickGenerator){axis.tickGenerator=function(axis){var ticks=[],start=floorInBase(axis.min,axis.tickSize),i=0,v=Number.NaN,prev;do{prev=v;v=start+i*axis.tickSize;ticks.push(v);++i}while(v<axis.max&&v!=prev);return ticks};axis.tickFormatter=function(value,axis){var factor=axis.tickDecimals?Math.pow(10,axis.tickDecimals):1;var formatted=""+Math.round(value*factor)/factor;if(axis.tickDecimals!=null){var decimal=formatted.indexOf(".");var precision=decimal==-1?0:formatted.length-decimal-1;if(precision<axis.tickDecimals){return(precision?formatted:formatted+".")+(""+factor).substr(1,axis.tickDecimals-precision)}}return formatted}}if($.isFunction(opts.tickFormatter))axis.tickFormatter=function(v,axis){return""+opts.tickFormatter(v,axis)};if(opts.alignTicksWithAxis!=null){var otherAxis=(axis.direction=="x"?xaxes:yaxes)[opts.alignTicksWithAxis-1];if(otherAxis&&otherAxis.used&&otherAxis!=axis){var niceTicks=axis.tickGenerator(axis);if(niceTicks.length>0){if(opts.min==null)axis.min=Math.min(axis.min,niceTicks[0]);if(opts.max==null&&niceTicks.length>1)axis.max=Math.max(axis.max,niceTicks[niceTicks.length-1])}axis.tickGenerator=function(axis){var ticks=[],v,i;for(i=0;i<otherAxis.ticks.length;++i){v=(otherAxis.ticks[i].v-otherAxis.min)/(otherAxis.max-otherAxis.min);v=axis.min+v*(axis.max-axis.min);ticks.push(v)}return ticks};if(!axis.mode&&opts.tickDecimals==null){var extraDec=Math.max(0,-Math.floor(Math.log(axis.delta)/Math.LN10)+1),ts=axis.tickGenerator(axis);if(!(ts.length>1&&/\..*0$/.test((ts[1]-ts[0]).toFixed(extraDec))))axis.tickDecimals=extraDec}}}}function setTicks(axis){var oticks=axis.options.ticks,ticks=[];if(oticks==null||typeof oticks=="number"&&oticks>0)ticks=axis.tickGenerator(axis);else if(oticks){if($.isFunction(oticks))ticks=oticks(axis);else ticks=oticks}var i,v;axis.ticks=[];for(i=0;i<ticks.length;++i){var label=null;var t=ticks[i];if(typeof t=="object"){v=+t[0];if(t.length>1)label=t[1]}else v=+t;if(label==null)label=axis.tickFormatter(v,axis);if(!isNaN(v))axis.ticks.push({v:v,label:label})}}function snapRangeToTicks(axis,ticks){if(axis.options.autoscaleMargin&&ticks.length>0){if(axis.options.min==null)axis.min=Math.min(axis.min,ticks[0].v);if(axis.options.max==null&&ticks.length>1)axis.max=Math.max(axis.max,ticks[ticks.length-1].v)}}function draw(){surface.clear();executeHooks(hooks.drawBackground,[ctx]);var grid=options.grid;if(grid.show&&grid.backgroundColor)drawBackground();if(grid.show&&!grid.aboveData){drawGrid()}for(var i=0;i<series.length;++i){executeHooks(hooks.drawSeries,[ctx,series[i]]);drawSeries(series[i])}executeHooks(hooks.draw,[ctx]);if(grid.show&&grid.aboveData){drawGrid()}surface.render();triggerRedrawOverlay()}function extractRange(ranges,coord){var axis,from,to,key,axes=allAxes();for(var i=0;i<axes.length;++i){axis=axes[i];if(axis.direction==coord){key=coord+axis.n+"axis";if(!ranges[key]&&axis.n==1)key=coord+"axis";if(ranges[key]){from=ranges[key].from;to=ranges[key].to;break}}}if(!ranges[key]){axis=coord=="x"?xaxes[0]:yaxes[0];from=ranges[coord+"1"];to=ranges[coord+"2"]}if(from!=null&&to!=null&&from>to){var tmp=from;from=to;to=tmp}return{from:from,to:to,axis:axis}}function drawBackground(){ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.fillStyle=getColorOrGradient(options.grid.backgroundColor,plotHeight,0,"rgba(255, 255, 255, 0)");ctx.fillRect(0,0,plotWidth,plotHeight);ctx.restore()}function drawGrid(){var i,axes,bw,bc;ctx.save();ctx.translate(plotOffset.left,plotOffset.top);var markings=options.grid.markings;if(markings){if($.isFunction(markings)){axes=plot.getAxes();axes.xmin=axes.xaxis.min;axes.xmax=axes.xaxis.max;axes.ymin=axes.yaxis.min;axes.ymax=axes.yaxis.max;markings=markings(axes)}for(i=0;i<markings.length;++i){var m=markings[i],xrange=extractRange(m,"x"),yrange=extractRange(m,"y");if(xrange.from==null)xrange.from=xrange.axis.min;if(xrange.to==null)xrange.to=xrange.axis.max;
if(yrange.from==null)yrange.from=yrange.axis.min;if(yrange.to==null)yrange.to=yrange.axis.max;if(xrange.to<xrange.axis.min||xrange.from>xrange.axis.max||yrange.to<yrange.axis.min||yrange.from>yrange.axis.max)continue;xrange.from=Math.max(xrange.from,xrange.axis.min);xrange.to=Math.min(xrange.to,xrange.axis.max);yrange.from=Math.max(yrange.from,yrange.axis.min);yrange.to=Math.min(yrange.to,yrange.axis.max);var xequal=xrange.from===xrange.to,yequal=yrange.from===yrange.to;if(xequal&&yequal){continue}xrange.from=Math.floor(xrange.axis.p2c(xrange.from));xrange.to=Math.floor(xrange.axis.p2c(xrange.to));yrange.from=Math.floor(yrange.axis.p2c(yrange.from));yrange.to=Math.floor(yrange.axis.p2c(yrange.to));if(xequal||yequal){var lineWidth=m.lineWidth||options.grid.markingsLineWidth,subPixel=lineWidth%2?.5:0;ctx.beginPath();ctx.strokeStyle=m.color||options.grid.markingsColor;ctx.lineWidth=lineWidth;if(xequal){ctx.moveTo(xrange.to+subPixel,yrange.from);ctx.lineTo(xrange.to+subPixel,yrange.to)}else{ctx.moveTo(xrange.from,yrange.to+subPixel);ctx.lineTo(xrange.to,yrange.to+subPixel)}ctx.stroke()}else{ctx.fillStyle=m.color||options.grid.markingsColor;ctx.fillRect(xrange.from,yrange.to,xrange.to-xrange.from,yrange.from-yrange.to)}}}axes=allAxes();bw=options.grid.borderWidth;for(var j=0;j<axes.length;++j){var axis=axes[j],box=axis.box,t=axis.tickLength,x,y,xoff,yoff;if(!axis.show||axis.ticks.length==0)continue;ctx.lineWidth=1;if(axis.direction=="x"){x=0;if(t=="full")y=axis.position=="top"?0:plotHeight;else y=box.top-plotOffset.top+(axis.position=="top"?box.height:0)}else{y=0;if(t=="full")x=axis.position=="left"?0:plotWidth;else x=box.left-plotOffset.left+(axis.position=="left"?box.width:0)}if(!axis.innermost){ctx.strokeStyle=axis.options.color;ctx.beginPath();xoff=yoff=0;if(axis.direction=="x")xoff=plotWidth+1;else yoff=plotHeight+1;if(ctx.lineWidth==1){if(axis.direction=="x"){y=Math.floor(y)+.5}else{x=Math.floor(x)+.5}}ctx.moveTo(x,y);ctx.lineTo(x+xoff,y+yoff);ctx.stroke()}ctx.strokeStyle=axis.options.tickColor;ctx.beginPath();for(i=0;i<axis.ticks.length;++i){var v=axis.ticks[i].v;xoff=yoff=0;if(isNaN(v)||v<axis.min||v>axis.max||t=="full"&&(typeof bw=="object"&&bw[axis.position]>0||bw>0)&&(v==axis.min||v==axis.max))continue;if(axis.direction=="x"){x=axis.p2c(v);yoff=t=="full"?-plotHeight:t;if(axis.position=="top")yoff=-yoff}else{y=axis.p2c(v);xoff=t=="full"?-plotWidth:t;if(axis.position=="left")xoff=-xoff}if(ctx.lineWidth==1){if(axis.direction=="x")x=Math.floor(x)+.5;else y=Math.floor(y)+.5}ctx.moveTo(x,y);ctx.lineTo(x+xoff,y+yoff)}ctx.stroke()}if(bw){bc=options.grid.borderColor;if(typeof bw=="object"||typeof bc=="object"){if(typeof bw!=="object"){bw={top:bw,right:bw,bottom:bw,left:bw}}if(typeof bc!=="object"){bc={top:bc,right:bc,bottom:bc,left:bc}}if(bw.top>0){ctx.strokeStyle=bc.top;ctx.lineWidth=bw.top;ctx.beginPath();ctx.moveTo(0-bw.left,0-bw.top/2);ctx.lineTo(plotWidth,0-bw.top/2);ctx.stroke()}if(bw.right>0){ctx.strokeStyle=bc.right;ctx.lineWidth=bw.right;ctx.beginPath();ctx.moveTo(plotWidth+bw.right/2,0-bw.top);ctx.lineTo(plotWidth+bw.right/2,plotHeight);ctx.stroke()}if(bw.bottom>0){ctx.strokeStyle=bc.bottom;ctx.lineWidth=bw.bottom;ctx.beginPath();ctx.moveTo(plotWidth+bw.right,plotHeight+bw.bottom/2);ctx.lineTo(0,plotHeight+bw.bottom/2);ctx.stroke()}if(bw.left>0){ctx.strokeStyle=bc.left;ctx.lineWidth=bw.left;ctx.beginPath();ctx.moveTo(0-bw.left/2,plotHeight+bw.bottom);ctx.lineTo(0-bw.left/2,0);ctx.stroke()}}else{ctx.lineWidth=bw;ctx.strokeStyle=options.grid.borderColor;ctx.strokeRect(-bw/2,-bw/2,plotWidth+bw,plotHeight+bw)}}ctx.restore()}function drawAxisLabels(){$.each(allAxes(),function(_,axis){var box=axis.box,legacyStyles=axis.direction+"Axis "+axis.direction+axis.n+"Axis",layer="flot-"+axis.direction+"-axis flot-"+axis.direction+axis.n+"-axis "+legacyStyles,font=axis.options.font||"flot-tick-label tickLabel",tick,x,y,halign,valign;surface.removeText(layer);if(!axis.show||axis.ticks.length==0)return;for(var i=0;i<axis.ticks.length;++i){tick=axis.ticks[i];if(!tick.label||tick.v<axis.min||tick.v>axis.max)continue;if(axis.direction=="x"){halign="center";x=plotOffset.left+axis.p2c(tick.v);if(axis.position=="bottom"){y=box.top+box.padding}else{y=box.top+box.height-box.padding;valign="bottom"}}else{valign="middle";y=plotOffset.top+axis.p2c(tick.v);if(axis.position=="left"){x=box.left+box.width-box.padding;halign="right"}else{x=box.left+box.padding}}surface.addText(layer,x,y,tick.label,font,null,null,halign,valign)}})}function drawSeries(series){if(series.lines.show)drawSeriesLines(series);if(series.bars.show)drawSeriesBars(series);if(series.points.show)drawSeriesPoints(series)}function drawSeriesLines(series){function plotLine(datapoints,xoffset,yoffset,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize,prevx=null,prevy=null;ctx.beginPath();for(var i=ps;i<points.length;i+=ps){var x1=points[i-ps],y1=points[i-ps+1],x2=points[i],y2=points[i+1];if(x1==null||x2==null)continue;if(y1<=y2&&y1<axisy.min){if(y2<axisy.min)continue;x1=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.min}else if(y2<=y1&&y2<axisy.min){if(y1<axisy.min)continue;x2=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.min}if(y1>=y2&&y1>axisy.max){if(y2>axisy.max)continue;x1=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.max}else if(y2>=y1&&y2>axisy.max){if(y1>axisy.max)continue;x2=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.max}if(x1<=x2&&x1<axisx.min){if(x2<axisx.min)continue;y1=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.min}else if(x2<=x1&&x2<axisx.min){if(x1<axisx.min)continue;y2=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.min}if(x1>=x2&&x1>axisx.max){if(x2>axisx.max)continue;y1=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.max}else if(x2>=x1&&x2>axisx.max){if(x1>axisx.max)continue;y2=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.max}if(x1!=prevx||y1!=prevy)ctx.moveTo(axisx.p2c(x1)+xoffset,axisy.p2c(y1)+yoffset);prevx=x2;prevy=y2;ctx.lineTo(axisx.p2c(x2)+xoffset,axisy.p2c(y2)+yoffset)}ctx.stroke()}function plotLineArea(datapoints,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize,bottom=Math.min(Math.max(0,axisy.min),axisy.max),i=0,top,areaOpen=false,ypos=1,segmentStart=0,segmentEnd=0;while(true){if(ps>0&&i>points.length+ps)break;i+=ps;var x1=points[i-ps],y1=points[i-ps+ypos],x2=points[i],y2=points[i+ypos];if(areaOpen){if(ps>0&&x1!=null&&x2==null){segmentEnd=i;ps=-ps;ypos=2;continue}if(ps<0&&i==segmentStart+ps){ctx.fill();areaOpen=false;ps=-ps;ypos=1;i=segmentStart=segmentEnd+ps;continue}}if(x1==null||x2==null)continue;if(x1<=x2&&x1<axisx.min){if(x2<axisx.min)continue;y1=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.min}else if(x2<=x1&&x2<axisx.min){if(x1<axisx.min)continue;y2=(axisx.min-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.min}if(x1>=x2&&x1>axisx.max){if(x2>axisx.max)continue;y1=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x1=axisx.max}else if(x2>=x1&&x2>axisx.max){if(x1>axisx.max)continue;y2=(axisx.max-x1)/(x2-x1)*(y2-y1)+y1;x2=axisx.max}if(!areaOpen){ctx.beginPath();ctx.moveTo(axisx.p2c(x1),axisy.p2c(bottom));areaOpen=true}if(y1>=axisy.max&&y2>=axisy.max){ctx.lineTo(axisx.p2c(x1),axisy.p2c(axisy.max));ctx.lineTo(axisx.p2c(x2),axisy.p2c(axisy.max));continue}else if(y1<=axisy.min&&y2<=axisy.min){ctx.lineTo(axisx.p2c(x1),axisy.p2c(axisy.min));ctx.lineTo(axisx.p2c(x2),axisy.p2c(axisy.min));continue}var x1old=x1,x2old=x2;if(y1<=y2&&y1<axisy.min&&y2>=axisy.min){x1=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.min}else if(y2<=y1&&y2<axisy.min&&y1>=axisy.min){x2=(axisy.min-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.min}if(y1>=y2&&y1>axisy.max&&y2<=axisy.max){x1=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y1=axisy.max}else if(y2>=y1&&y2>axisy.max&&y1<=axisy.max){x2=(axisy.max-y1)/(y2-y1)*(x2-x1)+x1;y2=axisy.max}if(x1!=x1old){ctx.lineTo(axisx.p2c(x1old),axisy.p2c(y1))}ctx.lineTo(axisx.p2c(x1),axisy.p2c(y1));ctx.lineTo(axisx.p2c(x2),axisy.p2c(y2));if(x2!=x2old){ctx.lineTo(axisx.p2c(x2),axisy.p2c(y2));ctx.lineTo(axisx.p2c(x2old),axisy.p2c(y2))}}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.lineJoin="round";var lw=series.lines.lineWidth,sw=series.shadowSize;if(lw>0&&sw>0){ctx.lineWidth=sw;ctx.strokeStyle="rgba(0,0,0,0.1)";var angle=Math.PI/18;plotLine(series.datapoints,Math.sin(angle)*(lw/2+sw/2),Math.cos(angle)*(lw/2+sw/2),series.xaxis,series.yaxis);ctx.lineWidth=sw/2;plotLine(series.datapoints,Math.sin(angle)*(lw/2+sw/4),Math.cos(angle)*(lw/2+sw/4),series.xaxis,series.yaxis)}ctx.lineWidth=lw;ctx.strokeStyle=series.color;var fillStyle=getFillStyle(series.lines,series.color,0,plotHeight);if(fillStyle){ctx.fillStyle=fillStyle;plotLineArea(series.datapoints,series.xaxis,series.yaxis)}if(lw>0)plotLine(series.datapoints,0,0,series.xaxis,series.yaxis);ctx.restore()}function drawSeriesPoints(series){function plotPoints(datapoints,radius,fillStyle,offset,shadow,axisx,axisy,symbol){var points=datapoints.points,ps=datapoints.pointsize;for(var i=0;i<points.length;i+=ps){var x=points[i],y=points[i+1];if(x==null||x<axisx.min||x>axisx.max||y<axisy.min||y>axisy.max)continue;ctx.beginPath();x=axisx.p2c(x);y=axisy.p2c(y)+offset;if(symbol=="circle")ctx.arc(x,y,radius,0,shadow?Math.PI:Math.PI*2,false);else symbol(ctx,x,y,radius,shadow);ctx.closePath();if(fillStyle){ctx.fillStyle=fillStyle;ctx.fill()}ctx.stroke()}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);var lw=series.points.lineWidth,sw=series.shadowSize,radius=series.points.radius,symbol=series.points.symbol;if(lw==0)lw=1e-4;if(lw>0&&sw>0){var w=sw/2;ctx.lineWidth=w;ctx.strokeStyle="rgba(0,0,0,0.1)";plotPoints(series.datapoints,radius,null,w+w/2,true,series.xaxis,series.yaxis,symbol);ctx.strokeStyle="rgba(0,0,0,0.2)";plotPoints(series.datapoints,radius,null,w/2,true,series.xaxis,series.yaxis,symbol)}ctx.lineWidth=lw;ctx.strokeStyle=series.color;plotPoints(series.datapoints,radius,getFillStyle(series.points,series.color),0,false,series.xaxis,series.yaxis,symbol);ctx.restore()}function drawBar(x,y,b,barLeft,barRight,fillStyleCallback,axisx,axisy,c,horizontal,lineWidth){var left,right,bottom,top,drawLeft,drawRight,drawTop,drawBottom,tmp;if(horizontal){drawBottom=drawRight=drawTop=true;drawLeft=false;left=b;right=x;top=y+barLeft;bottom=y+barRight;if(right<left){tmp=right;right=left;left=tmp;drawLeft=true;drawRight=false}}else{drawLeft=drawRight=drawTop=true;drawBottom=false;left=x+barLeft;right=x+barRight;bottom=b;top=y;if(top<bottom){tmp=top;top=bottom;bottom=tmp;drawBottom=true;drawTop=false}}if(right<axisx.min||left>axisx.max||top<axisy.min||bottom>axisy.max)return;if(left<axisx.min){left=axisx.min;drawLeft=false}if(right>axisx.max){right=axisx.max;drawRight=false}if(bottom<axisy.min){bottom=axisy.min;drawBottom=false}if(top>axisy.max){top=axisy.max;drawTop=false}left=axisx.p2c(left);bottom=axisy.p2c(bottom);right=axisx.p2c(right);top=axisy.p2c(top);if(fillStyleCallback){c.fillStyle=fillStyleCallback(bottom,top);c.fillRect(left,top,right-left,bottom-top)}if(lineWidth>0&&(drawLeft||drawRight||drawTop||drawBottom)){c.beginPath();c.moveTo(left,bottom);if(drawLeft)c.lineTo(left,top);else c.moveTo(left,top);if(drawTop)c.lineTo(right,top);else c.moveTo(right,top);if(drawRight)c.lineTo(right,bottom);else c.moveTo(right,bottom);if(drawBottom)c.lineTo(left,bottom);else c.moveTo(left,bottom);c.stroke()}}function drawSeriesBars(series){function plotBars(datapoints,barLeft,barRight,fillStyleCallback,axisx,axisy){var points=datapoints.points,ps=datapoints.pointsize;for(var i=0;i<points.length;i+=ps){if(points[i]==null)continue;drawBar(points[i],points[i+1],points[i+2],barLeft,barRight,fillStyleCallback,axisx,axisy,ctx,series.bars.horizontal,series.bars.lineWidth)}}ctx.save();ctx.translate(plotOffset.left,plotOffset.top);ctx.lineWidth=series.bars.lineWidth;ctx.strokeStyle=series.color;var barLeft;switch(series.bars.align){case"left":barLeft=0;break;case"right":barLeft=-series.bars.barWidth;break;default:barLeft=-series.bars.barWidth/2}var fillStyleCallback=series.bars.fill?function(bottom,top){return getFillStyle(series.bars,series.color,bottom,top)}:null;plotBars(series.datapoints,barLeft,barLeft+series.bars.barWidth,fillStyleCallback,series.xaxis,series.yaxis);ctx.restore()}function getFillStyle(filloptions,seriesColor,bottom,top){var fill=filloptions.fill;if(!fill)return null;if(filloptions.fillColor)return getColorOrGradient(filloptions.fillColor,bottom,top,seriesColor);var c=$.color.parse(seriesColor);c.a=typeof fill=="number"?fill:.4;c.normalize();return c.toString()}function insertLegend(){if(options.legend.container!=null){$(options.legend.container).html("")}else{placeholder.find(".legend").remove()}if(!options.legend.show){return}var fragments=[],entries=[],rowStarted=false,lf=options.legend.labelFormatter,s,label;for(var i=0;i<series.length;++i){s=series[i];if(s.label){label=lf?lf(s.label,s):s.label;if(label){entries.push({label:label,color:s.color})}}}if(options.legend.sorted){if($.isFunction(options.legend.sorted)){entries.sort(options.legend.sorted)}else if(options.legend.sorted=="reverse"){entries.reverse()}else{var ascending=options.legend.sorted!="descending";entries.sort(function(a,b){return a.label==b.label?0:a.label<b.label!=ascending?1:-1})}}for(var i=0;i<entries.length;++i){var entry=entries[i];if(i%options.legend.noColumns==0){if(rowStarted)fragments.push("</tr>");fragments.push("<tr>");rowStarted=true}fragments.push('<td class="legendColorBox"><div style="border:1px solid '+options.legend.labelBoxBorderColor+';padding:1px"><div style="width:4px;height:0;border:5px solid '+entry.color+';overflow:hidden"></div></div></td>'+'<td class="legendLabel">'+entry.label+"</td>")}if(rowStarted)fragments.push("</tr>");if(fragments.length==0)return;var table='<table style="font-size:smaller;color:'+options.grid.color+'">'+fragments.join("")+"</table>";if(options.legend.container!=null)$(options.legend.container).html(table);else{var pos="",p=options.legend.position,m=options.legend.margin;if(m[0]==null)m=[m,m];if(p.charAt(0)=="n")pos+="top:"+(m[1]+plotOffset.top)+"px;";else if(p.charAt(0)=="s")pos+="bottom:"+(m[1]+plotOffset.bottom)+"px;";if(p.charAt(1)=="e")pos+="right:"+(m[0]+plotOffset.right)+"px;";else if(p.charAt(1)=="w")pos+="left:"+(m[0]+plotOffset.left)+"px;";var legend=$('<div class="legend">'+table.replace('style="','style="position:absolute;'+pos+";")+"</div>").appendTo(placeholder);if(options.legend.backgroundOpacity!=0){var c=options.legend.backgroundColor;if(c==null){c=options.grid.backgroundColor;if(c&&typeof c=="string")c=$.color.parse(c);else c=$.color.extract(legend,"background-color");c.a=1;c=c.toString()}var div=legend.children();$('<div style="position:absolute;width:'+div.width()+"px;height:"+div.height()+"px;"+pos+"background-color:"+c+';"> </div>').prependTo(legend).css("opacity",options.legend.backgroundOpacity)}}}var highlights=[],redrawTimeout=null;function findNearbyItem(mouseX,mouseY,seriesFilter){var maxDistance=options.grid.mouseActiveRadius,smallestDistance=maxDistance*maxDistance+1,item=null,foundPoint=false,i,j,ps;for(i=series.length-1;i>=0;--i){if(!seriesFilter(series[i]))continue;var s=series[i],axisx=s.xaxis,axisy=s.yaxis,points=s.datapoints.points,mx=axisx.c2p(mouseX),my=axisy.c2p(mouseY),maxx=maxDistance/axisx.scale,maxy=maxDistance/axisy.scale;ps=s.datapoints.pointsize;if(axisx.options.inverseTransform)maxx=Number.MAX_VALUE;if(axisy.options.inverseTransform)maxy=Number.MAX_VALUE;if(s.lines.show||s.points.show){for(j=0;j<points.length;j+=ps){var x=points[j],y=points[j+1];if(x==null)continue;if(x-mx>maxx||x-mx<-maxx||y-my>maxy||y-my<-maxy)continue;var dx=Math.abs(axisx.p2c(x)-mouseX),dy=Math.abs(axisy.p2c(y)-mouseY),dist=dx*dx+dy*dy;if(dist<smallestDistance){smallestDistance=dist;item=[i,j/ps]}}}if(s.bars.show&&!item){var barLeft,barRight;switch(s.bars.align){case"left":barLeft=0;break;case"right":barLeft=-s.bars.barWidth;break;default:barLeft=-s.bars.barWidth/2}barRight=barLeft+s.bars.barWidth;for(j=0;j<points.length;j+=ps){var x=points[j],y=points[j+1],b=points[j+2];if(x==null)continue;if(series[i].bars.horizontal?mx<=Math.max(b,x)&&mx>=Math.min(b,x)&&my>=y+barLeft&&my<=y+barRight:mx>=x+barLeft&&mx<=x+barRight&&my>=Math.min(b,y)&&my<=Math.max(b,y))item=[i,j/ps]}}}if(item){i=item[0];j=item[1];ps=series[i].datapoints.pointsize;return{datapoint:series[i].datapoints.points.slice(j*ps,(j+1)*ps),dataIndex:j,series:series[i],seriesIndex:i}}return null}function onMouseMove(e){if(options.grid.hoverable)triggerClickHoverEvent("plothover",e,function(s){return s["hoverable"]!=false})}function onMouseLeave(e){if(options.grid.hoverable)triggerClickHoverEvent("plothover",e,function(s){return false})}function onClick(e){triggerClickHoverEvent("plotclick",e,function(s){return s["clickable"]!=false})}function triggerClickHoverEvent(eventname,event,seriesFilter){var offset=eventHolder.offset(),canvasX=event.pageX-offset.left-plotOffset.left,canvasY=event.pageY-offset.top-plotOffset.top,pos=canvasToAxisCoords({left:canvasX,top:canvasY});pos.pageX=event.pageX;pos.pageY=event.pageY;var item=findNearbyItem(canvasX,canvasY,seriesFilter);if(item){item.pageX=parseInt(item.series.xaxis.p2c(item.datapoint[0])+offset.left+plotOffset.left,10);item.pageY=parseInt(item.series.yaxis.p2c(item.datapoint[1])+offset.top+plotOffset.top,10)}if(options.grid.autoHighlight){for(var i=0;i<highlights.length;++i){var h=highlights[i];if(h.auto==eventname&&!(item&&h.series==item.series&&h.point[0]==item.datapoint[0]&&h.point[1]==item.datapoint[1]))unhighlight(h.series,h.point)}if(item)highlight(item.series,item.datapoint,eventname)}placeholder.trigger(eventname,[pos,item])}function triggerRedrawOverlay(){var t=options.interaction.redrawOverlayInterval;if(t==-1){drawOverlay();return}if(!redrawTimeout)redrawTimeout=setTimeout(drawOverlay,t)}function drawOverlay(){redrawTimeout=null;octx.save();overlay.clear();octx.translate(plotOffset.left,plotOffset.top);var i,hi;for(i=0;i<highlights.length;++i){hi=highlights[i];if(hi.series.bars.show)drawBarHighlight(hi.series,hi.point);else drawPointHighlight(hi.series,hi.point)}octx.restore();executeHooks(hooks.drawOverlay,[octx])}function highlight(s,point,auto){if(typeof s=="number")s=series[s];if(typeof point=="number"){var ps=s.datapoints.pointsize;point=s.datapoints.points.slice(ps*point,ps*(point+1))}var i=indexOfHighlight(s,point);if(i==-1){highlights.push({series:s,point:point,auto:auto});triggerRedrawOverlay()}else if(!auto)highlights[i].auto=false}function unhighlight(s,point){if(s==null&&point==null){highlights=[];triggerRedrawOverlay();return}if(typeof s=="number")s=series[s];if(typeof point=="number"){var ps=s.datapoints.pointsize;point=s.datapoints.points.slice(ps*point,ps*(point+1))}var i=indexOfHighlight(s,point);if(i!=-1){highlights.splice(i,1);triggerRedrawOverlay()}}function indexOfHighlight(s,p){for(var i=0;i<highlights.length;++i){var h=highlights[i];if(h.series==s&&h.point[0]==p[0]&&h.point[1]==p[1])return i}return-1}function drawPointHighlight(series,point){var x=point[0],y=point[1],axisx=series.xaxis,axisy=series.yaxis,highlightColor=typeof series.highlightColor==="string"?series.highlightColor:$.color.parse(series.color).scale("a",.5).toString();if(x<axisx.min||x>axisx.max||y<axisy.min||y>axisy.max)return;var pointRadius=series.points.radius+series.points.lineWidth/2;octx.lineWidth=pointRadius;octx.strokeStyle=highlightColor;var radius=1.5*pointRadius;x=axisx.p2c(x);y=axisy.p2c(y);octx.beginPath();if(series.points.symbol=="circle")octx.arc(x,y,radius,0,2*Math.PI,false);else series.points.symbol(octx,x,y,radius,false);octx.closePath();octx.stroke()}function drawBarHighlight(series,point){var highlightColor=typeof series.highlightColor==="string"?series.highlightColor:$.color.parse(series.color).scale("a",.5).toString(),fillStyle=highlightColor,barLeft;switch(series.bars.align){case"left":barLeft=0;break;case"right":barLeft=-series.bars.barWidth;break;default:barLeft=-series.bars.barWidth/2}octx.lineWidth=series.bars.lineWidth;octx.strokeStyle=highlightColor;drawBar(point[0],point[1],point[2]||0,barLeft,barLeft+series.bars.barWidth,function(){return fillStyle},series.xaxis,series.yaxis,octx,series.bars.horizontal,series.bars.lineWidth)}function getColorOrGradient(spec,bottom,top,defaultColor){if(typeof spec=="string")return spec;else{var gradient=ctx.createLinearGradient(0,top,0,bottom);for(var i=0,l=spec.colors.length;i<l;++i){var c=spec.colors[i];if(typeof c!="string"){var co=$.color.parse(defaultColor);if(c.brightness!=null)co=co.scale("rgb",c.brightness);if(c.opacity!=null)co.a*=c.opacity;c=co.toString()}gradient.addColorStop(i/(l-1),c)}return gradient}}}$.plot=function(placeholder,data,options){var plot=new Plot($(placeholder),data,options,$.plot.plugins);return plot};$.plot.version="0.8.3";$.plot.plugins=[];$.fn.plot=function(data,options){return this.each(function(){$.plot(this,data,options)})};function floorInBase(n,base){return base*Math.floor(n/base)}})(jQuery);
/* Javascript plotting library for jQuery, version 0.8.3.

Copyright (c) 2007-2014 IOLA and Ole Laursen.
Licensed under the MIT license.

*/
(function($,e,t){"$:nomunge";var i=[],n=$.resize=$.extend($.resize,{}),a,r=false,s="setTimeout",u="resize",m=u+"-special-event",o="pendingDelay",l="activeDelay",f="throttleWindow";n[o]=200;n[l]=20;n[f]=true;$.event.special[u]={setup:function(){if(!n[f]&&this[s]){return false}var e=$(this);i.push(this);e.data(m,{w:e.width(),h:e.height()});if(i.length===1){a=t;h()}},teardown:function(){if(!n[f]&&this[s]){return false}var e=$(this);for(var t=i.length-1;t>=0;t--){if(i[t]==this){i.splice(t,1);break}}e.removeData(m);if(!i.length){if(r){cancelAnimationFrame(a)}else{clearTimeout(a)}a=null}},add:function(e){if(!n[f]&&this[s]){return false}var i;function a(e,n,a){var r=$(this),s=r.data(m)||{};s.w=n!==t?n:r.width();s.h=a!==t?a:r.height();i.apply(this,arguments)}if($.isFunction(e)){i=e;return a}else{i=e.handler;e.handler=a}}};function h(t){if(r===true){r=t||1}for(var s=i.length-1;s>=0;s--){var l=$(i[s]);if(l[0]==e||l.is(":visible")){var f=l.width(),c=l.height(),d=l.data(m);if(d&&(f!==d.w||c!==d.h)){l.trigger(u,[d.w=f,d.h=c]);r=t||true}}else{d=l.data(m);d.w=0;d.h=0}}if(a!==null){if(r&&(t==null||t-r<1e3)){a=e.requestAnimationFrame(h)}else{a=setTimeout(h,n[o]);r=false}}}if(!e.requestAnimationFrame){e.requestAnimationFrame=function(){return e.webkitRequestAnimationFrame||e.mozRequestAnimationFrame||e.oRequestAnimationFrame||e.msRequestAnimationFrame||function(t,i){return e.setTimeout(function(){t((new Date).getTime())},n[l])}}()}if(!e.cancelAnimationFrame){e.cancelAnimationFrame=function(){return e.webkitCancelRequestAnimationFrame||e.mozCancelRequestAnimationFrame||e.oCancelRequestAnimationFrame||e.msCancelRequestAnimationFrame||clearTimeout}()}})(jQuery,this);(function($){var options={};function init(plot){function onResize(){var placeholder=plot.getPlaceholder();if(placeholder.width()==0||placeholder.height()==0)return;plot.resize();plot.setupGrid();plot.draw()}function bindEvents(plot,eventHolder){plot.getPlaceholder().resize(onResize)}function shutdown(plot,eventHolder){plot.getPlaceholder().unbind("resize",onResize)}plot.hooks.bindEvents.push(bindEvents);plot.hooks.shutdown.push(shutdown)}$.plot.plugins.push({init:init,options:options,name:"resize",version:"1.0"})})(jQuery);
/*
Axis Labels Plugin for flot.
http://github.com/markrcote/flot-axislabels
Original code is Copyright (c) 2010 Xuan Luo.
Original code was released under the GPLv3 license by Xuan Luo, September 2010.
Original code was rereleased under the MIT license by Xuan Luo, April 2012.
Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:
The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

(function ($) {
    var options = {
      axisLabels: {
        show: true
      }
    };

    function canvasSupported() {
        return !!document.createElement('canvas').getContext;
    }

    function canvasTextSupported() {
        if (!canvasSupported()) {
            return false;
        }
        var dummy_canvas = document.createElement('canvas');
        var context = dummy_canvas.getContext('2d');
        return typeof context.fillText == 'function';
    }

    function css3TransitionSupported() {
        var div = document.createElement('div');
        return typeof div.style.MozTransition != 'undefined'    // Gecko
            || typeof div.style.OTransition != 'undefined'      // Opera
            || typeof div.style.webkitTransition != 'undefined' // WebKit
            || typeof div.style.transition != 'undefined';
    }


    function AxisLabel(axisName, position, padding, plot, opts) {
        this.axisName = axisName;
        this.position = position;
        this.padding = padding;
        this.plot = plot;
        this.opts = opts;
        this.width = 0;
        this.height = 0;
    }

    AxisLabel.prototype.cleanup = function() {
    };


    CanvasAxisLabel.prototype = new AxisLabel();
    CanvasAxisLabel.prototype.constructor = CanvasAxisLabel;
    function CanvasAxisLabel(axisName, position, padding, plot, opts) {
        AxisLabel.prototype.constructor.call(this, axisName, position, padding,
                                             plot, opts);
    }

    CanvasAxisLabel.prototype.calculateSize = function() {
        if (!this.opts.axisLabelFontSizePixels)
            this.opts.axisLabelFontSizePixels = 14;
        if (!this.opts.axisLabelFontFamily)
            this.opts.axisLabelFontFamily = 'sans-serif';

        var textWidth = this.opts.axisLabelFontSizePixels + this.padding;
        var textHeight = this.opts.axisLabelFontSizePixels + this.padding;
        if (this.position == 'left' || this.position == 'right') {
            this.width = this.opts.axisLabelFontSizePixels + this.padding;
            this.height = 0;
        } else {
            this.width = 0;
            this.height = this.opts.axisLabelFontSizePixels + this.padding;
        }
    };

    CanvasAxisLabel.prototype.draw = function(box) {
        if (!this.opts.axisLabelColour)
            this.opts.axisLabelColour = 'black';
        var ctx = this.plot.getCanvas().getContext('2d');
        ctx.save();
        ctx.font = this.opts.axisLabelFontSizePixels + 'px ' +
            this.opts.axisLabelFontFamily;
        ctx.fillStyle = this.opts.axisLabelColour;
        var width = ctx.measureText(this.opts.axisLabel).width;
        var height = this.opts.axisLabelFontSizePixels;
        var x, y, angle = 0;
        if (this.position == 'top') {
            x = box.left + box.width/2 - width/2;
            y = box.top + height*0.72;
        } else if (this.position == 'bottom') {
            x = box.left + box.width/2 - width/2;
            y = box.top + box.height - height*0.72;
        } else if (this.position == 'left') {
            x = box.left + height*0.72;
            y = box.height/2 + box.top + width/2;
            angle = -Math.PI/2;
        } else if (this.position == 'right') {
            x = box.left + box.width - height*0.72;
            y = box.height/2 + box.top - width/2;
            angle = Math.PI/2;
        }
        ctx.translate(x, y);
        ctx.rotate(angle);
        ctx.fillText(this.opts.axisLabel, 0, 0);
        ctx.restore();
    };


    HtmlAxisLabel.prototype = new AxisLabel();
    HtmlAxisLabel.prototype.constructor = HtmlAxisLabel;
    function HtmlAxisLabel(axisName, position, padding, plot, opts) {
        AxisLabel.prototype.constructor.call(this, axisName, position,
                                             padding, plot, opts);
        this.elem = null;
    }

    HtmlAxisLabel.prototype.calculateSize = function() {
        var elem = $('<div class="axisLabels" style="position:absolute;">' +
                     this.opts.axisLabel + '</div>');
        this.plot.getPlaceholder().append(elem);
        // store height and width of label itself, for use in draw()
        this.labelWidth = elem.outerWidth(true);
        this.labelHeight = elem.outerHeight(true);
        elem.remove();

        this.width = this.height = 0;
        if (this.position == 'left' || this.position == 'right') {
            this.width = this.labelWidth + this.padding;
        } else {
            this.height = this.labelHeight + this.padding;
        }
    };

    HtmlAxisLabel.prototype.cleanup = function() {
        if (this.elem) {
            this.elem.remove();
        }
    };

    HtmlAxisLabel.prototype.draw = function(box) {
        this.plot.getPlaceholder().find('#' + this.axisName + 'Label').remove();
        this.elem = $('<div id="' + this.axisName +
                      'Label" " class="axisLabels" style="position:absolute;">'
                      + this.opts.axisLabel + '</div>');
        this.plot.getPlaceholder().append(this.elem);
        if (this.position == 'top') {
            this.elem.css('left', box.left + box.width/2 - this.labelWidth/2 +
                          'px');
            this.elem.css('top', box.top + 'px');
        } else if (this.position == 'bottom') {
            this.elem.css('left', box.left + box.width/2 - this.labelWidth/2 +
                          'px');
            this.elem.css('top', box.top + box.height - this.labelHeight +
                          'px');
        } else if (this.position == 'left') {
            this.elem.css('top', box.top + box.height/2 - this.labelHeight/2 +
                          'px');
            this.elem.css('left', box.left + 'px');
        } else if (this.position == 'right') {
            this.elem.css('top', box.top + box.height/2 - this.labelHeight/2 +
                          'px');
            this.elem.css('left', box.left + box.width - this.labelWidth +
                          'px');
        }
    };


    CssTransformAxisLabel.prototype = new HtmlAxisLabel();
    CssTransformAxisLabel.prototype.constructor = CssTransformAxisLabel;
    function CssTransformAxisLabel(axisName, position, padding, plot, opts) {
        HtmlAxisLabel.prototype.constructor.call(this, axisName, position,
                                                 padding, plot, opts);
    }

    CssTransformAxisLabel.prototype.calculateSize = function() {
        HtmlAxisLabel.prototype.calculateSize.call(this);
        this.width = this.height = 0;
        if (this.position == 'left' || this.position == 'right') {
            this.width = this.labelHeight + this.padding;
        } else {
            this.height = this.labelHeight + this.padding;
        }
    };

    CssTransformAxisLabel.prototype.transforms = function(degrees, x, y) {
        var stransforms = {
            '-moz-transform': '',
            '-webkit-transform': '',
            '-o-transform': '',
            '-ms-transform': ''
        };
        if (x != 0 || y != 0) {
            var stdTranslate = ' translate(' + x + 'px, ' + y + 'px)';
            stransforms['-moz-transform'] += stdTranslate;
            stransforms['-webkit-transform'] += stdTranslate;
            stransforms['-o-transform'] += stdTranslate;
            stransforms['-ms-transform'] += stdTranslate;
        }
        if (degrees != 0) {
            var rotation = degrees / 90;
            var stdRotate = ' rotate(' + degrees + 'deg)';
            stransforms['-moz-transform'] += stdRotate;
            stransforms['-webkit-transform'] += stdRotate;
            stransforms['-o-transform'] += stdRotate;
            stransforms['-ms-transform'] += stdRotate;
        }
        var s = 'top: 0; left: 0; ';
        for (var prop in stransforms) {
            if (stransforms[prop]) {
                s += prop + ':' + stransforms[prop] + ';';
            }
        }
        s += ';';
        return s;
    };

    CssTransformAxisLabel.prototype.calculateOffsets = function(box) {
        var offsets = { x: 0, y: 0, degrees: 0 };
        if (this.position == 'bottom') {
            offsets.x = box.left + box.width/2 - this.labelWidth/2;
            offsets.y = box.top + box.height - this.labelHeight;
        } else if (this.position == 'top') {
            offsets.x = box.left + box.width/2 - this.labelWidth/2;
            offsets.y = box.top;
        } else if (this.position == 'left') {
            offsets.degrees = -90;
            offsets.x = box.left - this.labelWidth/2 + this.labelHeight/2;
            offsets.y = box.height/2 + box.top;
        } else if (this.position == 'right') {
            offsets.degrees = 90;
            offsets.x = box.left + box.width - this.labelWidth/2
                        - this.labelHeight/2;
            offsets.y = box.height/2 + box.top;
        }
        offsets.x = Math.round(offsets.x);
        offsets.y = Math.round(offsets.y);

        return offsets;
    };

    CssTransformAxisLabel.prototype.draw = function(box) {
        this.plot.getPlaceholder().find("." + this.axisName + "Label").remove();
        var offsets = this.calculateOffsets(box);
        this.elem = $('<div class="axisLabels ' + this.axisName +
                      'Label" style="position:absolute; ' +
                      this.transforms(offsets.degrees, offsets.x, offsets.y) +
                      '">' + this.opts.axisLabel + '</div>');
        this.plot.getPlaceholder().append(this.elem);
    };


    IeTransformAxisLabel.prototype = new CssTransformAxisLabel();
    IeTransformAxisLabel.prototype.constructor = IeTransformAxisLabel;
    function IeTransformAxisLabel(axisName, position, padding, plot, opts) {
        CssTransformAxisLabel.prototype.constructor.call(this, axisName,
                                                         position, padding,
                                                         plot, opts);
        this.requiresResize = false;
    }

    IeTransformAxisLabel.prototype.transforms = function(degrees, x, y) {
        // I didn't feel like learning the crazy Matrix stuff, so this uses
        // a combination of the rotation transform and CSS positioning.
        var s = '';
        if (degrees != 0) {
            var rotation = degrees/90;
            while (rotation < 0) {
                rotation += 4;
            }
            s += ' filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=' + rotation + '); ';
            // see below
            this.requiresResize = (this.position == 'right');
        }
        if (x != 0) {
            s += 'left: ' + x + 'px; ';
        }
        if (y != 0) {
            s += 'top: ' + y + 'px; ';
        }
        return s;
    };

    IeTransformAxisLabel.prototype.calculateOffsets = function(box) {
        var offsets = CssTransformAxisLabel.prototype.calculateOffsets.call(
                          this, box);
        // adjust some values to take into account differences between
        // CSS and IE rotations.
        if (this.position == 'top') {
            // FIXME: not sure why, but placing this exactly at the top causes
            // the top axis label to flip to the bottom...
            offsets.y = box.top + 1;
        } else if (this.position == 'left') {
            offsets.x = box.left;
            offsets.y = box.height/2 + box.top - this.labelWidth/2;
        } else if (this.position == 'right') {
            offsets.x = box.left + box.width - this.labelHeight;
            offsets.y = box.height/2 + box.top - this.labelWidth/2;
        }
        return offsets;
    };

    IeTransformAxisLabel.prototype.draw = function(box) {
        CssTransformAxisLabel.prototype.draw.call(this, box);
        if (this.requiresResize) {
            this.elem = this.plot.getPlaceholder().find("." + this.axisName +
                                                        "Label");
            // Since we used CSS positioning instead of transforms for
            // translating the element, and since the positioning is done
            // before any rotations, we have to reset the width and height
            // in case the browser wrapped the text (specifically for the
            // y2axis).
            this.elem.css('width', this.labelWidth);
            this.elem.css('height', this.labelHeight);
        }
    };


    function init(plot) {
        plot.hooks.processOptions.push(function (plot, options) {

            if (!options.axisLabels.show)
                return;

            // This is kind of a hack. There are no hooks in Flot between
            // the creation and measuring of the ticks (setTicks, measureTickLabels
            // in setupGrid() ) and the drawing of the ticks and plot box
            // (insertAxisLabels in setupGrid() ).
            //
            // Therefore, we use a trick where we run the draw routine twice:
            // the first time to get the tick measurements, so that we can change
            // them, and then have it draw it again.
            var secondPass = false;

            var axisLabels = {};
            var axisOffsetCounts = { left: 0, right: 0, top: 0, bottom: 0 };

            var defaultPadding = 2;  // padding between axis and tick labels
            plot.hooks.draw.push(function (plot, ctx) {
                var hasAxisLabels = false;
                if (!secondPass) {
                    // MEASURE AND SET OPTIONS
                    $.each(plot.getAxes(), function(axisName, axis) {
                        var opts = axis.options // Flot 0.7
                            || plot.getOptions()[axisName]; // Flot 0.6

                        // Handle redraws initiated outside of this plug-in.
                        if (axisName in axisLabels) {
                            axis.labelHeight = axis.labelHeight -
                                axisLabels[axisName].height;
                            axis.labelWidth = axis.labelWidth -
                                axisLabels[axisName].width;
                            opts.labelHeight = axis.labelHeight;
                            opts.labelWidth = axis.labelWidth;
                            axisLabels[axisName].cleanup();
                            delete axisLabels[axisName];
                        }

                        if (!opts || !opts.axisLabel || !axis.show)
                            return;

                        hasAxisLabels = true;
                        var renderer = null;

                        if (!opts.axisLabelUseHtml &&
                            navigator.appName == 'Microsoft Internet Explorer') {
                            var ua = navigator.userAgent;
                            var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                            if (re.exec(ua) != null) {
                                rv = parseFloat(RegExp.$1);
                            }
                            if (rv >= 9 && !opts.axisLabelUseCanvas && !opts.axisLabelUseHtml) {
                                renderer = CssTransformAxisLabel;
                            } else if (!opts.axisLabelUseCanvas && !opts.axisLabelUseHtml) {
                                renderer = IeTransformAxisLabel;
                            } else if (opts.axisLabelUseCanvas) {
                                renderer = CanvasAxisLabel;
                            } else {
                                renderer = HtmlAxisLabel;
                            }
                        } else {
                            if (opts.axisLabelUseHtml || (!css3TransitionSupported() && !canvasTextSupported()) && !opts.axisLabelUseCanvas) {
                                renderer = HtmlAxisLabel;
                            } else if (opts.axisLabelUseCanvas || !css3TransitionSupported()) {
                                renderer = CanvasAxisLabel;
                            } else {
                                renderer = CssTransformAxisLabel;
                            }
                        }

                        var padding = opts.axisLabelPadding === undefined ?
                                      defaultPadding : opts.axisLabelPadding;

                        axisLabels[axisName] = new renderer(axisName,
                                                            axis.position, padding,
                                                            plot, opts);

                        // flot interprets axis.labelHeight and .labelWidth as
                        // the height and width of the tick labels. We increase
                        // these values to make room for the axis label and
                        // padding.

                        axisLabels[axisName].calculateSize();

                        // AxisLabel.height and .width are the size of the
                        // axis label and padding.
                        // Just set opts here because axis will be sorted out on
                        // the redraw.

                        opts.labelHeight = axis.labelHeight +
                            axisLabels[axisName].height;
                        opts.labelWidth = axis.labelWidth +
                            axisLabels[axisName].width;
                    });

                    // If there are axis labels, re-draw with new label widths and
                    // heights.

                    if (hasAxisLabels) {
                        secondPass = true;
                        plot.setupGrid();
                        plot.draw();
                    }
                } else {
                    secondPass = false;
                    // DRAW
                    $.each(plot.getAxes(), function(axisName, axis) {
                        var opts = axis.options // Flot 0.7
                            || plot.getOptions()[axisName]; // Flot 0.6
                        if (!opts || !opts.axisLabel || !axis.show)
                            return;

                        axisLabels[axisName].draw(axis.box);
                    });
                }
            });
        });
    }


    $.plot.plugins.push({
        init: init,
        options: options,
        name: 'axisLabels',
        version: '2.0'
    });
})(jQuery);
//# sourceMappingURL=elix_profile.js.map
