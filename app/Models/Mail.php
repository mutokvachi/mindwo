<?php

namespace App\Models;

use App\Http\Controllers\MailController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Mail
 *
 * A model which represents email message.
 *
 * @package App\Models
 */
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
	/**
	 * Database table for mail messages.
	 *
	 * @var string
	 */
	protected $table = 'dx_mail';
	
	/**
	 * Relationship to mail attachments.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function attachments()
	{
		return $this->hasMany('App\Models\MailAttachment', 'mail_id', 'id');
	}
	
	/**
	 * Override delete() method so that it removes attachments along with the message.
	 *
	 * @return bool|null
	 */
	public function delete()
	{
		foreach($this->attachments()->get() as $attachment)
		{
			$attachment->delete();
		}
		
		return parent::delete();
	}
	
	/**
	 * Check request data for uploaded files and process them.
	 *
	 * @param Request $request
	 * @return string
	 */
	public function processUploads(Request $request)
	{
		$result = [
			'messages' => [],
			'html' => ''
		];
		
		if(!$request->hasFile('files'))
		{
			return $result;
		}
		
		$attachments = [];
		$messages = [];
		$extensions = MailController::getAllowedExtensions();
		
		foreach($request->file('files') as $file)
		{
			if(!$file->isValid())
			{
				$messages[] = trans('mail.error_file_invalid', ['file' => $file->getClientOriginalName()]);
				continue;
			}
			
			// skip files with wrong extension
			if(!in_array(strtolower($file->getClientOriginalExtension()), $extensions))
			{
				$messages[] = trans('mail.error_file_extension', ['file' => $file->getClientOriginalName()]);
				continue;
			}
			
			$attachment = MailAttachment::createFromUploadedFile($file);
			$this->attachments()->save($attachment);
			$attachments[] = $attachment;
		}
		
		$result['messages'] = $messages;
		$result['html'] = view('mail.files', [
			'attachments' => $attachments,
			'deleteButton' => true
		])->render();
		
		return $result;
	}
	
	/**
	 * Send email to each recipient individually. Sending is done via queue.
	 */
	public function send()
	{
		$recipients = $this->getRecipients();
		$attachments = $this->attachments()->get();
		$delay = 0;
		foreach($recipients as $recipient)
		{
			if(!filter_var($recipient->email, FILTER_VALIDATE_EMAIL))
			{
				continue;
			}
			\Mail::later($delay, 'mail.send', ['mail' => $this], function ($message) use ($recipient, $attachments)
			{
				$message->to($recipient->email, $recipient->display_name);
				$message->from(config('mail.from.address'), config('mail.from.name'));
				$message->subject($this->subject);
				
				foreach($attachments as $attachment)
				{
					$message->attach($attachment->getFilePath(), [
						'as' => $attachment->file_name,
						'mime' => $attachment->mime_type
					]);
				}
			});
			$delay += config('dx.email.send_delay', 0);
		}
		
		$this->sent_time = Carbon::now();
		if(Auth::user())
		{
			$this->sent_user_id = Auth::user()->id;
		}
		$this->folder = 'sent';
		$this->save();
	}
	
	/**
	 * Format date according to configuration settings.
	 *
	 * @param $date
	 * @return string
	 */
	public function formatDate($date, $full = false)
	{
		$now = new Carbon('now');
		$date = new Carbon($date);
		
		if(!$full && $date->diffInHours($now) < 24)
		{
			$result = $date->toTimeString();
		}
		else
		{
			$result = $date->format(config('dx.txt_datetime_format', 'Y-m-d H:i:s'));
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
		
		$ignore = config('dx.empl_ignore_ids', []);
		
		if(!empty($ignore))
		{
			//$query->whereNotIn('id', $ignore);
		}
		
		return $query->distinct()->get();
	}
}
