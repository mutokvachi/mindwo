<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_file extends FormatAbstract
    {
        /**
        * Formatē lauka vērtību kā datni - formatē vērtību kā HTML saiti ar JavaScript funkciju datnes lejupielādei
        * 
        * @param Array $model_row   Datu modeļa masīva rinda - te pieejami lauka parametri
        * @param Array $data_row    Datu masīva rinda - te lauka vērtība
        * @return void
        */ 
        public function __construct($model_row, $data_row)
        {
            if (strlen($data_row[$model_row["name"]]) > 0)
            {
                $this->value =  view('grid.cell_file', [
                                     'item_id' => $data_row["id"],
                                     'list_id' => $model_row["list_id"],
                                     'field_id' => $model_row["field_id"],
                                     'cell_value' => $data_row[$model_row["name"]]
                                ])->render();            
            }
            else
            {
                $this->value = "";
            }
        }
    }
}