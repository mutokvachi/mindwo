<?php

namespace App\Libraries\FieldsSave
{
    use App\Exceptions;
    
    class FieldSave_datetime extends FieldSave
    {
        /**
         *
         * Formas datuma/laika lauka klase
         * Objekts nodrošina formas datuma/laika lauka vērtību apstrādi
         */
        
        protected $date_format = "dd.mm.yyyy HH:ii";
        
        /**
         * Apstrādā lauka vērtību
         */
        public function prepareVal()
        {
            $val = $this->request->input($this->fld->db_name, '');
            if (strlen($val) == 0)
            {
                $val = null;
            }
            
            if ($val != null)
            {
                $val = $this->getFormatedDate($val);
            }
            else
            {
                $val = $this->generateDate();
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }
        
        /**
         * Formatē datumu saglabāšanai datu bāzē
         * 
         * @param string $val Formatējamais datums
         * @return string Datums formātā, kas derīgs saglabāšanai datu bāzē 
         * @throws Exceptions\DXCustomException
         */
        private function getFormatedDate($val)
        {
            $date = check_date($val, $this->date_format);
            
            if (strlen($date) == 0)
            {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.wrong_date_format'), $this->fld->title_form, $this->date_format));
            }
            
            $this->is_val_set = 1;
            
            return $date;
        }
        
        /**
         * Ģenerē šodienas datumu
         * 
         * @return string Atgriež null, ja nav noklusētā vērtība [NOW] vai arī šodienas datumu
         */
        private function generateDate()
        {
            if ($this->item_id > 0 || strlen($this->fld->default_value) == 0)
            {
                return null;
            }
                
            if ($this->fld->default_value == "[NOW]")
            {
                $gen_format = date('Y-n-d');
                $view_format = date('d.n.Y');
                if (strlen($this->date_format) > 10)
                {
                    $gen_format = date('Y-n-d H:i:s');
                    $view_format = date('d.n.Y H:i');
                }
                
                $this->txt_arr[$this->fld->db_name] = $view_format;
                
                $this->is_val_set = 1;
                
                return $gen_format;
            }
            
            return null;                
        }
    }

}