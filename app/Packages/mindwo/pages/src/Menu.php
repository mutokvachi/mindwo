<?php

namespace mindwo\pages
{

  use Auth;
    use DB;
    use Config;
    use Request;
    use Illuminate\Support\Facades\File;
    use Detection\MobileDetect;
    
    /**
     *
     * Menu izveidošanas klase
     *
     *
     * Klase nodrošina hierarhiska menu izveidošanu      |
     *
     */
    class Menu
    {

        private $menu_html = "";
        private $user_id = 0;
        
        /**
         * Pazīme, vai klase izsaukta navigācijas konfigurācijas režīmā
         * @var boolean
         */
        private $is_builder = 0;
        
        /**
         * Blade skats, kas jāizmanto navigācijas koka veidošanai
         * @var string
         */
        private $blade_view = 'mindwo/pages::main.menu_item';
        
        /**
         * Portāla ID - no tabulas dx_menu_groups
         * 
         * @var integer
         */
        private $site_id = 0;
        
        /**
         * Menu klases konstruktors
         * Uzstāda noklusēto lietotāju un inicializē menu HTML veidošanu
         * 
         * @param boolean $is_builder Pazīme, vai menu jāzīmē priekš konstruktora
         * @param integer $site_id Portāla ID (no tabulas dx_menu_groups)
         * @return void
         */
        public function __construct($is_builder = 0, $site_id = 0)
        {
            if (Auth::check()) {
                $this->user_id = Auth::user()->id;
            }
            else {
                $this->user_id = Config::get('dx.public_user_id');
            }
            $this->is_builder = $is_builder;
            $this->site_id = $site_id;
            $this->blade_view = $is_builder ? 'mindwo/pages::main.builder_menu_item' : 'mindwo/pages::main.menu_item';
                        
            $this->menu_html = $this->is_builder ? $this->makeMenu(0, 0)['htm'] : $this->getMenuFromFile();
        }

        /**
         * Atgriež menu HTML
         * 
         * @return string Menu HTMLs
         */
        public function getHTML()
        {
            return $this->menu_html;
        }

        /**
         * Izgūst lietotāja menu no datnes.
         * Ja menu HTML datne vēl nav izveidota, tad izveido menu html ar funkciju un saglabā rezultātu datnē
         * 
         * @return string Menu HMTLs
         */
        private function getMenuFromFile()
        {
            $menuPath = $this->getMenuCachePath();
            
            if (File::isFile($menuPath)) {
                $db_change = strtotime(DB::table('in_last_changes')->where('code', '=', 'MENU')->first()->change_time);
                $file_change = File::lastModified($menuPath);

                if ($file_change >= $db_change) {
                    return File::get($menuPath);
                }
            }

            $htm = $this->makeMenu(0, 0)['htm'];
            File::put($menuPath, $htm);
            return $htm;
        }
        
        /**
         * Izgūst menu cache datnes pilno ceļu
         * Pārbauda, arī vai pieslēgums no mobilā vai planšetes - katram savs config
         * 
         * @return type
         */
        private function getMenuCachePath() {
            $detect = new MobileDetect();
            $mob_prefix = "";
            
            if ($detect->isMobile() || $detect->isTablet()) {
                $mob_prefix = "mob_";
            }
            
            $database_name = Config::get('database.connections.' . Config::get('database.default') . '.database') . "_" . getRootForCache();
            
            return base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . $database_name . "_" . $mob_prefix . 'menu_' . $this->user_id . '.htm';
        }

        /**
         * Uzzīmē menu HTML - rekursīvi
         * 
         * @param integer $parent_id Vecāka ID
         * @param integer $level Rekursijas līmenis
         * @return Array Atgriež masību no 2 elementiem: hmt - menu HTMLs, is_active_set - pazīme, vai iestatīta aktīvā izvēlne
         */
        private function makeMenu($parent_id, $level)
        {
            $rows = $this->getMenuSQL($parent_id);

            $htm = "";

            $is_global_active_set = 0;

            foreach ($rows as $row) {
                $rez = $this->makeMenu($row->id, $level + 1);
                $child_htm = $rez['htm'];
                $is_active_set = $rez['is_active_set'];

                $href = "";
                $target = "";
                $selected = 0;

                if (strlen($child_htm) > 0) {
                    $href = 'javascript:;';
                }
                else if ($row->list_id > 0) {
                    $href = Request::root() . '/skats_' . $row->view_url;
                }
                else if (strlen($row->url) > 0) {
                    if ($row->is_target_blank == 1) {
                        $target = "target=_blank";
                    }

                    if (substr($row->url, 0) != "/") {
                        $row->url = "/" . $row->url;
                    }

                    $href = Request::root() . $row->url;
                }

                if (strlen($href) > 0 || $this->is_builder) {
                    if (Request::url() == $href) {
                        $is_active_set = 1;
                    }

                    $active = "";
                    $open = "";

                    if ($is_active_set == 1) {
                        if ($level == 0) {
                            $selected = 1;
                        }
                        $active = "active";
                        $open = "open";
                    }

                    $htm .= view($this->blade_view, [
                        'active' => $active,
                        'open' => $open,
                        'href' => $href,
                        'sub_items_htm' => $child_htm,
                        'icon_class' => $row->fa_icon,
                        'color' => $row->color,
                        'target' => '',
                        'title' => $row->title,
                        'selected' => $selected,
                        'level' => $level,
                        'list_id' => $row->list_id,
                        'view_id' => $row->view_id,
                        'menu_id' => $row->id,
                        'order_index' => $row->order_index,
                        'parent_id' => $row->parent_id
                    ])->render();
                }

                if ($is_active_set == 1) {
                    $is_global_active_set = 1;
                }
            }

            return ['htm' => $htm, 'is_active_set' => $is_global_active_set];
        }

        /**
         * Atgriež menu rindas no datu bāzes tabulas dx_menu
         *
         * @param  integer  $parent_id  Ja 0, tad atgriež visas pirmā līmeņa izvēlnes. Pretējā gadījumā atgriež tās izvēlnes, kurām vecāks vienāds ar parent_id
         * 
         * @return Object   Menu rindas
         */
        private function getMenuSQL($parent_id)
        {
            $where_sql = "";
            if ($parent_id == 0) {
                $where_sql = "is null AND ifnull(m.position_id, 1) = 1 ";
            }
            else {
                $where_sql = "=" . $parent_id;
            }

            $sql = "
                    SELECT DISTINCT
                             m.id
                   ";
            
            if ($this->is_builder) {
                $sql .= ", m.title";
            }            
            else {
                $sql .= ",case when m.is_title_hidden then '' else m.title end as title";
            }
            
            $sql .= "       ,ifnull(m.list_id,0) as list_id
                            ,ifnull(m.url, '') as url
                            ,m.is_target_blank
                            ,ifnull(m.full_path,'') as full_path
                            ,fa_icon
                            ,ifnull(v.url, v.id) as view_url
                            ,color
                            ,v.id as view_id
                            ,m.order_index
                            ,m.parent_id
                    FROM
                            dx_menu m
                            left join dx_views v on m.list_id = v.list_id AND v.is_default = 1
                    WHERE
                            m.parent_id " . $where_sql;
            
            if ($this->is_builder && $this->site_id) {
                $sql .= " AND m.group_id = " . $this->site_id;
            }
            else {
            
                $sql .= " 
                            AND (m.list_id is null or m.list_id in (select distinct rl.list_id from dx_users_roles ur inner join dx_roles_lists rl on ur.role_id = rl.role_id where ur.user_id = " . $this->user_id . "))
                            AND (m.group_id is null or m.group_id=" . Config::get('dx.menu_group_id', 0) . ")
                            AND (m.url is null or m.url in (select distinct p.url_title from dx_pages p inner join dx_roles_pages rp on rp.page_id = p.id inner join dx_users_roles ur on ur.role_id = rp.role_id where ur.user_id = " . $this->user_id . ")
                            or m.url not in (select p.url_title from dx_pages p)    
                            )
                            AND (m.role_id is null or m.role_id in (select distinct ur.role_id from dx_users_roles ur where ur.user_id = " . $this->user_id . "))";
                
            }
            
            $sql .= " ORDER BY
                            m.order_index
                    ";
            
            return DB::select($sql);
        }

    }

}