/**
 * Uzdevumu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var TaskLogic = function()
{    
    /**
     * Callback function which will be executed after sucessfull delegation
     * @type type
     */
    var delegateCallback = null;
    
    /**
     * Callback function which will be executed on succesfull Yes or No decision
     * @type type
     */
    var decisionCallback = null;
    
    /**
     * Uzdevuma deleģēšana
     * 
     * @param {object} frm_deleg Deleģēšanas formas HTML objekts
     * @returns {undefined}
     */
    var delegateTask = function(frm_deleg)
    {        
        var formData = getDelegateSavingData(frm_deleg);
        
        if (formData == null) {
            return;
        }
        
        var request = new FormAjaxRequest("save_delegate", "", "", formData);

        request.callback = function(data) {
            var frm_id = frm_deleg.attr('dx_frm_uniq_id');
            $('#item_edit_form_' + frm_id).find('input[name=task_status]').val(data.status);
            
            var grid_htm_id = frm_deleg.attr('dx_grid_htm_id');
            if (grid_htm_id) {
                reload_grid(grid_htm_id);
            }
            
            if (delegateCallback) {
                delegateCallback.call(this, frm_deleg.attr('dx_task_id'), data.status);
            }
            
            getDelegatedTasks(frm_deleg, frm_deleg.find('.dx-tab-tasks'));
            
            notify_info(Lang.get('task_form.notify_task_delegated'));
        };
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    /**
     * Saglabā uzdevuma izpildes/noraidīšanas rezultātu
     * 
     * @param {string} save_url Rezultāta veids: task_yes vai task_no
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var saveTask = function(save_url, frm){            
        var form_id = 'list_item_view_form_' + frm.attr('dx_frm_uniq_id');

        var formData = getTaskSavingData(form_id, save_url);
        
        if (formData == null) {
            return;
        }
        
        var request = new FormAjaxRequest(save_url, form_id, frm.attr('dx_grid_htm_id'), formData);

        request.callback = function(data) {
            displaySavingInfo(frm, data);
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    /**
     * Validē deleģējamā uzdevuma datus un atgriež saglabājamo datu objektu
     * 
     * @param {string} form_id Delegēšanas uzdevuma HTML formas elementa ID
     * @returns {FormData} Saglabāšanas datu objekts vai null, ja nav norādītas obligātās vērtības
     */
    var getDelegateSavingData = function(frm_deleg) {
        
        var data = frm_deleg.find("input[name=deleg_empl_txt]").select2('data');
            
        if (!data || data.id == 0) {
            notify_err(Lang.get('task_form.notify_err_provide_employee'));
            return null;
        }
            
        var employee_id = data.id;
                
        var task_txt = frm_deleg.find("textarea[name=task_txt]").val();
        
        if (task_txt.length == 0) {
            notify_err(Lang.get('task_form.notify_err_provide_description'));
            frm_deleg.find("textarea[name=task_txt]").focus();            
            return null;
        }
        
        var formData = new FormData();
        formData.append("employee_id", employee_id);
        formData.append("task_txt", task_txt);
        formData.append("task_id", frm_deleg.attr('dx_task_id'));
        formData.append("due_date", frm_deleg.find("input[name=due_date]").val());
        
        return formData;
    };
    
    /**
     * Validē saglabājamā uzdevuma datus un atgriež saglabājamo datu objektu
     * 
     * @param {string} form_id Uzdevuma HTML formas elementa ID
     * @param {string} save_url Izvēlētā darbība: task_yes vai task_no
     * @returns {FormData} Saglabāšanas datu objekts vai null, ja noraidīšanas gadījuma nav norādīts komentārs
     */
    var getTaskSavingData = function(form_id, save_url) {
        var comm = $( "#" + form_id  + " textarea[name='task_comment']").val().trim();

        if (save_url == "task_no" && comm.length == 0)
        {
            notify_err(Lang.get('task_form.err_comment_required'));
            $( "#" + form_id  + " textarea[name='task_comment']").focus();            
            return null;
        }

        var item_id = $( "#" + form_id  + " input[name='item_id']").val();

        var formData = new FormData();
        formData.append("task_comment", comm);
        formData.append("item_id", item_id);
        
        return formData;
    };
    
    /**
     * Attēlo uzdevuma saglabāšanas rezultātu - parāda paslēpj pogas, pārlādē uzdevumu sarakstu, atjaunina galvenājā logā informāciju par uzdevumu skaitu
     * 
     * @param {object} frm Uzdevuma formas elements
     * @param {object} data UZdevuma saglabāšanas JSON rezultāts
     * @returns {undefined}
     */
    var displaySavingInfo = function(frm, data) {
        try {
            if (data['success'] == 1){                  
                var form_id = 'list_item_view_form_' + frm.attr('dx_frm_uniq_id');
                
                var grid_htm_id = frm.attr('dx_grid_htm_id');
                if (grid_htm_id)
                {
                    reload_grid(grid_htm_id);
                }
        
                $( "#" + form_id  + " input[name='task_status']").val(data['task_status']);
                $( "#" + form_id  + " textarea[name='task_comment']").attr('disabled','disabled');
                $( "#btns_sec_" + frm.attr('dx_frm_uniq_id')).hide();
                
                updateMenuTasksCounter(data['tasks_count']);
                                
                if (decisionCallback) {
                    decisionCallback.call(this, frm.attr('dx_task_id'));
                }

                notify_info(Lang.get('task_form.notify_saved'));
            } 
            else
            {             	
                notify_err(data['error']);
            }
        }
        catch (err){
            notify_err(escapeHtml(err));
        }
    };
    
    /**
     * Updates total task count in TOP menu badge and displays congratulation message if all tasks are done
     * @param {integer} cnt Total tasks count
     * @returns {undefined}
     */
    var updateMenuTasksCounter = function(cnt) {
        if (cnt == 0)
        {
            $("#dx_tasks_count_li").hide();
            setTimeout(function() {
                    $.gritter.add({
                        title: Lang.get('task_form.congrat_title'),
                        text: '<i class="fa fa-thumbs-o-up"></i> ' + Lang.get('task_form.congrat_all_done'),
                        time: 7000
                    });
            }, 3000);
        }
        else
        {
            $("#dx_tasks_count_badge").html(cnt);
        }
    };
    
    /**
     * Uzdevumam piesaistītā dokumenta atvēršanas pogas funkcionalitāte
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleOpenItemBtnClick = function(frm){       
        frm.find('.dx-cms-task-btn-open-item').click(function(){
            rel_new_form(frm.attr('dx_form_url'), frm.attr('dx_rel_list_id'), frm.attr('dx_item_id'), frm.attr('dx_rel_field_id'), 'list_item_view_form_' + frm.attr('dx_frm_uniq_id'), "");
        });  
    };
    
    /**
     * Deleģēšanas pogas funkcionalitāte - atver deleģēšanas formu
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleDelegateBtnClick = function(frm){       
        frm.find('.dx-cms-task-btn-delegate').click(function(e){
            e.stopPropagation();     
            var frm_deleg = $("#form_delegate_" + frm.attr('dx_frm_uniq_id'));
            
            if (frm_deleg.attr('dx_is_init') == "0") {
                initFormDelegate(frm_deleg);
                
                var el_task_text = frm.find("textarea[name=task_txt]");
                var task_text = frm.find("input[name=task_title]").val();
                if (el_task_text.length > 0 && el_task_text.val()) {
                    task_text = el_task_text.val();
                }                
                
                frm_deleg.find("textarea[name=task_txt]").html(task_text);
                getDelegatedTasks(frm_deleg, frm_deleg.find('.dx-tab-tasks'));
            }            
            
            frm_deleg.modal('show');
            
            setTimeout(function() {
                frm_deleg.find("select[name=employee_id]").focus();
            }, 1000);
        });  
    };
    
     /**
     * Izpildīšanas pogas funkcionalitāte
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleYesBtnClick = function(frm){       
        frm.find('.dx-cms-task-btn-yes').click(function(e){
            e.stopPropagation();
                    
            saveTask('task_yes', frm);
        });  
    };
    
     /**
     * Noraidīšanas pogas funkcionalitāte
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleNoBtnClick = function(frm){       
        frm.find('.dx-cms-task-btn-no').click(function(e){
            e.stopPropagation();
                    
            saveTask('task_no', frm);
        });  
    };
    
    /**
     * Uzstāda ritjoslu modālajam uzdevuma logam
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleModalScrollbar = function(frm) {
        frm.on('show.bs.modal', function () {
            frm.find('.modal-body').css('overflow-y', 'auto');
	});
    };
    
    /**
     * Apstrādā uzdevuma formas aizvēršanu - izņem pārlūkā ielādēto HTML
     * Ja forma bija atvērta no saraksta, tad iespējo saraksta funkcionalitāti
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleModalHide = function(frm) {
        frm.on('hidden.bs.modal', function (e) {			
	
            var grid_id = frm.attr('dx_grid_htm_id');
            
            if (grid_id) {
                stop_executing(grid_id);
            }
             
            setTimeout(function() {                
                frm.remove();
            }, 200);
	});  
    };
    
    /**
     * Deleģēšanas formas delegēšanas pogas funkcionalitāte
     * 
     * @param {object} frm_deleg Deleģēšanas formas HTML objekts
     * @returns {undefined}
     */
    var handleDelegFormBtnDelegClick = function(frm_deleg) {
        frm_deleg.find(".dx-cms-task-btn-delegate").click(function(e) {
            e.stopPropagation();
            frm_deleg.find("form").validator('validate');
            
            delegateTask(frm_deleg);
        });
    };
    
    /**
     * Inicializē termiņa datuma lauka funkcionalitāti
     * 
     * @param {object} frm_deleg Deleģēšanas formas HTML objekts
     * @returns {undefined}
     */
    var initDueDateField = function(frm_deleg) {
        
        frm_deleg.find(".dx-cms-date-field input[name=due_date]").datetimepicker({
            lang: Lang.getLocale(),
            format: frm_deleg.attr('dx_date_format'),
            timepicker: false,
            dayOfWeekStart: 1,
            closeOnDateSelect: true
        });
        
        frm_deleg.find(".dx-cms-date-field button").click(function(){            
            frm_deleg.find(".dx-cms-date-field input[name=due_date]").datetimepicker('show');
        });
    };
    
    /**
     * Uzstāda deleģēšanas formai datu validātoru
     * 
     * @param {object} frm_deleg Deleģēšanas formas HTML objekts
     * @returns {undefined}
     */
    var initDelegFormValidator = function(frm_deleg) {
        frm_deleg.find("form").validator({
            custom : {
                foo: function($el) 
                { 
                    if (!($el.val()>0) && $el.attr('required'))
                    {
                        return false;
                    }
                    return true;
                }
            },
            errors: {
                foo: Lang.get('task_form.err_value_not_set')
            },
            feedback: {
                success: 'glyphicon-ok',
                error: 'glyphicon-alert'
            }
        });    
    };
    
    /**
     * Handles event when delegate task or delegated tasks list tab is clicked (swith panes)
     * 
     * @param {object} frm_deleg Delegate form's HTML element
     * @returns {undefined}
     */
    var handleDelegateTabClick = function(frm_deleg) {
        frm_deleg.find("li.dx-tasks-list a").click(function() {
            frm_deleg.find("button.dx-cms-task-btn-delegate").hide();
            
            frm_deleg.find(".dx-tab-new-task").removeClass("active");
            frm_deleg.find('.dx-tab-tasks').addClass("active");
                
            var tab_tasks = frm_deleg.find('.dx-tab-tasks');
            
            if (tab_tasks.html().length == 0) {
                getDelegatedTasks(frm_deleg, tab_tasks);
            }
        });
        
        frm_deleg.find("li.dx-tasks-create a").click(function() {
            showNewDelegteTab(frm_deleg);
        });
    };
    
    /**
     * Switch to new delegated task entering tab pane
     * 
     * @param {object} frm_deleg Delegate form's HTML element
     * @returns {undefined}
     */
    var showNewDelegteTab = function(frm_deleg) {
        frm_deleg.find("button.dx-cms-task-btn-delegate").show();
            
        frm_deleg.find(".dx-tab-new-task").addClass("active");
        frm_deleg.find('.dx-tab-tasks').removeClass("active");
        
        frm_deleg.find("li.dx-tasks-create").addClass("active").find("a").attr("aria-expanded", "true");
        frm_deleg.find("li.dx-tasks-list").removeClass("active").find("a").attr("aria-expanded", "false");
    };
    
    /**
     * Retrieves delegated tasks from the server
     * 
     * @param {object} frm_deleg Delegate form's HTML element
     * @param {object} tab_tasks Delegate form's tasks list tab HTML element
     * @returns {undefined}
     */
    var getDelegatedTasks = function(frm_deleg, tab_tasks) {
        var formData = new FormData();
        formData.append("task_id", frm_deleg.attr('dx_task_id'));
        
        var request = new FormAjaxRequest("tasks/get_delegated", "", "", formData);

        request.callback = function(data) {
            tab_tasks.html(data.html);
            frm_deleg.find("span.dx-task-count").html(data.count); 
            handleDelegatedRevoke(frm_deleg);
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    var handleDelegatedRevoke = function(frm_deleg) {
        frm_deleg.find('a.dx-revoke-task').click(function() {
            PageMain.showConfirm(revokeDelegatedTask, {task_id: $(this).data('task-id'), frm: frm_deleg, self: $(this)}, null, Lang.get('task_form.msg_confirm_delegate_revoke'), Lang.get('task_form.confirm_yes'), Lang.get('task_form.confirm_no'));
        });
    };
    
    var revokeDelegatedTask = function(data) {
        var formData = new FormData();
        formData.append("task_id", data.task_id);
        
        var request = new FormAjaxRequest("tasks/cancel_delegated", "", "", formData);

        request.callback = function(result) {            
            data.self.hide();
            data.self.parent().find("span.dx-task-status").html(result.status).css('background-color', result.color); 
            notify_info(Lang.get('task_form.notify_task_canceled'));
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    /**
     * Iestata darbinieku (kuriem tiks deleģēts uzdevums) meklēšanas lauku
     * 
     * @param {object} frm_deleg Delegēšanas formas objekts
     * @returns {undefined}
     */
    var initEmployeeLookup = function(frm_deleg) {
        
        if (parseInt(frm_deleg.attr("data-is-any-delegate"))) {            
        
            frm_deleg.find("input[name=deleg_empl_txt]").select2({
                placeholder: Lang.get('workflow.wf_init_add_form_employee_search_placeholder'),
                minimumInputLength: 3,
                ajax: {
                    type: 'POST',
                    url: DX_CORE.site_url  + 'workflow_find_approver',
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    quietMillis: 250,
                    cache: true,
                    data: function (term, page) {
                        return {
                            q: term
                        };
                    },
                    results: function (data, page) {                 
                        // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to alter the remote JSON data

                        if (data.success == 1)
                        {
                            return { results: data.data};
                        }
                        else
                        {
                            if (data.error)
                            {
                                    notify_err(data.error);
                            }
                            else
                            {
                                    notify_err(frm_appr.attr('dx_system_error'));
                            }
                        }
                    }
                }
            });  
        }
        else {
            var data = jQuery.parseJSON( frm_deleg.attr("data-subordinates") ); //[{ id: 0, text: 'enhancement' }, { id: 1, text: 'bug' }, { id: 2, text: 'duplicate' }, { id: 3, text: 'invalid' }, { id: 4, text: 'wontfix' }];

            frm_deleg.find("input[name=deleg_empl_txt]").select2({
                placeholder: Lang.get('workflow.wf_init_add_form_employee_search_placeholder'),
                minimumInputLength: 0,
                data: data
            });
        }
    };
    
    /**
     * Inicializē uzdevuma deleģēšanas formu
     * 
     * @param {object} frm_deleg Delegēšanas formas objekts
     * @returns {undefined}
     */
    var initFormDelegate = function(frm_deleg) {
        initEmployeeLookup(frm_deleg);
        handleDelegFormBtnDelegClick(frm_deleg);
        initDelegFormValidator(frm_deleg);
        initDueDateField(frm_deleg);
        handleDelegateTabClick(frm_deleg);
        
        frm_deleg.attr('dx_is_init', 1); // uzstādam pazīmi, ka forma inicializēta
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializētās uzdevumu formas
     * 
     * @returns {undefined}
     */
    var initForm = function()
    {        
        $(".dx-cms-task-form[dx_is_init='0']").each(function() {
            
            handleModalScrollbar($(this));
            handleModalHide($(this));
            
            handleOpenItemBtnClick($(this));            
            handleDelegateBtnClick($(this));
            handleYesBtnClick($(this));
            handleNoBtnClick($(this));
            
            $(this).attr('dx_is_init', 1); // uzstādam pazīmi, ka forma inicializēta
            
            $(this).modal('show');
        });
        
    };

    return {
        init: function() {
            initForm();
        },
        initFormDelegate: function(frm_deleg) {
            initFormDelegate(frm_deleg);
        },
        setDelegateCallback: function(callback) {
            delegateCallback = callback;
        },
        setDecisionCallback: function(callback) {
            decisionCallback = callback;
        },
        updateMenuTasksCounter: function(cnt) {
            updateMenuTasksCounter(cnt);
        },
        fillDelegatedTasks: function(frm_deleg, tab_tasks) {
            getDelegatedTasks(frm_deleg, tab_tasks);
        },
        showNewDelegteTab: function(frm_deleg) {
            showNewDelegteTab(frm_deleg);
        },
        initEmployeeLookup: function(frm_deleg) {
            initEmployeeLookup(frm_deleg);
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {            
    TaskLogic.init();           
});