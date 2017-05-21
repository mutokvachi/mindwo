<?php

namespace App\Libraries\DataView\Formatters
{
    use PHPExcel_Shared_Date;
    use DateTime;
    use Config;
    
    class Format_datetime extends FormatAbstract
    {
        /**
        * Formatē lauka vērtību kā datumu un laiku. Uzstāda, ka vērtība tiek iecentrēta
        * 
        * @param mixed $value Formatējamā vērtība
        * @param boolean $is_formula Vai vērtība jāformatē eksportam uz Excel
        * @return void
        */ 
        public function __construct($value, $is_formula = false)
        {
            if (strlen($value) > 0)
            {
                $phpdate = strtotime( $value );
                
                if ($is_formula) {
                    $dt = new DateTime();
                    $dt->setTimestamp($phpdate);

                    $this->value = PHPExcel_Shared_Date::PHPToExcel($dt); 
                }
                else {
                    $this->value = date(Config::get('dx.txt_datetime_format'), $phpdate );
                }
                
                $this->align = "center";
            }
        }
    }
}