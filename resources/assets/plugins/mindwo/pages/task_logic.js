/**
 * Uzdevumu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var TaskLogic = function()
{    
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
            reload_grid(frm_deleg.attr('dx_grid_htm_id'));
            
            notify_info("Uzdevums veiksmīgi deleģēts!");
        };
        
        // izpildam AJAX pieprasījumu
        request.doRequest();
    }
    
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
        var employee_id = frm_deleg.find("select[name=employee_id]").val();
        
        if (employee_id == 0) {
            notify_err("Jānorāda darbinieks, kuram tiks deleģēts uzdevums!");
            frm_deleg.find("select[name=employee_id]").focus();            
            return null;
        }
        
        var task_txt = frm_deleg.find("textarea[name=task_txt]").val();
        
        if (task_txt.length == 0) {
            notify_err("Jānorāda uzdevuma apraksts!");
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
            notify_err("Noraidīšanas gadījumā ir obligāti jānorāda komentārs!");
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

                if (data['tasks_count'] == 0)
                {
                    $("#dx_tasks_count_li").hide();
                    setTimeout(function() {
                            $.gritter.add({
                                title: 'Apsveicam!',
                                text: '<i class="fa fa-thumbs-o-up"></i> Labs darbs - jums visi uzdevumi ir izpildīti.',
                                time: 7000
                            });
                    }, 3000);
                }
                else
                {
                    $("#dx_tasks_count_badge").html(data['tasks_count']);
                }

                notify_info("Uzdevuma dati veiksmīgi saglabāti!");
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
     * Uzdevumam piesaistītā dokumenta atvēršanas pogas funkcionalitāte
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleOpenItemBtnClick = function(frm){       
        frm.find('.dx-cms-task-btn-open-item').click(function(){
            rel_new_form(frm.attr('dx_form_url'), frm.attr('dx_rel_list_id'), frm.attr('dx_item_id'), frm.attr('dx_rel_field_id'), 'list_item_view_form_' + frm.attr('dx_frm_uniq_id'), null);
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
                frm_deleg.find("textarea[name=task_txt]").html(frm.find("input[name=task_title]").val());
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
            lang: 'lv',
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
                foo: 'Nav norādīta vērtība!'
            },
            feedback: {
                success: 'glyphicon-ok',
                error: 'glyphicon-alert'
            }
        });    
    };
    
    /**
     * Inicializē uzdevuma deleģēšanas formu
     * 
     * @param {object} frm_deleg Delegēšanas formas objekts
     * @returns {undefined}
     */
    var initFormDelegate = function(frm_deleg) {
        
        handleDelegFormBtnDelegClick(frm_deleg);
        initDelegFormValidator(frm_deleg);
        initDueDateField(frm_deleg);
        
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
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {            
    TaskLogic.init();           
});