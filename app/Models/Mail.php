<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;

class Mail extends Model
{
	/**
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	
    protected $table = 'dx_mail';
    
    public function formatDate($date)
	{
		$now = new Carbon('now');
		$date = new Carbon($date);
		
		if($date->diffInHours($now) < 24)
		{
			$result = $date->toTimeString();
		}
		
		else
		{
			$result = $date->toDateTimeString();
		}
		
		return $result;
	}
	
	public function getPlainRecipientsList()
	{
		$list = unserialize($this->to);
		
		$result = [];
		
		foreach($list as $type => $items)
		{
			foreach($items as $item)
			{
				$result[] = [
					'id' => $type.':'.$item['id'],
					'text' => $item['text']
				];
			}
		}
		
		return $result;
	}
	
}
