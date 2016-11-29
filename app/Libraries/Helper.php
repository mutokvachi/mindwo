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
         * @throws Exceptions\DXCustomException
         */
        public static function checkSaveRights($list_id)
        {
            $right = Rights::getRightsOnList($list_id);

            if ($right == null || $right->is_new_rights == 0) {
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
        public static function getDateFromCode($day_code, $month_nr) {
            $now = Carbon::now();
            if (is_numeric($day_code)) {
                    $dat = $now->year . '-' . $month_nr . '-' . $day_code;
            }
            else {
                // last day
                $dat_month = $now->year . '-' . $month_nr . '-01';
                $dat = date("Y-m-t", strtotime($dat_month));
            }
            
            return $dat;
        }

    }

}
