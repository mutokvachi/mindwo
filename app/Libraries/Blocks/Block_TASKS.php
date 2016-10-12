<?php
namespace App\Libraries\Blocks 
{
    use DB;
    use PDO;
    use App\Exceptions;
    use Auth;
    
    /**
     * Uzdevumu statusu attēlošanas bloks
     */
    class Block_TASKS extends Block 
    {        
        /**
         * Masīvs ar uzdevumu statusiem
         * 
         * @var array 
         */
        private $arr_info = array();
        
        /**
        * Izgūst bloka HTML
        * 
        * @return string Bloka HTML
        */
        public function getHTML()
        {            
            return view('blocks.tasks', [
                'arr_info' => $this->arr_info,
                'panels_class' => ((count($this->arr_info) == 2) ? 6 : 4)
                
            ])->render();
        }
        
        /**
        * Izgūst bloka JavaScript
        * 
        * @return string Bloka JavaScript loģika
        */
        public function getJS()
        {
            return "";
        }
        
        /**
        * Izgūst bloka CSS
        * 
        * @return string Bloka CSS
        */
        public function getCSS() 
        {
            return "";
        }
        
        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return "";
        }
        
        /**
        * Izgūst bloka parametra vērtības
        * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...]]
        * 
        * @return void
        */
        protected function parseParams()
        {
            array_push($this->arr_info, $this->getMyTasks());
            
            $team_tasks = $this->getTeamTasks();
            
            if (count($team_tasks) > 0) {
                array_push($this->arr_info, $team_tasks);
            }
            
            array_push($this->arr_info, $this->getCompanyTasks());
        }
        
        /**
         * Atgriež masīvu ar darbinieka uzdevumu statusiem
         * 
         * @return type
         */
        private function getMyTasks() {
            $arr_data = array();
            
            $arr_data['total_all'] = DB::table('dx_tasks')
                                  ->where('task_employee_id', '=', Auth::user()->id)
                                  ->whereNull('task_closed_time')
                                  ->count();
            
            $arr_data['due_today'] = DB::table('dx_tasks')
                                      ->where('task_employee_id', '=', Auth::user()->id)
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['due_today_undone'] = DB::table('dx_tasks')
                                      ->where('task_employee_id', '=', Auth::user()->id)
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->whereNull('task_closed_time')
                                      ->count();
            
            $arr_data['total_failed'] = DB::table('dx_tasks')
                                  ->where('task_employee_id', '=', Auth::user()->id)
                                  ->where(function($query) {
                                      $query->whereNull('task_closed_time')
                                            ->orWhere('task_closed_time', '>=', date('Y-n-d'));
                                  })
                                  ->where('due_date', '<', date('Y-n-d'))
                                  ->count();
            
            $arr_data['failed_solved'] = DB::table('dx_tasks')
                                      ->where('task_employee_id', '=', Auth::user()->id)
                                      ->where('due_date', '<', date('Y-n-d'))
                                      ->where('task_closed_time', '>=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['title'] = "MANI UZDEVUMI";
            $arr_data['url_all'] = "/skats_aktualie_uzdevumi";
            $arr_data['url_today'] = "/skats_mani_uzdevumi_sodien_termins";
            $arr_data['url_fail'] = "/skats_mani_uzdevumi_nokavetie";
            $arr_data['icon_class'] = "fa fa-user";
            $arr_data['color_class'] = "green-sharp";
            
            return $this->calculateStat($arr_data);
        }
        
        /**
         * Atgriež masīvu ar padoto darbinieku uzdevumu statusiem
         * 
         * @return type
         */
        private function getTeamTasks() {
            
            DB::setFetchMode(PDO::FETCH_ASSOC);
            
            $arr_team_members = DB::table('dx_users')
                                ->select('id')
                                ->where('manager_id', '=', Auth::user()->id)
                                ->get();
            
            DB::setFetchMode(PDO::FETCH_CLASS);
                                
            $arr_data = array();
            
            if (count($arr_team_members) == 0) {
                return $arr_data; // nav neviena padotā
            }
            
            $arr_data['total_all'] = DB::table('dx_tasks')
                                  ->whereIn('task_employee_id', $arr_team_members)
                                  ->whereNull('task_closed_time')
                                  ->count();
            
            $arr_data['due_today'] = DB::table('dx_tasks')
                                      ->whereIn('task_employee_id', $arr_team_members)
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['due_today_undone'] = DB::table('dx_tasks')
                                      ->whereIn('task_employee_id', $arr_team_members)
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->whereNull('task_closed_time')
                                      ->count();
            
            $arr_data['total_failed'] = DB::table('dx_tasks')
                                  ->whereIn('task_employee_id', $arr_team_members)
                                  ->where(function($query) {
                                      $query->whereNull('task_closed_time')
                                            ->orWhere('task_closed_time', '>=', date('Y-n-d'));
                                  })
                                  ->where('due_date', '<', date('Y-n-d'))
                                  ->count();
            
            $arr_data['failed_solved'] = DB::table('dx_tasks')
                                      ->whereIn('task_employee_id', $arr_team_members)
                                      ->where('due_date', '<', date('Y-n-d'))
                                      ->where('task_closed_time', '>=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['title'] = "PADOTO UZDEVUMI";
            $arr_data['url_all'] = "/skats_padoto_aktualie_uzdevumi";
            $arr_data['url_today'] = "/skats_padoto_sodienas_uzdevumi";
            $arr_data['url_fail'] = "/skats_padoto_kavetie_uzdevumi";
            $arr_data['icon_class'] = "fa fa-users";
            $arr_data['color_class'] = "red-haze";
            
            return $this->calculateStat($arr_data);
        }        
        
         /**
         * Atgriež masīvu ar visa uzņēmuma uzdevumu statusiem
         * 
         * @return type
         */
        private function getCompanyTasks() {
            $arr_data = array();
            
            $arr_data['total_all'] = DB::table('dx_tasks')
                                  ->whereNull('task_closed_time')
                                  ->count();
            
            $arr_data['due_today'] = DB::table('dx_tasks')
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['due_today_undone'] = DB::table('dx_tasks')
                                      ->where('due_date', '=', date('Y-n-d'))
                                      ->whereNull('task_closed_time')
                                      ->count();
            
            $arr_data['total_failed'] = DB::table('dx_tasks')
                                  ->where(function($query) {
                                      $query->whereNull('task_closed_time')
                                            ->orWhere('task_closed_time', '>=', date('Y-n-d'));
                                  })
                                  ->where('due_date', '<', date('Y-n-d'))
                                  ->count();
            
            $arr_data['failed_solved'] = DB::table('dx_tasks')
                                      ->where('due_date', '<', date('Y-n-d'))
                                      ->where('task_closed_time', '>=', date('Y-n-d'))
                                      ->count();
            
            $arr_data['title'] = "Uzņēmuma UZDEVUMI";
            $arr_data['url_all'] = "/skats_uznemuma_uzdevumi_aktualie";
            $arr_data['url_today'] = "/skats_uznemuma_uzdevumi_sodienas";
            $arr_data['url_fail'] = "/skats_uznemuma_uzdevumi_kavetie";
            $arr_data['icon_class'] = "fa fa-building-o";
            $arr_data['color_class'] = "blue-sharp";
            
            return $this->calculateStat($arr_data);
        }
        
        /**
         * Aprēķina procentuālās vērtības
         * 
         * @param Array $arr_data Masīvs ar uzdevumu statusiem
         * @return Array Papildināts masīvs ar procentuālajām vērtībām
         */
        private function calculateStat($arr_data) {
            
            $arr_data["percent_today"] = ($arr_data['due_today'] == 0) ? 0 : round(($arr_data['due_today'] - $arr_data['due_today_undone'])/$arr_data['due_today'] * 100);
            $arr_data["percent_fail"] = ($arr_data['total_failed'] ==0) ? 0 : round($arr_data['failed_solved']/$arr_data['total_failed']*100);
            $arr_data["failed_todo"] = $arr_data['total_failed'] - $arr_data['failed_solved'];
                    
            return $arr_data;
        }
        
    }
}