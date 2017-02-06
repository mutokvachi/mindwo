<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Mail;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendScheduledEmails extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;
	
	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}
	
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
