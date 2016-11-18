<?php

namespace App\Libraries\Blocks;

use App;
use App\Models;
use Illuminate\Support\Facades\Auth;

class Block_ACTIVITIES extends Block
{
	protected $events;
	
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
					$('.widget-activities > .portlet-title .dropdown-menu a').click(function(){
						console.log($(this).data('group'));
						var buttons = $('.widget-activities > .portlet-title > .actions > .btn-group > ul > li > a');
						var items = $('.widget-activities > .portlet-body .mt-actions > .mt-action');
						
						if($(this).data('group') == '-1')
						{
							buttons.removeClass('active');
							items.filter(':hidden').show();
							return;
						}
						
						if(!$(this).hasClass('active'))
						{
							buttons.removeClass('active');
							$(this).addClass('active');
							items.filter(':visible').hide();
							items.filter('[data-group="' + $(this).data('group') + '"]').show();
						}
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
			</style>
END;
	}

	public function getJSONData()
	{
		// TODO: Implement getJSONData() method.
	}
	
	protected function parseParams()
	{
		// TODO: Implement parseParams() method.
	}
	
	protected function getEvents()
	{
		if($this->events)
			return $this->events;
		
		$user = App\User::find(Auth::user()->id);
		
		$this->events = Models\Event::whereIn('list_id', array_keys($user->getLists()))
			->limit(10)
			->orderBy('id', 'desc')
			->get();
		
		return $this->events;
	}
}

?>