<?php

namespace App\Libraries\DataView
{
    use Config;
    use Request;
    
    /**
     * Helper class to get HTML for grid drawing - to avoid Blade views rendering performance issue
     */
    class Helper
    {
        /**
         * Form type ID (employee profile) from dx_forms_types
         */
        const FORM_TYPE_EMPLOYEE_PROFILE = 3;
        
        /**
         * Employee profile relative URL
         * @var string 
         */
        private $profile_url = "";
    
        /**
         * Helper class constructor
         */
        public function __construct() {
            $this->profile_url = Config::get('dx.employee_profile_page_url');
        }
    
        /**
         * Returns HTML for button with popup menu cell (for row operations edit, delete and view)
         * @param array $arr_args Array with arguments: item_id, dropup, form_type_id
         * 
         * @return string HTML for cell
         */
        public function getBtnsCol($arr_args) {
            
            $item_id = $arr_args['item_id'];
            $dropup = $arr_args['dropup'];
            $form_type_id = $arr_args['form_type_id'];
            
            $htm = "<td align='center'>
                        <input type='checkbox' class='dx-grid-input-check' dx_item_id='" .  $item_id . "'>&nbsp;
                        <div class='btn-group " . $dropup . "'>
                            <button type='button' class='btn btn-primary dropdown-toggle btn-xs' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true' style='color: #DDDDDD;'><i class='fa fa-cog'></i> <i class='fa fa-caret-down'></i></button>
                            <ul class='dropdown-menu dropdown-menu-right' style='z-index: 50000;'>";
            
            if ($form_type_id == self::FORM_TYPE_EMPLOYEE_PROFILE && $this->profile_url) {
                $htm .= "<li><a href='" . Request::root() . $this->profile_url . $item_id . "'><i class='fa fa-user'></i> " . trans('employee.lbl_open_profile') . "</a></li>";
            }
            else {
                $htm .= "<li><a href='javascript:;' class='dx-grid-cmd-view' dx_item_id='" . $item_id . "'><i class='fa fa-external-link'></i> " . trans('grid.menu_view') . "</a></li>
                                    <li><a href='javascript:;' class='dx-grid-cmd-edit' dx_item_id='" . $item_id . "'><i class='fa fa-edit'></i> " . trans('grid.menu_edit') . "</a></li>";

            }
            
            $htm .= "    <li><a href='javascript:;' class='dx-grid-cmd-delete' style='color: red;' dx_item_id='" . $item_id . "'><i class='fa fa-cut'></i> " . trans('grid.menu_delete') . "</a></li>
                            </ul>
                        </div>
                    </td>";
            
            return $htm;
        }
        
        /**
         * Returns HTML for file download cell
         * @param array $arr_args Array with arguments: is_pdf, item_id, list_id, field_id, cell_value
         * 
         * @return string HTML for cell
         */
        public function getFileCell($arr_args) {
            
            $is_pdf = (isset($arr_args['is_pdf']) && $arr_args['is_pdf']);
            $item_id = $arr_args['item_id'];
            $list_id = $arr_args['list_id'];
            $field_id = $arr_args['field_id'];
            $cell_value = $arr_args['cell_value'];
            
            if (!$is_pdf) {
                $htm = "<a href = 'JavaScript: download_file(" . $item_id . ", " . $list_id . ", " . $field_id . ");'><i class='glyphicon glyphicon-file'></i> " . e($cell_value) . "</a>";
            }
            else {
                $htm = "<a href='" . Request::root() . "/web/viewer.html?file=" . Request::root() . "/download_file_" . $item_id . "_" . $list_id . "_" . $field_id . ".pdf' target='_blank'>" . e($cell_value) . "</a>";
            }
            
            return $htm;
        }
        
        /**
         * Returns HTML for skype link cell
         * @param array $arr_args Array with arguments: cell_value
         * 
         * @return string HTML for cell
         */
        public function getSkypeCell($arr_args) {
            $cell_value = $arr_args['cell_value'];
            
            if (!$cell_value) {
                return "";
            }
            return "<a href='skype:" . e($cell_value) ."?chat' title='" . trans('fields.hint_skype') . "'><i class='fa fa-skype'></i> " . e($cell_value) . "</a>";
        }
        
        /**
         * Returns HTML for link (opend CMS form) cell
         * @param array $arr_args Array with arguments: item_id, cell_value
         * 
         * @return string HTML for cell
         */
        public function getLinkCell($arr_args) {
            $item_id = $arr_args['item_id'];
            $cell_value = $arr_args['cell_value'];
            
            return "<a href='javascript:;' class='dx-grid-cmd-view' dx_item_id='" . $item_id . "'>" . e($cell_value) . "</a>";
        }
        
         /**
         * Returns HTML for employee profile link cell
         * @param array $arr_args Array with arguments: item_id, cell_value
         * 
         * @return string HTML for cell
         */
        public function getLinkProfileCell($arr_args) {
            $item_id = $arr_args['item_id'];
            $cell_value = $arr_args['cell_value'];
            
            return "<a href='" . Request::root() . $this->profile_url . $item_id . "'>" . e($cell_value) . "</a>";
        }
        
        /**
         * Returns HTML for grid cell
         * @param array $arr_args Array with arguments: align, is_val_html, cell_value
         * 
         * @return string HTML for cell
         */
        public function getCell($arr_args) {
            $align = $arr_args['align'];
            $cell_value = $arr_args['cell_value'];
            $is_val_html = (isset($arr_args['is_val_html']) && $arr_args['is_val_html']);
            
            $htm = "<td align='{{ $align }}'>";
            if ($is_val_html) {
                $htm .= $cell_value; // not escaped
            }
            else {
                $htm .= e($cell_value);
            }
            $htm .= "</td>";
            
            return $htm;
        }
        
        /**
         * Returns HTML for grid row
         * @param array $arr_args Array with arguments: htm
         * 
         * @return string HTML for row
         */
        public function getRow($arr_args) {
            $htm = $arr_args['htm'];
            return "<tr>" . $htm . "</tr>";
        }
    }
}