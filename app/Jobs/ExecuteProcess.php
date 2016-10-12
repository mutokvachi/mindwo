<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Laravel darba klase, kas izpilda tai padoto procesu
 */
class ExecuteProcess extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;
    /**
     * @var \App\Libraries\Processes\Process Process, kuru izpildīs
     */
    private $process;

    /**
     * Inicializē procesu izpildes darbu
     *
     * @param \App\Libraries\Processes\Process $process Process, kuru nepieciešams izpildīt
     * @return void
     */
    public function __construct(\App\Libraries\Processes\Process $process)
    {
        $this->process = $process;
    }

    /**
     * Izpilda procesu
     *
     * @return void
     */
    public function handle()
    {
        $this->process->execute();
    }
}