<?php

namespace App\Libraries
{
    
    use App\Exceptions;
    use PhpOffice\PhpWord;
    use Webpatser\Uuid\Uuid;
    use App\Http\Controllers\FileController;
    use DB;
    use Illuminate\Support\Facades\File;
    use Auth;
    use PDO;

    use App\Libraries\DataView\Formatters;

    /**
     * Generates documents from Word templates
     */
    class DocGenerator
    {     

        /**
         * Generated file full path
         *
         * @var string
         */
        public $file_path = "";

        /**
         * Generated file name
         *
         * @var string
         */
        public $file_name = "";
        
        /**
         * Generated file GUID
         *
         * @var string
         */
        public $file_guid = "";

        /**
         * Template row from table dx_doc_templates
         *
         * @var array
         */
        private $template = null;

        /**
         * Data view from which will be taken insertable values for template
         *
         * @var array
         */
        private $data_view = null;

        /**
         * Array with item fields values
         *
         * @var array
         */
        private $data_rows = null;

        /**
         * Register ID for which document will be generated
         *
         * @var integer
         */
        private $list_id = 0;

        /**
         * Item ID
         *
         * @var integer
         */
        private $item_id = 0;

        /**
         * Files storage folder (non public access only)
         *
         * @var string
         */
        private $file_folder = "";

        /**
         * View processing object
         *
         * @var \App\Libraries\View
         */
        private $view_obj = null;

        /**
         * File field row object
         *
         * @var array
         */
        public $field_row = null;

        /**
         * Is generated document in PDF format
         *
         * @var boolean
         */
        private $is_pdf = false;

        /**
         * Generates document
         *
         * @param integer $list_id Register ID
         * @param integer $item_id Item ID
         * @param integer $template_row Template row from table dx_doc_templates
         * @return void
         */
        public function __construct($list_id, $item_id, $template_row) {
            $this->list_id = $list_id;
            $this->item_id = $item_id;

            $this->template = $template_row;

            $this->setDataView();
            $this->setViewDataArray();
            
            $this->file_folder = FileController::getFileFolder(0); // non public files folder

            if (!$this->template->file_guid) {
                $this->makePDF();
            }
            else {
                $this->makeDoc();
            }
        }

        /**
         * Store generated document in database
         *
         * @return \App\Libraries\DocGenerator
         */
        public function updateItem()
        {  
            $list = DB::table('dx_lists')->where('id', '=', $this->list_id)->first();

            $obj_row = DB::table('dx_objects')->where('id','=', $list->object_id)->first();
            
            $this->field_row = DB::table('dx_lists_fields')
                         ->where('list_id', '=', $this->list_id)
                         ->where('type_id','=', DBHelper::FIELD_TYPE_FILE)
                         ->where('is_word_generation','=', 1)
                         ->first();
            
            if (!$this->field_row)
            {
                throw new Exceptions\DXCustomException("Reģistram nav definēts datnes lauks ar Word ģenerēšanas iespēju!");
            }

            $file_guid_name = str_replace("_name", "_guid", $this->field_row->db_name);
                        
            $arr_update = [
                $this->field_row->db_name => $this->file_name, 
                $file_guid_name => $this->file_guid
            ];
            
            $dx_db = (new DB_DX())
                        ->list($this->list_id)
                        ->where('id', '=', $this->item_id)
                        ->update($arr_update);            
            
            DB::transaction(function () use ($dx_db){
                $dx_db->commitUpdate();
            });

            return $this;
        }

        /**
         * Returns document field HTML which will be returned by AJAX to update UI 
         *
         * @return string
         */
        public function getFieldHTM() {           
            
            $down_guid = Uuid::generate(4);
            DB::table('dx_downloads')->insertGetId([
                'user_id' => Auth::user()->id,
                'field_id' => $this->field_row->id,
                'item_id' => $this->item_id,
                'guid' => $down_guid,
                'init_time' => date('Y-n-d H:i:s')
            ]);
                    
            return  view('fields.file', [
                         'item_id' => $this->item_id, 
                         'list_id' => $this->list_id,
                         'field_id' => $this->field_row->id,
                         'item_field_remove' => str_replace('_name', '_removed', $this->field_row->db_name),
                         'item_field' => $this->field_row->db_name,
                         'item_value' => $this->file_name,
                         'is_disabled' => 1,
                         'class_exist' => "exists",
                         'is_required' => $this->field_row->is_required,
                         'is_pdf' => $this->is_pdf,
                         'down_guid' => $down_guid,
                         'is_item_editable' => true,
						 'is_crypted' => 0,
					     'masterkey_group_id' => 0
                    ])->render(); 
        }

        /**
         * Set view to be used for fields values for inserting in template
         *
         * @return void
         */
        private function setDataView() {
            $this->data_view =  DB::table('dx_views')
            ->where('list_id', '=', $this->list_id)                
            ->orderBy('is_for_word_generating', 'DESC')
            ->orderBy('is_default', 'DESC')
            ->first();
        }

        /**
         * Set data array with field values for inserting in template
         *
         * @return void
         */
        private function setViewDataArray()
        {
            $this->view_obj = new \App\Libraries\View($this->list_id, $this->data_view->id, Auth::user()->id);

            $sql = $this->view_obj->get_view_sql() . " AND id=" . $this->item_id;

            DB::setFetchMode(PDO::FETCH_ASSOC);            
            $this->data_rows = DB::select($sql)[0];
            DB::setFetchMode(PDO::FETCH_CLASS);
        }

        /**
         * Generates PDF file from HTML template - replaces fields with values
         *
         * @return void
         */
        private function makePDF() {
            $snappy = \App::make('snappy.pdf');
            $content = $this->template->html_template;

            $html = view('forms.pdf_template', ['content' => $content])->render();

            $this->file_name = $this->getFileName();
            $this->file_name .= ".pdf";

            $this->file_guid = Uuid::generate(4) . ".pdf";

            $this->file_path = $this->file_folder . $this->file_guid;

            $html = $this->replaceWithFieldsVals($html);
            $snappy->generateFromHtml($html, $this->file_path);

            $this->is_pdf = true;
        }

        /**
         * Prepares generated file name - replaces fields if they are used for file name formation
         *
         * @return string
         */
        private function getFileName() {

            if (!$this->template->title_file) {
                return $this->list_id . "_" . $this->item_id;
            }

            $title = $this->replaceWithFieldsVals($this->template->title_file);            
            $title = $this->replaceLatvian($title);

            return preg_replace("/[^a-zA-Z0-9.]/", "_", $title);
        }

        /**
         * Replaces fields with their values in given text 
         *
         * @param string $txt Initial text
         * @return string Formated text
         */
        private function replaceWithFieldsVals($txt) {
            preg_match_all("/\{(.*?)\}/", $txt, $matches);
            foreach($matches[1] as $item) {               
                $search = "\${" . $item . "}"; 
                $txt = str_replace($search, $this->getFieldValue($item), $txt);
            }

            return $txt;
        }

        /**
         * Replace latvian letters with latin letters
         *
         * @param string $str String to be formated
         * @return string String without latvian letters
         */
        private function replaceLatvian($str) {
            $str1 = "ā,č,ē,ģ,ī,ķ,ļ,ņ,š,ū,ž";
            $str2 = "a,c,e,g,i,k,l,m,s,u,z";

            $arr_str1 = explode(",", $str1);
            $arr_str2 = explode(",", $str2);

            for($i=0;$i<count($arr_str1); $i++) {
                $str = str_replace($arr_str1[$i], $arr_str2[$i], $str);
            }

            return $str;
        }

        /**
         * Generates Word file from template - replaces fields with values
         *
         * @return void
         */
        private function makeDoc() {
            $template_path = $this->file_folder . $this->template->file_guid;
            
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_path);
            
            $fld_arr = $templateProcessor->getVariables();

            foreach($fld_arr as $fld)
            {
                $templateProcessor->setValue($fld, $this->getFieldValue($fld));
            }
                        
            $this->file_name = $this->getFileName() . "." . File::extension($this->template->file_name);
            $this->file_guid = Uuid::generate(4) . "." . File::extension($this->template->file_name);

            $this->file_path = $this->file_folder . $this->file_guid;

            $templateProcessor->saveAs($this->file_path);
        }


        /**
        * Funkcija izgūst ieraksta lauku vērtības kā masīvu
        * 
        * @param string $fld_label Lauka nosaukums saraksta kolonnai
        * @param Array $row Ieraksta lauku vērtību masīvs
        * @return void
        */ 
        private function getFieldValue($fld_label)
        {                        
            for ($i=0; $i<count($this->view_obj->model);$i++)
            {
                if ($this->isFieldIncludable($this->view_obj->model[$i]) && $this->view_obj->model[$i]["label"] == $fld_label) // compares by list_title field
                {
                    $cell_obj = Formatters\FormatFactory::build_field($this->resetFieldType($this->view_obj->model[$i], $this->data_rows), $this->data_rows);

                    return $cell_obj->value;
                }
            }
            
            return "#FIELD ERROR#";
        }

        /**
         * Detects if field can be included in template
         *
         * @param array $model_row Field parameters array
         * @return boolean True - can be included, False - can't include
         */
        private function isFieldIncludable($model_row)
        {
            if (strlen($model_row["label"]) > 0 && strlen($model_row["name"]) > 0)
            {
                return true;
            }
            
            return false;
        }
        
        /**
        * Funkcija pārveido lauka tipu uz tekstu, ja tas ir file vai arī kā saite - jo Word nav nepieciešams attēlot saites vai lejupielādēt datnes
        * 
        * @param Array $model_row Masīvs ar lauka atribūtiem
        * @return Array Masīvs ar lauka atribūtiem (nepieciešamības gadījumā pamainīts lauka tips)
        */ 
        private function resetFieldType($model_row)
        {
            if ($model_row['type'] == 'file' || $model_row['is_link'])
            {
                $model_row['type'] = 'varchar';
            }
            
            return $model_row;
        }
    }
}