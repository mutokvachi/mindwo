<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ServerLogImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mindwo:save-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save log data to database';

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
        $originalFilePath = Config::get('server_log.logFilePath');
        $tempPath = Config::get('server_log.tempFolderPath');
        $patternCRON = "/CRON[[0-9]*]/u";
        $patternUser = "#([A-Za-z]{3}.[0-9]{2}.[0-9]{2}:[0-9]{2}:[0-9]{2}).*(sshd\[(.*?)\]).*(session (.*?) for user ([a-zA-Z0-9]+))#";
        $result = array();
        
        //make file copy
        $filePath = $this->copyFile($originalFilePath, $tempPath);
        
        if(File::exists($filePath)) 
        {
            $lastAction = DB::table('logs')->max('timestamp');
            $handle = fopen($filePath, "r");
            if ($handle) {
                //loop file line by line
                while (($line = fgets($handle)) !== false) {
                    $lineArr = json_decode($line, TRUE);
                    if(array_key_exists('message', $lineArr) &&
                       array_key_exists('offset', $lineArr) &&
                       array_key_exists('@timestamp', $lineArr) &&
                       strtotime($lineArr['@timestamp']) > strtotime($lastAction))
                    {
                        $record = $lineArr['message'];
                        //allow all except CRON job
                        if(!preg_match($patternCRON, $record))
                            if(preg_match($patternUser, $record, $userMatch))
                            {                               
                                $ts = date("YmdHis",strtotime($lineArr['@timestamp']));
                                $actionDate = \DateTime::createFromFormat('M d H:i:s',$userMatch[1])->format('Y-m-d H:i:s');
                                //collect new event logs
                                $result[$lineArr['offset']] = array(
                                    'timestamp' => $lineArr['@timestamp'],
                                    'user' => $userMatch[6],
                                    'action' => $userMatch[5],
                                    'date' => $actionDate,
                                    'sshd' => $userMatch[3],
                                    'note' => $line,
                                    'UID' => $ts . $lineArr['offset']
                                );                              
                            }                            
                    }
                }
                fclose($handle);
            } else {
                // error opening the file.
            }
            foreach ($result as $key => $val)
            {
                if($val['action'] === 'opened')
                {
                    $hasRecord = DB::table('logs')->where('UID',$val['UID'])->count();
                    if($hasRecord == 0)
                    {
                    $data = array(
                        'Timestamp'=>$val['timestamp'],
                        'Offset'=>$key,
                        'User'=>$val['user'],
                        'Connect' => $val['date'],
                        'Note' => $val['note'],
                        'SSHD' => $val['sshd'],
                        'UID' => $val['UID']
                    );
                    DB::table('logs')->insert($data);
                    }
                } else {
                    $close = date_create_from_format('M d H:i:s', $val['date']);
                    DB::table('logs')
                            ->where('User', $val['user'])
                            ->where('Drop', 'IS NULL', null, 'and')
                            ->where('SSHD', $val['sshd'])
                            ->update(['Drop' => $val['date'], 'Timestamp' => $val['timestamp']]);
                }                
            }
            $this->deleteFile($filePath);
        }
    }
   
    private function copyFile($filePath, $tempPath)
    {
        $newFilePath = $this->makeUniqueFileName($tempPath);
        File::copy($filePath, $newFilePath);
        return $newFilePath;
    }
    
    private function deleteFile($filePath) {
        if(File::exists($filePath))
            File::delete($filePath);
    }
    
    private function makeUniqueFileName($tempPath)
    {
        $tempFileName;
        while (true) {
            $tempFileName = $tempPath . uniqid(rand(), true) . '.txt';
            if (!file_exists($tempFileName)) 
                    break;
        }
        return $tempFileName;
    }
}