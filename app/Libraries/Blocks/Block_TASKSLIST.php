<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use App\Exceptions;
use Request;
use Input;
use Config;
use Log;

/**
 * Widget displays current user tasks and provides possibilities to work with them
 */
class Block_TASKSLIST extends Block
{

    /**
     * Object with tasks rows
     * @var object
     */
    public $tasks_rows = null;

    /**
     * Array with predefined tasks views rows
     * 
     * @var array
     */
    public $views_rows = [];

    /**
     * Currnet user object
     * @var object
     */
    public $user = null;

    /**
     * Indicates if user have at least 1 subordinate
     * 
     * @var boolean
     */
    public $is_subordinates = false;

    /**
     * Current user subordinates
     * @var object 
     */
    private $subordinates = null;
    
    /**
     * Current view info
     * @var array
     */
    public $current_view = null;

    /**
     * Tasks register id
     * @var integer 
     */
    public $task_list_id = 0;
    
    /**
     * Render widget and return its HTML.
     *
     * @return string
     */
    public function getHtml()
    {
        $view = (Request::ajax()) ? 'tasks' : 'widget';

        $result = view('blocks.taskslist.' . $view, [
            'self' => $this,
            'frm_uniq_id' => 'widget',
            'item_id' => 0,
            'date_format' => Config::get('dx.txt_date_format', 'd.m.Y'),
            'grid_htm_id' => '',
            'employees' => $this->subordinates
        ])->render();

        return $result;
    }

    /**
     * Returns JavaScript that calculates appropriate height of a widget.
     *
     * @return string
     */
    public function getJS()
    {
        return <<<END
			<script>
				$(document).ready(function(){
					
                                        var set_scroll = function() {
                                            var items = $('.tasks-widget .mt-actions > .mt-action');
                                            var mult = (items.length < 3 ? items.length : 3);

                                            // init scrollbar
                                            $('.tasks-widget .mt-actions').slimScroll({
                                                    height: (items.first().outerHeight() * mult) + 'px' // calculate height of scrollable area
                                            })
                                        }
            
                                        $('.tasks-widget .dx-tasks-filter').click(function() {
                                            show_page_splash(1);
                                            
                                            var formData = new FormData();
                                            formData.append("view_code", $(this).data('code'));
                                            formData.append("param", 'OBJ=TASKSLIST');

                                            var request = new FormAjaxRequest ('block_ajax', "", "", formData);

                                            request.progress_info = "";

                                            request.callback = function(data) {
                                                $('.tasks-widget .task-content').html(data.html);
                                                $('.tasks-widget .dx-task-count').html(data.data.count);
                                                $('.tasks-widget .dx-task-view-title').html(data.data.view_title);
                                                
                                                set_scroll();
                                                setHandlers();
                                                hide_page_splash(1);
                                            };
                                            request.doRequest();
                                        });
                                        
                                        var reloadTotalCountWidget = function() {
        
                                            var widget = $('.dx-tasks-total-widget');
        
                                            if (widget.length == 0) {
                                                return;
                                            }
        
                                            var formData = new FormData();
                                            formData.append("param", 'OBJ=TASKS');

                                            var request = new FormAjaxRequest ('block_ajax', "", "", formData);

                                            request.progress_info = "";

                                            request.callback = function(data) {
                                                widget.html(data.html);
                                                hide_page_splash(1);
                                            };
                                            request.doRequest();
                                        };
        
                                        var openTaskForm = function(sender) {
                                            TaskLogic.setDelegateCallback(afterDelegated);
                                            TaskLogic.setDecisionCallback(afterDecisionDone);
                                            
                                            var task = sender.closest('.mt-action');
        
                                            open_form('form_task', task.data('task-id'), task.data('task-list-id'), 0, 0, '', 1, '');
                                        };
        
                                        var setHandlers = function() {
                                            $('.tasks-widget .dx-form-link').click(function() {
                                                open_form('form', $(this).data('item-id'), $(this).data('list-id'), 0, 0, '', $(this).data('is-edit'), '');
                                            });

                                            $('.tasks-widget .dx-task-link').click(function() {
                                                openTaskForm($(this));
                                            });
        
                                            $('.tasks-widget .dx-btn-yes, .tasks-widget .dx-btn-info').click(function() {
                                                saveYesTask('task_yes', $(this).closest('.mt-action').data('task-id'), '');
                                            });
        
                                            $('.tasks-widget .dx-btn-no').click(function() {
                                                openTaskForm($(this));
                                            });
                                                
                                            $('.tasks-widget .dx-task-history').click(function() {
                                                var item_id = $(this).data('item-id');
                                                var list_id = $(this).data('list-id');

                                                $('#popup_window .modal-header h4').html(Lang.get('task_form.history_title'));

                                                $("#popup_body").html(getProgressInfo());
                                                $('#popup_window').modal('show');

                                                var formData = "item_id=" + item_id + "&list_id=" + list_id;

                                                var request = new FormAjaxRequestIE9 ('get_tasks_history', "", "", formData);            
                                                request.progress_info = "";                       

                                                request.callback = function(data) {
                                                    $('#popup_body').html(data['html']);
                                                };

                                                // execute AJAX request
                                                request.doRequest();
                                            });
                                            
                                            $('.tasks-widget .dx-btn-deleg').click(function() {
                                                var frm_deleg = $("#form_delegate_widget");
                                                if (frm_deleg.attr('dx_is_init') == "0") {
                                                    TaskLogic.initFormDelegate(frm_deleg);                                                    
                                                }
                                                TaskLogic.showNewDelegteTab(frm_deleg);
                                                TaskLogic.setDelegateCallback(afterDelegated);
                                                
                                                frm_deleg.attr('dx_task_id', $(this).closest('.mt-action').data('task-id'));
        
                                                var task_details = $(this).data('details') ? $(this).data('details') : $(this).data('task-type');
                                                frm_deleg.find("textarea[name=task_txt]").html(task_details);
                                                
                                                frm_deleg.find("input[name=due_date]").val($(this).closest('.mt-action').find('.mt-action-time.dx-task-due').html());
                                                
                                                var tab_tasks = frm_deleg.find('.dx-tab-tasks');
                                                TaskLogic.fillDelegatedTasks(frm_deleg, tab_tasks);
                                                        
                                                frm_deleg.modal('show');

                                                setTimeout(function() {
                                                    frm_deleg.find("select[name=employee_id]").focus();
                                                }, 1000);
                                            });
        
                                        };
                                        
                                        var afterDelegated = function(task_id, status) {
                                            $('.tasks-widget .mt-action[data-task-id=' + task_id + ']').find('.dx-task-status').html(status);
                                            reloadTotalCountWidget();
                                        };
        
                                        var afterDecisionDone = function(task_id) {
                                            $('.tasks-widget .mt-action[data-task-id=' + task_id + ']').addClass('bounceOutLeft');
                                            setTimeout(function(){ 
                                                $('.tasks-widget .mt-action[data-task-id=' + task_id + ']').hide();
                                            }, 500);        
                                            var cnt = parseInt($('.tasks-widget .dx-task-count').html())-1;
                                            $('.tasks-widget .dx-task-count').html(cnt);
                                            reloadTotalCountWidget();
                                        };
                                        
                                        var saveYesTask = function(save_url, task_id, comm) {                                            
                                            
                                            show_page_splash();
        
                                            var formData = new FormData();
                                            formData.append("item_id", task_id);
                                            formData.append("task_comment", comm);
                                            
                                            var request = new FormAjaxRequest(save_url, '', '', formData);

                                            request.callback = function(data) {                                                
                                                afterDecisionDone(task_id);
                                                TaskLogic.updateMenuTasksCounter(data['tasks_count']);
                                                notify_info(Lang.get('task_widget.msg_done'));
                                            };

                                            // izpildam AJAX pieprasījumu
                                            request.doRequest();
                                        };
        
                                        set_scroll();
                                        setHandlers();
				});
			</script>
END;
    }

    /**
     * Returns widget's styles.
     *
     * @return string
     */
    public function getCSS()
    {

        $custom = <<<END
			<style>                                
                                .tasks-widget .btn-group-circle button, .dx-btn-info {
                                            border-width: 1px !important;
                                }
            
                                .tasks-widget .mt-action-buttons {
                                    width: 165px!important;
                                    margin-top: 20px!important;
                                }
            
                                .tasks-widget .mt-actions {
                                    padding-right: 15px!important;
                                }
            
                                .tasks-widget .mt-action-img a.btn {
                                    width: 50px;
                                    height:50px;
                                    font-size: 20px!important;
                                    background-color: #cccccc;
                                    padding-top: 10px!important;
                                }
			</style>
END;
        return view('pages.view_css_includes')->render() . $custom;
    }

    public function getJSONData()
    {
        return ['count' => count($this->tasks_rows), 'view_title' => $this->current_view['title']];
    }

    protected function parseParams()
    {
        // set current user
        $this->user = App\User::find(Auth::user()->id);
        $this->subordinates = $this->user->subordinates()->whereRaw("ifnull(termination_date, '2099-01-01') > NOW()")->orderBy('display_name')->get();
        
        $this->is_subordinates = (count($this->subordinates) > 0);

        $this->fillViewsArray();

        $this->setCurentView();

        $this->tasks_rows = TaskListViews\TaskListViewFactory::build_taskview($this->current_view['code'])->getRows();

        $this->fillIncludesArr();
        
        $this->task_list_id = App\Libraries\DBHelper::getListByTable("dx_tasks")->id;
    }

    /**
     * Sets the current view
     * @throws Exceptions\DXCustomException
     */
    private function setCurentView()
    {
        $view_code = Input::get('view_code', 'MY_ACTUAL');

        foreach ($this->views_rows as $key => $view) {
            if ($view['code'] == $view_code) {
                $this->current_view = $this->views_rows[$key];
                return;
            }
        }

        throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_task_view'), $view_code));
    }

    /**
     * Fill views array
     */
    private function fillViewsArray()
    {
        array_push($this->views_rows, ['code' => 'MY_ACTUAL', 'title' => trans('task_widget.view_my_actual')]);
        array_push($this->views_rows, ['code' => 'MY_OVERDUE', 'title' => trans('task_widget.view_my_overdue')]);        
        array_push($this->views_rows, ['code' => 'MY_DUETODAY', 'title' => trans('task_widget.view_my_duetoday')]);
        array_push($this->views_rows, ['code' => 'MY_DONE', 'title' => trans('task_widget.view_my_done')]);

        /*
        if ($this->is_subordinates) {
            // exist at least 1 subordinate
            // add divider item
            array_push($this->views_rows, ['code' => 'DIVIDER', 'title' => 'DIVIDER']);

            // add subordinate items
            array_push($this->views_rows, ['code' => 'SUB_ACTUAL', 'title' => trans('task_widget.view_sub_actual')]);
            array_push($this->views_rows, ['code' => 'SUB_OVERDUE', 'title' => trans('task_widget.view_sub_overdue')]);
            array_push($this->views_rows, ['code' => 'SUB_DONE', 'title' => trans('task_widget.view_sub_done')]);
            array_push($this->views_rows, ['code' => 'SUB_DUETODAY', 'title' => trans('task_widget.view_sub_duetoday')]);
        }        
        */
    }

    /**
     * Fill JS includes array
     */
    private function fillIncludesArr()
    {

        if (Request::ajax()) {
            return;
        }

        $this->addJSInclude(elixir('js/elix_view.js'));

        // Teksta redaktora komponente
        $this->addJSInclude('plugins/tinymce/tinymce.min.js');
    }

}

?>