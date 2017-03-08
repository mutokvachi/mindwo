<?php

namespace App\Libraries\Blocks;

use App;
use App\Models;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Config;

/**
 * Class Block_ACTIVITIES
 *
 * Widget that displays history of actions performed by authorized users.
 *
 * @package App\Libraries\Blocks
 */
class Block_ACTIVITIES extends Block
{
	/**
	 * Indicates if system have profile UI functionality
	 * @var type
	 */
	public $is_profile = false;
        
        /**
         * Events register ID
         * @var integer 
         */
        public $events_list_id = 0;
        
	/**
	 * @var App\User Current logged in user
	 */
	protected $user;
	/**
	 * @var Collection Filtered list of events
	 */
	protected $events;
	/**
	 * @var array List of lists that user has access to
	 */
	protected $lists = [];
	/**
	 * @var array List of selected filter groups
	 */
	protected $groups = [];
	/**
	 * @var int Id of last loaded element for lazy loading
	 */
	protected $lastId;
	
	/**
	 * Render widget and return its HTML.
	 * @return string
	 */
	public function getHtml()
	{
		$result = view('blocks.widget_activities', [
			'self' => $this,
			'groups' => App\Models\ListGroup::orderBy('order_index')->get(),
			'events' => $this->getEvents()
		])->render();
		
		return $result;
	}
	
	/**
	 * Returns JavaScript that:
	 *
	 * - calculates appropriate height of a widget
	 * - implements filtering
	 * - handles lazy loading of widget content
	 *
	 * @return string
	 */
	public function getJS()
	{
		return <<<END
			<script>
				$(document).ready(function(){
					var activitiesFilterGroups = [];
				
					// bind click handlers to 'view' and 'history' buttons
					var handleButtons = function()
					{
						$('.widget-activities .mt-action-buttons a:not(.handled)').each(function()
						{
							if(($(this).data('profile') == false) || $(this).hasClass('dx-button-history'))
							{
								$(this).click(function()
								{
									view_list_item('form', $(this).data('item_id'), $(this).data('list_id'), 0, 0, '', '');
								});
							}
							$(this).addClass('handled');
						});
					};
					
					handleButtons();
					
					var items = $('.widget-activities .mt-actions > .mt-action');
					var mult = (items.length < 5 ? items.length : 5);
					
					// init scrollbar
					$('.widget-activities .mt-actions').slimScroll({
						height: (items.first().outerHeight() * mult) + 'px' // calculate height of scrollable area
					})
					
					// lazyload more records when bottom of scrollable area has reached
					.bind('slimscroll', function(e, pos)
					{
						if(pos != 'bottom')
							return;
						
						var request = {
							param: 'OBJ=ACTIVITIES',
							lastId: $('.widget-activities .mt-actions > .mt-action').last().data('id'),
							groups: activitiesFilterGroups
						};
						
						show_page_splash(1);
						
						$.ajax({
							type: 'POST',
							url: DX_CORE.site_url + 'block_ajax',
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
								
								$('.widget-activities > .portlet-body .mt-actions').append(data.html);
								handleButtons();
								
								hide_page_splash(1);
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								console.log(textStatus);
								console.log(jqXHR);
								hide_page_splash(1);
							}
						});
					});
					
					var labels = $('.widget-activities > .portlet-title > .actions > .btn-group label');
					var checkboxes = $('.widget-activities > .portlet-title > .actions > .btn-group input');
					
					// prevent closing of filter dropdown after clicking on checkbox label
					labels.click(function(e)
					{
						e.stopPropagation();
					});
					
					// filtering of results
					checkboxes.change(function()
					{
						activitiesFilterGroups = [];
						
						checkboxes.each(function()
						{
							if($(this).is(':checked'))
							{
								activitiesFilterGroups.push($(this).val());
							}
						});
						
						var request = {
							param: 'OBJ=ACTIVITIES',
							groups: activitiesFilterGroups
						};
						
						show_page_splash(1);
						
						$.ajax({
							type: 'POST',
							url: DX_CORE.site_url + 'block_ajax',
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
								
								var root = $('.widget-activities > .portlet-body .mt-actions');
								
								root.html(data.html);
								handleButtons();
								
								hide_page_splash(1);
							},
							error: function(jqXHR, textStatus, errorThrown)
							{
								console.log(textStatus);
								console.log(jqXHR);
								hide_page_splash(1);
							}
						});
					});
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
				.widget-activities .mt-action-img img {
					width: 45px;
					height: 45px;
				}
				.widget-activities > .portlet-title > .actions > .btn-group > ul > li > a.active {
					background-color: #ddd;
				}
				.widget-activities .mt-checkbox-list {
					padding: 10px;
				}
				.widget-activities label {
					display: block;
					white-space: nowrap;
				}
			</style>
END;
	}
	
	public function getJSONData()
	{
		return [];
	}
	
	/**
	 * Parse parameters and initialize class properties
	 */
	protected function parseParams()
	{
		// current logged in user
		$this->user = App\User::find(Auth::user()->id);
		
		if(Request::ajax())
		{
			// list of groups from filter
			$this->groups = Request::input('groups');
			// ID of last loaded element for AJAX lazy loading
			$this->lastId = Request::input('lastId');
		}
		
		if(empty($this->groups))
		{
			// lists without filter
			$this->lists = array_keys($this->user->getLists());
		}
		
		else
		{
			// filtered list of lists
			foreach($this->user->getLists() as $id => $arr)
			{
				if(in_array($arr['group'], $this->groups))
				{
					$this->lists[] = $id;
				}
			}
		}
		
		$this->is_profile = (Config::get('dx.employee_profile_page_url'));
                $this->events_list_id = App\Libraries\DBHelper::getListByTable('dx_db_events')->id;
	}
	
	/**
	 * Find all events that user can view, and optionally filter them by group.
	 *
	 * @return mixed
	 */
	protected function getEvents()
	{
		if($this->events)
		{
			return $this->events;
		}
		
		$query = Models\Event::whereIn('list_id', $this->lists);
		
		if($this->lastId)
		{
			$query->where('id', '<', $this->lastId);
		}
		
		$query
			->limit((Request::ajax() && $this->lastId) ? 5 : 10)
			->orderBy('id', 'desc');
		
		$this->events = $query->get();
		
		return $this->events;
	}
}

?>