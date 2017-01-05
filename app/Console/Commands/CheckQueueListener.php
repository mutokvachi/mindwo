<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

/**
 * Komanda pārbauda vai darbojas Queue Listener process un nepieciešamības gadījumā iedarbina to
 * Darbojas tikai uz Linux
 */
class CheckQueueListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mindwo:check_listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pārbauda, vai darbojas Queue Listener process un nepieciešamības gadījumā iedarbina to';

    /**
     * Ceļš uz datni, kurā saglabā procesa ID numuru
     * 
     * @var string 
     */
    private $tmp_path = "";
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->tmp_path = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "queue" . DIRECTORY_SEPARATOR . 'queue.pid';
        
        $this->checkIsListenerRunning();
        
        $this->info('Queue Listener is working!');
    }
    
    /**
     * Pārbauda, vai darbojas Queue Listetner process un iedarbina to
     */
    private function checkIsListenerRunning() {
        
        if (file_exists($this->tmp_path )) {
            $pid = file_get_contents($this->tmp_path );
            $result = exec('ps -e | grep ' . $pid);
            if ($result == '') {
                $this->runListener();
            }
        } else {
            $this->runListener();
        }
    }
            
    
    /**
     * Iedarbina Queue Listener procesu
     */
    private function runListener ()
    {
        $cmd = "php " . base_path() . DIRECTORY_SEPARATOR . "artisan queue:listen > /dev/null & echo $!";
        
        $number = exec($cmd);
        file_put_contents($this->tmp_path, $number);
    }
}