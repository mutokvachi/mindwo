<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use DB;

class SendNotifyMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    /**
     * Masīvs ar e-pasta datiem
     * 
     * @var array
     */
    private $arr_data = array();
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($arr_data)
    {
        $this->arr_data = $arr_data;
        
        $this->arr_data["date_now"] = date('Y-n-d H:i:s');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        if (!filter_var($this->arr_data['email'], FILTER_VALIDATE_EMAIL)) {
            \Log::info("Email sending error. Not valid e-mail address. Data: " . json_encode($this->arr_data));
            return; // no valid email address provided
        }
        
        $mailer->send(
                'emails.notify_email',
                $this->arr_data, 
                function ($message) {
                    $message->to($this->arr_data['email'])->subject($this->arr_data['subject']);
                }
        );
        
        DB::table('dx_emails_sent')->where('id', '=', $this->arr_data['email_id'])->update([
            'sent_time' => $this->arr_data["date_now"]
        ]);

        sleep(2); // For Gmail there is rate-limit (1 email per second) https://productforums.google.com/forum/#!topic/apps/P9Yh0xI2fac
    }
}
