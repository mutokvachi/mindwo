<?php

namespace App\Libraries\Structure
{
    use DB;
    use Request;
    
    /**
     * Sistēmas dokumentācijas ģenerēšanas klase
     */
    class DocGenerator
    {

        /**
         * Pazīme, vai ģenerēto dokumentāciju atgriezt kā HTML tekstu (true - html teksts, false - skata objekts)
         * @var boolean 
         */
        public $is_html_return = false;
        
        /**
         * Ģenerē lietotāja rokasgrāmatu
         * 
         * @return mixed Ģenerētā instrukcija (HTMLteksts vai kā View objekts)
         */
        public function generateManual()
        {
            set_time_limit(60);

            $groups_rows = DB::table('dx_lists_groups')->orderBy('order_index')->get();

            foreach ($groups_rows as $group) {
                $group->rows_lists = $this->getGroupLists($group->id);
            }

            $ret = view('structure.ppa.doc_manual', [
                'groups_rows' => $groups_rows,
                'page_title' => 'Lietotāja rokasgrāmata'
            ]);
            
            if ($this->is_html_return) {
                return $ret->render();
            }
            
            return $ret;
        }

        /**
         * Ģemerē sistēmas PPA dokumentāciju
         * 
         * @param boolean $is_clean_html Vai ģenerēt tīru HTML (bez CMS saskarnes). True - tīrs HTMl, False - CMS saskarne
         * @return mixed Ģenerētā PPA dokumentācija (HTMLteksts vai kā View objekts)
         */
        public function generatePPA($is_clean_html)
        {
            set_time_limit(60);

            $begin_pages_rows = DB::table('dx_ppa_texts')->where('is_page', '=', 1)->where('is_begin', '=', 1)->orderBy('order_index')->get();
            $end_pages_rows = DB::table('dx_ppa_texts')->where('is_page', '=', 1)->where('is_begin', '=', 0)->orderBy('order_index')->get();
            
            foreach($begin_pages_rows as $page) {
                $page->formated_description = format_html_img(Request::root(), $page->html_description);
            }
            
            foreach($end_pages_rows as $page) {
                $page->formated_description = format_html_img(Request::root(), $page->html_description);
            }
            
            $components_rows = DB::table('dx_ppa_components')->orderBy('order_index')->get();

            foreach ($components_rows as $component) {
                $component->rows_modules = $this->getModules($component->id);
                $component->html_interface = format_html_img(Request::root(), $component->html_interface);
                
                if (!$component->is_generate) {
                    $component->html_description = format_html_img(Request::root(), $component->html_description);
                }
            }

            $list_roles_rows = DB::table('dx_roles')->get();

            foreach ($list_roles_rows as $list_role) {
                $list_role->rows_lists = $this->getRoleLists($list_role->id);
                $list_role->rows_pages = $this->getRolePages($list_role->id);
                $list_role->rows_specs = $this->getRoleSpecs($list_role->id);
            }
            
            if ($this->is_html_return) {
                return view('structure.ppa.doc_ppa_html', [
                    'begin_pages' => $begin_pages_rows,
                    'end_pages' => $end_pages_rows,
                    'components_rows' => $components_rows,
                    'list_roles_rows' => $list_roles_rows,
                    'page_title' => 'Programmatūras Projektējuma Apraksts'
                ])->render();
            }
            else {
                $view = 'structure.ppa.doc_ppa';
                if ($is_clean_html) {
                    $view = 'structure.ppa.doc_ppa_clean';
                }
                
                return view($view, [
                    'begin_pages' => $begin_pages_rows,
                    'end_pages' => $end_pages_rows,
                    'components_rows' => $components_rows,
                    'list_roles_rows' => $list_roles_rows,
                    'page_title' => 'Programmatūras Projektējuma Apraksts'
                ]);
            }
        }

        /**
         * Izgūst visus norādītās lomas reģistrus un to tiesības
         * 
         * @param integer $role_id Lomas ID
         * @return Array Masīvs ar reģistriem un to tiesībām
         */
        private function getRoleLists($role_id)
        {
            return DB::table('dx_roles_lists as rl')
                            ->select(DB::raw('rl.list_id, l.list_title, rl.is_new_rights, rl.is_edit_rights, rl.is_delete_rights'))
                            ->join('dx_lists as l', 'rl.list_id', '=', 'l.id')
                            ->join('dx_lists_groups as lg', 'l.group_id', '=', 'lg.id')
                            ->where('lg.is_not_in_docs', '=', 0)
                            ->where('rl.role_id', '=', $role_id)
                            ->orderBy('l.list_title')
                            ->get();
        }

        /**
         * Izgūst visas norādītās lomas speciālo funkciju tiesības
         * 
         * @param integer $role_id Lomas ID
         * @return Array Masīvs ar speciālajām funkcijām
         */
        private function getRoleSpecs($role_id)
        {
            return DB::table('dx_custom_php as cp')
                            ->select(DB::raw('cp.id, cp.title, cp.description'))
                            ->where('cp.role_id', '=', $role_id)
                            ->orderBy('cp.title')
                            ->get();
        }

        /**
         * Izgūst visas norādītās lomas lapas
         * 
         * @param integer $role_id Lomas ID
         * @return Array Masīvs ar lapām
         */
        private function getRolePages($role_id)
        {
            return DB::table('dx_roles_pages as rp')
                            ->select(DB::raw('rp.page_id, p.title'))
                            ->join('dx_pages as p', 'rp.page_id', '=', 'p.id')
                            ->where('rp.role_id', '=', $role_id)
                            ->orderBy('p.title')
                            ->get();
        }

        /**
         * Izgūst visus norādītās komponentes moduļus
         * 
         * @param type $component_id Komponentes ID
         * @return Array Masīvs ar moduļiem
         */
        private function getModules($component_id)
        {
            $modules_rows = DB::table('dx_ppa_modules')->where('component_id', '=', $component_id)->orderBy('order_index')->get();
            foreach ($modules_rows as $module) {
                $module->rows_groups = $this->getGroups($module->id);
            }

            return $modules_rows;
        }

        /**
         * Izgūst norādītā moduļa visas datu grupas
         * 
         * @param integer $module_id Moduļa ID
         * @return Array Masīvs ar datu grupām
         */
        private function getGroups($module_id)
        {
            $groups_rows = DB::table('dx_lists_groups')
                           ->where('module_id', '=', $module_id)
                           ->where('is_not_in_docs', '=', 0)
                           ->orderBy('order_index')
                           ->get();
            foreach ($groups_rows as $group) {
                $group->rows_lists = $this->getGroupLists($group->id);
            }

            return $groups_rows;
        }

        /**
         * Izgūst norādītās datu grupas visus reģistrus un ar tiem saistīto informāciju
         * 
         * @param integer $group_id Datu grupas ID
         * @return Array Masīvs ar reģistriem
         */
        private function getGroupLists($group_id)
        {
            $lists_rows = DB::table('dx_lists')->where('group_id', '=', $group_id)->orderBy('list_title')->get();

            foreach ($lists_rows as $list) {
                $list->row_form = DB::table('dx_forms')->where('list_id', '=', $list->id)->first();
                $list->rows_sections = $this->getFormSections($list->row_form->id);
                $list->rows_form_fields = $this->getFormFields($list->row_form->id);
                $list->menu_path = $this->getMenuPath($list->id, 0);
                $list->rows_ref_sections = $this->getListSections($list->id);
                $list->row_default_view = $this->getDefaultView($list->id);
                $list->rows_scripts = $this->getFormScripts($list->row_form->id);
                $list->rows_uses_lists = $this->getUsesLists($list->id);
                $list->rows_used_by_lists = $this->getUsedByLists($list->id);
            }

            return $lists_rows;
        }

        /**
         * Izgūst norādītā reģistra noklusēto skatu
         * 
         * @param integer $list_id Reģistra ID
         * @return Array Masīvs ar skata informāciju
         */
        private function getDefaultView($list_id)
        {
            return DB::table('dx_views')
                            ->where('list_id', '=', $list_id)
                            ->where('is_default', '=', 1)
                            ->where('is_hidden_from_main_grid', '=', 0)
                            ->first();
        }

        /**
         * Izgūst formas lauku masīvu
         * 
         * @param integer $form_id Formas ID
         * @return Array Masīvs ar formas laukiem
         */
        private function getFormFields($form_id)
        {
            return DB::table('dx_forms_fields as ff')
                            ->select(DB::raw('lf.title_form, lf.max_lenght, lf.is_required, ff.is_readonly, lf.default_value, lf.hint, lf.is_image_file, lf.is_multiple_files, ft.title as type_title, ft.sys_name as type_code, rl.list_title as rel_list_title, lf.rel_list_id'))
                            ->leftJoin('dx_lists_fields as lf', 'lf.id', '=', 'ff.field_id')
                            ->leftJoin('dx_field_types as ft', 'lf.type_id', '=', 'ft.id')
                            ->leftJoin('dx_lists as rl', 'lf.rel_list_id', '=', 'rl.id')
                            ->where('ff.form_id', '=', $form_id)
                            ->where('ff.is_hidden', '=', 0)
                            ->orderBy('ff.order_index')
                            ->get();
        }

        /**
         * Izgūst formas sadaļu masīvu
         * 
         * @param integer $form_id Formas ID
         * @return Array Masīvs ar formas sadaļām
         */
        private function getFormSections($form_id)
        {
            return DB::table('dx_forms_tabs as ft')
                            ->select(DB::raw('ft.title as section_title, l.list_title, l.id as list_id, l.hint as list_hint'))
                            ->leftJoin('dx_lists as l', 'ft.grid_list_id', '=', 'l.id')
                            ->orderBy('order_index')
                            ->where('ft.form_id', '=', $form_id)
                            ->get();
        }

        /**
         * Izgūst formas speciālo funkciju (JavaScript) masīvu
         * 
         * @param integer $form_id Formas ID
         * @return Array Masīvs ar formas speciālajām funkcijām
         */
        private function getFormScripts($form_id)
        {
            return DB::table('dx_forms_js')
                            ->where('form_id', '=', $form_id)
                            ->get();
        }

        /**
         * Izgūst masīvu ar formu sadaļām, kurās ir izmantots norādītais reģistrs
         * 
         * @param integer $list_id Reģistra ID
         * @return Array Masīvs ar formu sadaļām, kurā izmantots reģistrs
         */
        private function getListSections($list_id)
        {
            return DB::table('dx_forms_tabs as ft')
                            ->select(DB::raw('l.list_title, l.id as list_id'))
                            ->leftJoin('dx_forms as f', 'ft.form_id', '=', 'f.id')
                            ->leftJoin('dx_lists as l', 'f.list_id', '=', 'l.id')
                            ->orderBy('l.list_title')
                            ->where('ft.grid_list_id', '=', $list_id)
                            ->get();
        }

        /**
         * Izgūst masīvu ar reģistriem, kuru saistītos datus izmanto norādītais reģistrs
         * 
         * @param integer $list_id Reģistra ID
         * @return Array Masīvs ar izmantotajiem reģistriem
         */
        private function getUsesLists($list_id)
        {
            return DB::table('dx_lists_fields as lf')
                            ->select(DB::raw('l.list_title, l.id as list_id'))
                            ->join('dx_lists as l', 'lf.rel_list_id', '=', 'l.id')
                            ->join('dx_lists_groups as lg', 'l.group_id', '=', 'lg.id')
                            ->where('lg.is_not_in_docs', '=', 0)
                            ->where('lf.list_id', '=', $list_id)                            
                            ->orderBy('l.list_title')
                            ->distinct()
                            ->get();
        }

        /**
         * Izgūst masīvu ar reģistriem, kas izmanto norādītā reģistra datus
         * 
         * @param integer $list_id Reģistra ID
         * @return Array Masīvs ar reģistriem
         */
        private function getUsedByLists($list_id)
        {
            return DB::table('dx_lists_fields as lf')
                            ->select(DB::raw('l.list_title, l.id as list_id'))
                            ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
                            ->join('dx_lists_groups as lg', 'l.group_id', '=', 'lg.id')
                            ->where('lg.is_not_in_docs', '=', 0)
                            ->where('lf.rel_list_id', '=', $list_id)                            
                            ->orderBy('l.list_title')
                            ->distinct()
                            ->get();
        }

        /**
         * Izgūst rekursīvi norādi (secīgi nospiežamas izvēlnes), kā atvērt reģistru no sistēmas galvenās izvēlnes
         * @param integer $list_id Reģistra ID
         * @param integer $parent_id Vecāka elementa ID
         * @return string Norādes pilnais ceļš kā teksts
         */
        private function getMenuPath($list_id, $parent_id)
        {

            $menu_row = DB::table('dx_menu')
                            ->where(function($query) use ($list_id, $parent_id)
                            {
                                if ($parent_id) {
                                    $query->where('id', '=', $parent_id);
                                }
                                else {
                                    $query->where('list_id', '=', $list_id);
                                }
                            })->first();

            if ($menu_row) {
                $parent = $this->getMenuPath(0, $menu_row->parent_id);
                return $parent . ((strlen($parent) > 0) ? ' &rarr; ' : '') . $menu_row->title;
            }
            else {
                return '';
            }
        }

    }

}