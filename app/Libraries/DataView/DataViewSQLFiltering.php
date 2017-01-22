<?php
namespace App\Libraries\DataView {
    
    use App\Exceptions;    
    use DB;
    use Config;
    
    class DataViewSQLFiltering
    {        
        /**
        *
        * Grida filtrēšanas parametru apstrādes klase
        *
        *
        * Objekts nodrošina grida filtrēšanas parametru apstrādi - filtrēšanas parametri tiek padoti JSON formātā ar JavaScript/AJAX
        *
        */
        
        public $sql = "";
        public $arr_filt = array();
        public $arr_val = array();
        
        /**
        * Inicializē filtrēšanas parametru apstrādes klases objektu.
        * 
        * @param string $filter_json    Filtrēšanas parametri JSON formātā (tos sagatavo JavaScript dx_grids_core.js funkcija get_filter_data
        * @param int    $list_id        Reģistra identifikators
        * @return void
        */  
        public function __construct($filter_json, $list_id)
        {            
            if (strlen($filter_json) == 0) {
                return;
            }
            
            $arr = array();
            
            $arr = json_decode($filter_json);
            
            for ($i = 0; $i < count($arr); $i++)
            {
                if ($arr[$i][0] == "id") {
                    $this->sql .= " AND " . $arr[$i][0] . " = :" . $arr[$i][0];
                    $this->arr_filt[":" . $arr[$i][0]] = $arr[$i][1];
                }
                else {
                    $field_row = $this->getFieldRow($arr[$i][2], $list_id);
                    $val = $arr[$i][1];

                    if ($field_row->type_id == 2 || $field_row->type_id == 9)
                    {
                        // DateTime vai Date tipa lauks
                        $date_format = Config::get('dx.date_format');
                        $date = check_date($arr[$i][1], $date_format);
                        if (strlen($date) == 0)
                        {
                            throw new Exceptions\DXCustomException(sprintf(trans('errors.wrong_date_format'), $date_format));
                        }
                        $val = $date;
                    }
                    
                    if ($field_row->type_id == 7) {
                        // yes/no
                        if ($val == trans('fields.yes')) {
                            $val = 1;
                        }
                        
                        if ($val == trans('fields.no')) {
                            $val = 0;
                        }
                    }

                    // It is expected that SQL contains WHERE 1=1 as first criteria
                    $this->sql .= " AND " . $arr[$i][0] . " LIKE :" . $arr[$i][0];
                    $this->arr_filt[":" . $arr[$i][0]] = "%" . $val . "%";
                }
                $this->arr_val[":" . $arr[$i][0]] = "%" . $arr[$i][1] . "%"; // Saglabājam atsevišķā masīvā oriģinālo neformatēto filtra kritērija vērtību
            }
        }
        
        /**
        * Izgūst lauka rindas masīvu - ierakstu no tabulas dx_lists_fields
        * 
        * @param int    $field_id       Lauka identifikators
        * @param int    $list_id        Reģistra identifikators
        * @return Array Masīvs ar lauka atribūtiem
        */ 
        private function getFieldRow($field_id, $list_id)
        {
            $field_row = DB::table('dx_lists_fields')->where('id','=',$field_id)->first();
            
            if (!$field_row)
            {
                throw new Exceptions\DXCustomException("Reģistra (ID=" . $list_id . ") filtrēšanas kritērijā norādīta neeksistejošs lauka ID (" . $field_id . ")!");
            }
            
            return $field_row;
        }
    }
}
