<?php

namespace App\Libraries\Blocks;

use App;
use App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Block_ACTIVITIES extends Block
{
	protected $events, $lists = [], $groups = [], $user;
	
	public function getHtml()
	{
		$result = view('blocks.widget_activities', [
			'self' => $this,
			'groups' => App\Models\ListGroup::orderBy('order_index')->get(),
			'events' => $this->getEvents()
		])->render();
		
		return $result;
	}
	
	public function getJS()
	{
		return <<<END
			<script>
				$(document).ready(function(){
					var items = $('.widget-activities .mt-actions > .mt-action');
					var mult = (items.length < 5 ? items.length : 5);
					
					$('.widget-activities .mt-actions').slimScroll({
						height: (items.first().outerHeight() * mult) + 'px'
					});
					$('.widget-activities .mt-action-buttons a[data-profile="false"]').click(function(){
						view_list_item('form', $(this).data('item_id'), $(this).data('list_id'), 0, 0, '', '');
					});
					$('.widget-activities .mt-action-buttons .dx-button-history').click(function(){
						view_list_item('form', $(this).data('item_id'), $(this).data('list_id'), 0, 0, '', '');
					});
					
					var labels = $('.widget-activities > .portlet-title > .actions > .btn-group label');
					var checkboxes = $('.widget-activities > .portlet-title > .actions > .btn-group input');
					
					labels.click(function(e)
					{
						e.stopPropagation();
					});
					
					checkboxes.change(function(){
						var request = {
							param: 'OBJ=ACTIVITIES',
							groups: []
						};
						
						checkboxes.each(function()
						{
							if($(this).is(':checked'))
							{
								request.groups.push($(this).val());
							}
						});
						
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
		return [
			'groups' => $this->groups,
			'lists' => $this->lists
		];
	}
	
	protected function parseParams()
	{
		$this->user = App\User::find(Auth::user()->id);
		
		if(Request::ajax())
		{
			$this->groups = Request::input('groups');
		}
		
		if(empty($this->groups))
		{
			$this->lists = array_keys($this->user->getLists());
		}
		
		else
		{
			foreach($this->user->getLists() as $id => $arr)
			{
				if(in_array($arr['group'], $this->groups))
				{
					$this->lists[] = $id;
				}
			}
		}
	}
	
	/**
	 * Find all events that user can view, and optionally filter them by group.
	 *
	 * @return mixed
	 */
	protected function getEvents()
	{
		if($this->events)
			return $this->events;
		
		$this->events = Models\Event::whereIn('list_id', $this->lists)
			->limit(10)
			->orderBy('id', 'desc')
			->get();
		
		return $this->events;
	}
}

?>