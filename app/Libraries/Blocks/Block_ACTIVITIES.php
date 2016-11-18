<?php

namespace App\Libraries\Blocks;

use App;
use App\Models;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Block_ACTIVITIES extends Block
{
	protected $events;
	
	public function getHtml()
	{
		$result = view('blocks.widget_activities', [
			'self' => $this,
			'events' => $this->getEvents()
		])->render();
		
		return $result;
	}
	
	public function getJS()
	{
		// TODO: Implement getJS() method.
	}
	
	public function getCSS()
	{
		return <<<END
			<style>
				.widget-activities .mt-action-img img {
					width: 45px;
					height: 45px;
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
		
		$this->events = Models\Event::limit(10)->orderBy('id', 'DESC')->get();
		
		return $this->events;
	}
}

?>