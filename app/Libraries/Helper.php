<?php

namespace App\Libraries
{

    use DB;
    use App\Exceptions;
    use Request;
    use Config;
    use App\Http\Controllers\BoxController;
    use Auth;
    use Carbon\Carbon;
    use Log;
    use Illuminate\Support\Facades\Schema;
    
    /**
     * Palīgfunkciju klase
     */
    class Helper
    {
        
        /**
         * Dzēš ierakstu no datu bāzes un izveido vēstures ierakstu, ja paredzēts veidot vēsturi
         * 
         * @param object $table_row Tabulas objekts
         * @param array $fields Masīvs ar laukiem
         * @param integer $item_id Dzēšamā ieraksta ID
         * @throws Exceptions\DXCustomException
         * @throws \App\Libraries\Exception
         */
        public static function deleteItem($table_row, $fields, $item_id)
        {
            try {
                $history = new DBHistory($table_row, $fields, null, $item_id);
                $history->makeDeleteHistory();
                
                DB::table($table_row->table_name)->where('id', '=', $item_id)->delete();
            }
            catch (\Exception $e) {
                if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                    throw new Exceptions\DXCustomException(trans('errors.cant_delete'));
                }
                else {
                    throw $e;
                }
            }
        }

        /**
         * Check if config option is set on for slidable menu. 
         * If set, then sets global views parameters regarding slidable menu
         * 
         * @return void
         */
        public static function setBreadcrumbViewGlobals()
        {

            $is_slidable_menu = Config::get('dx.is_slidable_menu', false);

            if (!$is_slidable_menu) {
                return;
            }

            $sliderMenu = BoxController::generateSlideMenu();
            view()->share('slidable_htm', $sliderMenu);
            view()->share('breadcrumb', Helper::getBreadcrumb(Request::url()));
            view()->share('is_slidable_menu', $is_slidable_menu);
        }

        /**
         * Gets array with menu items ordered starting from root till provided element
         * @param string $url Requested page url
         * @return array Menu items (id and title)
         */
        public static function getBreadcrumb($url)
        {
            $parts = explode("/", $url);

            $route_url = $parts[count($parts) - 1];

            if (strpos($route_url, 'skats_') !== false) {
                // view
                $view_id = str_replace("skats_", "", $route_url);

                $view_row = getViewRowByID($url, $view_id);

                $menu_row = DB::table('dx_menu')->where('list_id', '=', $view_row->list_id)->first();
            }
            else {
                // regular page
                $menu_row = DB::table('dx_menu')->where('url', '=', $route_url)->first();
            }

            $arr_items = [];
            if ($menu_row) {
                $arr_items = Helper::getMenuParent($menu_row, $arr_items);
            }

            return $arr_items;
        }

        /**
         * Gets menu parent items as array
         * @param object $menu_row Current menu row
         * @param array $arr_items Menu items
         * @return array Menu items
         */
        private static function getMenuParent($menu_row, $arr_items)
        {

            array_unshift($arr_items, ["id" => $menu_row->id, "title" => $menu_row->title]);
            if ($menu_row->parent_id) {
                $row = DB::table('dx_menu')->where('id', '=', $menu_row->parent_id)->first();
                if ($row) {
                    return Helper::getMenuParent($row, $arr_items);
                }
            }

            return $arr_items;
        }

        /**
         * Validates if user have rights to insert new items in the register
         * 
         * @param integer $list_id Register id
         * @param boolean $check_for_import Is it needed to check importing rights
         * @throws Exceptions\DXCustomException
         */
        public static function checkSaveRights($list_id, $check_for_import = 0)
        {
            $right = Rights::getRightsOnList($list_id);

            if ($right == null || $right->is_new_rights == 0 || ($check_for_import && $right->is_import_rights == 0)) {
                $list_name = DB::table('dx_lists')->where('id', '=', $list_id)->first()->list_title;
                throw new Exceptions\DXCustomException(sprintf(trans('errors.no_rights_to_insert_imp'), $list_name));
            }
        }

        /**
         * Formatē kataloga ceļu tā, lai tas vienmēr beidzas ar slīpsvītru un izmanto OS atbilstošu slīpsvītras veidu
         * 
         * @param string $folder Kataloga ceļš
         * @return string Kataloga ceļš, beidzas ar slīpsvītru
         */
        public static function folderSlash($folder)
        {
            $folder = trim($folder);

            if (DIRECTORY_SEPARATOR == "\\") {
                $folder = str_replace("/", DIRECTORY_SEPARATOR, $folder);
            }
            else {
                $folder = str_replace("\\", DIRECTORY_SEPARATOR, $folder);
            }

            $folder .= DIRECTORY_SEPARATOR;
            $folder = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $folder);

            return $folder;
        }
        
        /**
         * Returns path to current user small avatar
         * If user does not have picture, will be used default from assets
         * 
         * @return string Path to current user small avatar
         */
        public static function getUserAvatarSmall() {
            if (Auth::user()->picture_guid) {
                return "formated_img/small_avatar/" . Auth::user()->picture_guid;
            }
            else {
                return "assets/global/avatars/default_avatar_small.jpg";
            }
        }
        
         /**
         * Returns path to employee avatar
         * If employee does not have picture, will be used default from assets
         * 
         * @return string Path to emoloyee big avatar
         */
        public static function getEmployeeAvatarBig($picture) {
            if ($picture) {
                return "formated_img/small_avatar/" . $picture;
            }
            else {
                return "assets/global/avatars/default_avatar_big.jpg";
            }
        }
        
        /**
         * Builds date from given month and day numbers
         * 
         * @param string $day_code Day number (or "LAST")
         * @param integer $month_nr Month number
         * @return string Date in format yyyy-mm-dd
         */
        public static function getDateFromCode($year, $day_code, $month_nr) {
            $now = Carbon::now(Config::get('dx.time_zone'));
            
            if (!($year > 0)) {
                $year = $now->year;
            }
            
            if (is_numeric($day_code)) {
                    $dat = $year . '-' . $month_nr . '-' . $day_code;
            }
            else {
                // last day
                $dat_month = $year . '-' . $month_nr . '-01';
                $dat = date("Y-m-t", strtotime($dat_month));
            }
            
            return $dat;
        }
        
        /**
         * Checks if user have access to public info
         * 
         * @return boolean True - user have access, False - user do not have access
         */
        public static function isUserPublicAccessOk() {
            $is_all_login_required = Config::get('dx.is_all_login_required', false);
            
            if (!$is_all_login_required) {                
                return true; // no login required for public access info
            }
            
            if (Auth::check()) {
                
                if (Auth::user()->id == Config::get('dx.public_user_id', 0)) {
                    Log::info("AUTH: it is required login for all info but here we have public user ID - lets deny access");
                    return false; // it is required login for all info but here we have public user ID - lets deny access
                }
                return true; // is authentificated
            }
            else {
                Log::info("AUTH: all info must have authorized access but this user is not loged in. URL: " . Request::url() . " METHOD: " . Request::method());
                return false; // all info must have authorized access but this user is not loged in
            }
        }
        
        /**
        * Atgriež pazīmi, vai formas reģistram ir definēts kāds skats, kas izmantojams WORD ģenerēšanas lauku sarakstam
        * 
        * @param integer $list_id  Reģistra ID
        * @return int 0 - nav Word ģenerēšana; 1 - ir Word ģenerēšana
        */
        public static function getWordGenerBtn($list_id)
        {
            $is_word_generation_btn = 0;
            $view_row = DB::table('dx_doc_templates')->where('list_id', '=', $list_id)->first();
            if ($view_row) {
                $is_word_generation_btn = 1;
            }

            return $is_word_generation_btn;
        }

        /**
         * Gets info tasks added to item
         * 
         * @param integer $list_id List ID
         * @param integer $item_id Item ID
         * @param string $table_name List table name
         * @return array Array with info tasks or null of nothing found
         */
        public static function getInfoTasks($list_id, $item_id, $table_name) {
            $info_tasks = null;
            
            if ($item_id != 0 && Schema::hasColumn($table_name, 'created_user_id')) {

                $creator_id = DB::table($table_name)->select('created_user_id')->where('id','=',$item_id)->first()->created_user_id;

                $info_tasks = DB::table('dx_tasks as t')
                                ->select('u.display_name', 't.task_closed_time')
                                ->join('dx_users as u', 't.task_employee_id', '=', 'u.id')
                                ->where('t.list_id', '=', $list_id)
                                ->where('t.item_id', '=', $item_id)
                                ->where('t.task_type_id', '=', \App\Http\Controllers\TasksController::TASK_TYPE_INFO)                            
                                ->where('t.task_employee_id', "!=", $creator_id)
                                ->orderBy('u.display_name', 't.task_closed_time')
                                ->distinct()
                                ->get();
                
                $arr_uniq = [];
                foreach($info_tasks as $task) {
                    if (array_search($task->display_name, $arr_uniq)) {
                        $task->display_name = "";
                    }else {
                        array_push($arr_uniq, $task->display_name);
                    }
                }

                $info_tasks = array_filter($info_tasks, function($value) { return strlen($value->display_name) > 0; });
            }
            
            return $info_tasks;
        }
        
        /**
         * Loads holiday array
         * @param ineger $country_id Employee country ID
         * @return array
         */
        public static function getHolidaysArray($country_id) {
            $rows = DB::table('dx_holidays as h')
                                   ->select(
                                           'h.is_several_days', 
                                           'm1.nr as month_from_nr', 
                                           'd1.code as day_from_code', 
                                           'm2.nr as month_to_nr', 
                                           'd2.code as day_to_code',
                                           'h.from_year',
                                           'h.to_year',
                                           'h.country_id',
                                           'h.id as holiday_id'
                                           )
                                   ->leftJoin('dx_months as m1', 'h.from_month_id', '=', 'm1.id')
                                   ->leftJoin('dx_month_days as d1', 'h.from_day_id', '=', 'd1.id')
                                   ->leftJoin('dx_months as m2', 'h.to_month_id', '=', 'm2.id')
                                   ->leftJoin('dx_month_days as d2', 'h.to_day_id', '=', 'd2.id')
                                   ->where(function($query) use ($country_id) {
                                        if ($country_id) {
                                            $query->whereNull('h.country_id')
                                              ->orWhere('h.country_id', '=', $country_id); 
                                        }
                                   })                                   
                                   ->orderBy('m1.nr')
                                   ->orderBy('d1.code')
                                   ->get();            
            
            foreach($rows as $holiday) {                
                
                $holiday->date_from = Helper::getDateFromCode($holiday->from_year, $holiday->day_from_code, $holiday->month_from_nr);
                
                if (!$holiday->is_several_days) {
                    $holiday->date_to = $holiday->date_from;
                }
                else {
                    $holiday->date_to = Helper::getDateFromCode($holiday->to_year, $holiday->day_to_code, $holiday->month_to_nr);
                }
            }
            
            return json_decode(json_encode($rows), true);            
            
        }
        
        /**
         * Return employee status information (active, left, potential)
         * 
         * @param DateTime $valid_from_date Valid from
         * @param DateTime $termination_date
         * @return Array Array with status information: 'button' - button label, 'class' - button class name, 'title' - hover hint text
         */
        public static function getEmployeeStatus($valid_from_date, $termination_date) {
            $now = Carbon::now(Config::get('dx.time_zone'));
            $valid_from = ($valid_from_date) ? Carbon::createFromFormat('Y-m-d', $valid_from_date) : Carbon::createFromFormat('Y-m-d', '1900-01-01');

            if($termination_date)
            {
                    $result = [
                            'button' => trans('empl_profile.avail_left'),
                            'class' => 'grey dx-status-left',
                            'title' => sprintf(trans('empl_profile.hint_left'), short_date($termination_date))
                    ];
            }
            elseif($now->gte($valid_from) && !$termination_date)
            {
                    $result = [
                            'button' => trans('empl_profile.avail_active'),
                            'class' => 'green-jungle dx-status-active',
                            'title' => trans('empl_profile.hint_active')
                    ];
            }
            else
            {
                    $result = [
                            'button' => trans('empl_profile.avail_potential'),
                            'class' => 'yellow-lemon dx-status-potential',
                            'title' => trans('empl_profile.hint_potential')
                    ];
            }

            return $result;
        }
    }

}
