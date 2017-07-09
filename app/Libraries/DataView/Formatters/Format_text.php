<?php

namespace App\Libraries\DataView\Formatters
{    
    /**
     * Formatter for long texts
     */
    class Format_text extends FormatAbstract
    {
        /**
        * Formatter constructor
        * 
        * @param Array $model_row  Array with field properties
        * @param Array $data_row  Array with field value
        * @return void
        */ 
        public function __construct($model_row, $data_row)
        {
            $value_formated = "";
            if (strlen($data_row[$model_row["name"]]) > 0) {
                
                if ($model_row["width"] > 0) {
                    $helper = new \App\Libraries\DataView\Helper();
                    $value_formated = $helper->getLongTextCell([
                                         'cell_value' => $data_row[$model_row["name"]],
                                         'cell_width' => $model_row["width"]
                                    ]);

                    $this->values['is_html'] = true;                
                    $this->values['is_link'] = true;
                }
                else {
                    $value_formated = $data_row[$model_row["name"]];
                }
            }
            
            $this->value = $value_formated;
        }
    }
}