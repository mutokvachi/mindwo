<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Sends emails for views monitoring information
 */
class SendMonitoringMail extends Job implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    /**
     * Array with email data
     * 
     * @var array
     */
    private $arr_data = array();

    /**
     * Blade view name for email template
     * 
     * @var string
     */
    private $blade_mail = "";

    /**
     * Constructs monitoring mail sending class
     * 
     * @param array $arr_data Email data array (must contain email and subject)
     * @param string $blade_view Blade view name (extension .blade not included)
     */
    public function __construct($arr_data, $blade_view)
    {
        $this->arr_data = $arr_data;
        $this->arr_data["portal_url"] = get_portal_config("PORTAL_PUBLIC_URL");
        $this->blade_mail  = $blade_view;
        $this->arr_data['date_now'] = date('Y-n-d H:i:s');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $mailer->send(
                $this->blade_mail, $this->arr_data, function ($message)
                {
                    $message->to($this->arr_data['email'])->subject($this->arr_data['subject']);
                }
        );
    }

}