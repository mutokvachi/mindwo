<?php
namespace App\Libraries\DataView {
    
    use Config;
    use App\Exceptions;
    use PhpOffice\PhpWord;
    use Webpatser\Uuid\Uuid;
    use App\Http\Controllers\FileController;
    use DB;
    use Illuminate\Support\Facades\File;
    use Auth;
    use Log;
    
    class DataViewWord extends DataView {
        
        /**
        *
        * Word lauku izgūšanas klase
        *
        *
        * Objekts nodrošina lauku izgūšanu Word ģenerēšanai no sagataves
        *
        */
        
        public $item_id = 0;
        public $file_field = null; // tiks atgriezts ar JSON lai HTMLā varētu atjaunināt lauka izskatu
        
        private $file_name = "";
        private $file_guid = "";
        
        /**
        * Inicializē Word lauku klases objektu.
        * 
        * @param int $view_id Skata identifikatrs (no tabulas dx_views lauks id)
        * @param mixed $filter_data JSON formātā filtrēšanas lauku vērtības
        * @param string $session_guid Unikāla guid vērtība vai arī tukšums. SQL objekti tiek glabāti sesijā, lai uzlabotu ātrdarbību
        * @return void
        */
        public function __construct($view_id, $filter_data, $session_guid)
        {
            $this->initObjects($view_id, $filter_data, $session_guid);
        }
        
        /**
        * Funkcija atgriež HTML, kas tiks pārzīmēts saskarnē, lai attēlotu noģenerēto Word datni
        * 
        * @return string tukšums
        */
        public function getViewHtml() 
        {
            if ($this->item_id == 0)
            {
                throw new Exceptions\DXCustomException("Nav norādīts ieraksta identifikators!");
            }
                        
            $this->generateWord();
            
            return  view('fields.file', [
                         'item_id' => $this->item_id, 
                         'list_id' => $this->list_id,
                         'field_id' => $this->file_field->id,
                         'item_field_remove' => str_replace('_name', '_removed', $this->file_field->db_name),
                         'item_field' => $this->file_field->db_name,
                         'item_value' => $this->file_name,
                         'is_disabled' => 1,
                         'class_exist' => "exists",
                         'is_required' => $this->file_field->is_required
                    ])->render(); 
        }
        
        /**
        * Funkcijā tiek definēta SQL daļa datu kārtošanai
        * Word dati tiek atgriezti par vienu rindu, tādēļ kārtošana nav nepieciešama
        * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
        *
        * @return string SQL kārtošanas fragments (ORDER BY)
        */
        protected function getSortingSQL() 
        {
            return "";
        }

        /**
        * Funkcijā tiek definēta SQL daļa Excel datu porcijas lielumam (ierakstu skaits)
        * Maksimālais uz Excel eksportējamo ierakstu skaits ir 10000. Eksportēti tiek ieraksti sākot no pirmā (tātad, neņemot vērā saskarnē veikto ierakstu lapošanu)
        * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
        * 
        * @return string SQL ierakstu porcijas fragments (LIMIT)
        */        
        protected function getLimitSQL() 
        {            
            return " LIMIT 0, 1";
        }
        
        /**
        * Funkcija izgūst ieraksta lauku vērtības kā masīvu
        * 
        * @param string $fld_label Lauka nosaukums saraksta kolonnai
        * @param Array $row Ieraksta lauku vērtību masīvs
        * @return void
        */ 
        private function getFieldValue($fld_label, $row)
        {            
            $view = $this->view->view_obj;
                        
            for ($i=0; $i<count($view->model);$i++)
            {
                if ($this->isFieldIncludable($view->model[$i]) && $view->model[$i]["label"] == $fld_label) // compares by list_title field
                {
                    $cell_obj = Formatters\FormatFactory::build_field($this->resetFieldType($view->model[$i], $row), $row);

                    return $cell_obj->value;
                }
            }
            
            return "#NEKOREKTS LAUKS#"; // atgriežam šādu, lai Word var redzēt, ka kāds lauks nav pareizi norādīts
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
        
        /**
        * Funkcija ģenerē Word datni no sagataves - aizvieto laukus ar vērtībām no datu bāzes
        * 
        * @return void
        */ 
        private function generateWord()
        {
            $field_row = DB::table('dx_lists_fields')->where('list_id', '=', $this->list_id)->where('type_id','=', 12)->where('is_word_generation','=', 1)->first();
            
            if (!$field_row)
            {
                throw new Exceptions\DXCustomException("Reģistram nav definēts datnes lauks ar Word ģenerēšanas iespēju!");
            }
            
            $file_folder = FileController::getFileFolder($field_row->is_public_file); //ToDo: te nedarbojas, ja norādīta vērtība 1 (publiska datne)
            
            $list_row = DB::table('dx_lists')->where('id','=',$this->list_id)->first();
            
            if (strlen($list_row->template_guid) == 0)
            {
                throw new Exceptions\DXCustomException("Reģistram nav norādīta Word sagatave!");
            }
            
            // izgūst ieraksta lauku vērtības kā masīvu
            $data_row = $this->getViewDataArray();
            
            $template_path = FileController::getFileFolder(0) . $list_row->template_guid;

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_path);

            $fld_arr = $templateProcessor->getVariables();

            foreach($fld_arr as $fld)
            {
                $templateProcessor->setValue($fld, $this->getFieldValue($fld, $data_row[0]));
            }
            
            $this->file_name = $list_row->template_name;
            $this->file_guid = Uuid::generate(4) . "." . File::extension($this->file_name);
            
            $templateProcessor->saveAs($file_folder . $this->file_guid);
            
            try
            {
                $this->updateItem($this->file_guid, $this->file_name, $list_row, $field_row);
            }
            catch (\Exception $e)
            {
                File::delete($file_folder . $this->file_guid); // kļūdas gadījumā dzēšam noģenerēto datni lai nepiesārņotu disku
                throw $e;
            }
        }
        
        /**
        * Funkcija uzstāda ieraksta lauku vērtības kas atbilst datnes tipam (datnes nosaukumu un guid)
        * Esošais risinājums nodrošina 1 Word lauka iekļaušanu formās. Ģenerēts tiks tikai pirmais šāds lauks.
        * 
        * @param string $file_guid Datnes unikālais nosaukums
        * @param string $file_name Datnes normālais nosaukums (izlasāmais)
        * $param Object $list_row Reģistra rinda
        * @param Object $field_row Reģistra lauka objekts, kurā saglabāt datni
        * @return void
        */
        private function updateItem($file_guid, $file_name, $list_row, $field_row)
        {            
            $obj_row = DB::table('dx_objects')->where('id','=', $list_row->object_id)->first();
            
            $file_guid_name = str_replace("_name", "_guid", $field_row->db_name);
            
            $this->file_field = $field_row;
            
            $arr_update = [$field_row->db_name => $file_name, $file_guid_name => $file_guid];
            
            if ($obj_row->is_history_logic == 1)
            {
                $arr_update["modified_user_id"] = Auth::user()->id;
                $arr_update["modified_time"] = date('Y-n-d H:i:s');
            }
            
            DB::table($obj_row->db_name)
                    ->where('id','=',$this->item_id)
                    ->update($arr_update);
        }
    }
}