<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use App\Exceptions;
use Request;
use Input;
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
         * Current view info
         * @var array
         */
        public $current_view = null;
                
        /**
	 * Render widget and return its HTML.
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$view = (Request::ajax()) ? 'tasks' : 'widget';
                
                $result = view('blocks.taskslist.' . $view, [
			'self' => $this
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
                                                hide_page_splash(1);
                                            };
                                            request.doRequest();
                                        });
            
                                        set_scroll();
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
		return <<<END
			<style>                                
                                .tasks-widget .btn-group-circle button {
                                            border-width: 1px !important;
                                }
            
                                .tasks-widget .mt-action-buttons {
                                    width: 165px!important;
                                    margin-top: 20px!important;
                                }
            
                                .tasks-widget .mt-actions {
                                    padding-right: 15px!important;
                                }
            
                                .tasks-widget .mt-action-img button {
                                    width: 50px;
                                    height:50px;
                                    font-size: 20px!important;
                                }
			</style>
END;
     
	}
	
	public function getJSONData()
        {
            return ['count' => count($this->tasks_rows), 'view_title' => $this->current_view['title']];
	}
        
	protected function parseParams()
	{
            // set current user
            $this->user = App\User::find(Auth::user()->id);
            $this->is_subordinates = ($this->user->subordinates()->first());
            
            $this->fillViewsArray();

            $this->setCurentView();
            
            $this->tasks_rows = TaskListViews\TaskListViewFactory::build_taskview($this->current_view['code'])->getRows();
	}
        
        /**
         * Sets the current view
         * @throws Exceptions\DXCustomException
         */
        private function setCurentView() {
            $view_code = Input::get('view_code', 'MY_ACTUAL');
            
            foreach($this->views_rows as $key => $view) {
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
        private function fillViewsArray() {
            array_push($this->views_rows, ['code' => 'MY_ACTUAL', 'title' => trans('task_widget.view_my_actual')]);
            array_push($this->views_rows, ['code' => 'MY_OVERDUE', 'title' => trans('task_widget.view_my_overdue')]);
            array_push($this->views_rows, ['code' => 'MY_DONE', 'title' => trans('task_widget.view_my_done')]);
            array_push($this->views_rows, ['code' => 'MY_DUETODAY', 'title' => trans('task_widget.view_my_duetoday')]);
            
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
        }
}

?>