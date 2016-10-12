<?php

namespace App\Libraries\FieldsSave
{
    use Hash;
    use \App\Exceptions;
    
    class FieldSave_password extends FieldSave
    {
        /**
         *
         * Formas paroles lauka klase
         * Objekts nodrošina formas paroles lauka vērtību apstrādi
         * HTML formā pēc noklusēšanas šo lauku uzstāda uz "BLANK". Tas tiek ņemts vērā pie apstrādes.
         */
        
        /**
         * Minimālais paroles garums (simbolu skaits)
         * @var integer 
         */
        private $min_len = 8;
        
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
            
            if ($val != "BLANK")
            {
                if (strlen($val) < $this->min_len)
                {
                    throw new Exceptions\DXCustomException(sprintf(trans('errors.min_password'), $this->min_len));
                }
            }
            else
            {
                if ($this->item_id == 0)
                {
                    throw new Exceptions\DXCustomException(sprintf(trans('errors.min_password'), $this->min_len));
                }
                else
                {
                    $this->is_val_set = 1; // parole nav mainīta
                    return;
                }
            }
            
            $this->is_val_set = 1;
            $this->val_arr[$this->fld->db_name] = Hash::make($val);
        }

    }

}