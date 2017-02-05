<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
	protected $addresses = [];
	
	public function send()
	{
		$delay = 0;
		
		$recipients = $this->getRecipients();
		
		foreach($recipients as $recipient)
		{
			if(!filter_var($recipient->email, FILTER_VALIDATE_EMAIL))
			{
				continue;
			}
			\Mail::later($delay, 'mail.send', ['mail' => $this], function ($message) use ($recipient)
			{
				$message->to($recipient->email, $recipient->display_name);
				$message->from(config('mail.from.address'), config('mail.from.name'));
				$message->subject($this->subject);
			});
			$delay += 2;
		}
		
		$this->sent_time = Carbon::now();
		if(Auth::user())
		{
			$this->sent_user_id = Auth::user()->id;
		}
		$this->folder = 'sent';
		$this->save();
	}
	
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
	
	/**
	 * Get a list of IDs and names of recipients in format suitable for use in <select> element.
	 *
	 * @return array
	 */
	public function getPlainRecipientsList()
	{
		$list = unserialize($this->to);
		
		$result = [];
		
		foreach($list as $type => $items)
		{
			foreach($items as $item)
			{
				$result[] = [
					'id' => $type . ':' . $item['id'],
					'text' => $item['text']
				];
			}
		}
		
		return $result;
	}
	
	/**
	 * Collect email addresses and names of all recipients specified in To field, taking into account information about
	 * specified departments and teams.
	 *
	 * @return mixed
	 */
	public function getRecipients()
	{
		$list = unserialize($this->to);
		
		$query = DB::table('dx_users')
			->select('email', 'display_name')
			->where('is_blocked', 0)
			->whereNull('termination_date');
		
		// not all company
		if(!isset($list[0][0]))
		{
			$ids = [];
			
			// get IDs by type (department, team, employee)
			foreach($list as $type => $items)
			{
				foreach($items as $item)
				{
					$ids[$type][] = $item['id'];
				}
			}
			
			$query->where(function ($query) use ($ids)
			{
				if(isset($ids['dept']))
				{
					$query->orWhereIn('source_id', $ids['dept']);
				}
				
				if(isset($ids['team']))
				{
					$query->orWhereIn('team_id', $ids['team']);
				}
				
				if(isset($ids['empl']))
				{
					$query->orWhereIn('id', $ids['empl']);
				}
			});
		}
		
		return $query->distinct()->get();
	}
}
