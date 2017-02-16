<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Mail;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class SendScheduledEmails
 *
 * A job which checks for scheduled emails and sends them when their time is reached.
 *
 * @package App\Jobs
 */
class SendScheduledEmails extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;
	
	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$scheduled = Mail::where([
			['folder', '=', 'scheduled'],
			['send_time', '<', Carbon::now()->toDateTimeString()]
		])->get();
		
		foreach($scheduled as $mail)
		{
			$mail->send();
		}
	}
}
