<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

/**
 * The log file parsing and user session save to database in dx_server_access table
 * Required: 
 *      database table=Logs, 
 *      configFile=server_log with parameter=[logFilePath,tempFolderPath] ,
 *      temp folder where create temporary files
 */
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
        $userMatch = array();
        
        //make file copy
        $filePath = $this->copyFile($originalFilePath, $tempPath);
        
        if(!File::exists($filePath)) 
        {
            \Log::info("Server log read error - file not found");
            return;
        }
        
        $lastAction = DB::table('dx_server_access')->max('timestamp');
        $handle = fopen($filePath, "r");
        
        if (!$handle) {
            \Log::info("Server log read error - file not readable");
            return;
        }
        
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
                {
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
                            'uid' => $ts . $lineArr['offset']
                        );                              
                    }
                }                            
            }
        }
        fclose($handle);

        $this->setLogToDB($result);
        $this->deleteFile($filePath);
        
    }
   /**
    * Save log file sessions to database. 
    * Open session will be inserted, but close session update Drop column
    * @param type $result 
    */
    private function setLogToDB($result) {
         foreach ($result as $key => $val)
            {
                if($val['action'] === 'opened')
                {
                    //check if record not already exist in DB
                    $hasRecord = DB::table('dx_server_access')->where('uid',$val['uid'])->count();
                    if($hasRecord == 0)
                    {
                        $data = array(
                            'timestamp'=>$val['timestamp'],
                            'offset'=>$key,
                            'user'=>$val['user'],
                            'connect' => $val['date'],
                            'note' => $val['note'],
                            'sshd' => $val['sshd'],
                            'uid' => $val['uid']
                        );
                        DB::table('dx_server_access')->insert($data);
                    }
                } else {
                    DB::table('dx_server_access')
                            ->where('user', $val['user'])
                            ->whereNull('disconnect')
                            ->where('sshd', $val['sshd'])
                            ->update(['disconnect' => $val['date'], 'timestamp' => $val['timestamp']]);
                }                
            }
    }
    /**
     * Create file copy in specified folder
     * @param type $filePath the original file path
     * @param type $tempPath the folder path where will crate a file
     * @return type the copied file full path
     */
    private function copyFile($filePath, $tempPath)
    {
        $newFilePath = $this->makeUniqueFileName($tempPath);
        File::copy($filePath, $newFilePath);
        return $newFilePath;
    }
    /**
     * File delete
     * @param type $filePath the file path
     */
    private function deleteFile($filePath) {
        if(File::exists($filePath))
        {
            File::delete($filePath);
        }
    }
    /**
     * Generate unique file name in current folder, but don't create file
     * @param type $tempPath the path to folder
     * @return string the unique file name
     */
    private function makeUniqueFileName($tempPath)
    {
        $tempFileName = '';
        while (true) {
            $tempFileName = $tempPath . uniqid(rand(), true) . '.txt';
            if (!file_exists($tempFileName)) 
            {
                    break;
            }
        }
        return $tempFileName;
    }   
}