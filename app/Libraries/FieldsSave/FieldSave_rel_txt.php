<?php

namespace App\Libraries\FieldsSave
{

    /**
     * Save textual dropdown value into database - validation
     */
    class FieldSave_rel_txt extends FieldSave
    {       

        /**
         * Validate field value
         */
        public function prepareVal()
        {
            $val = $this->request->input($this->fld->db_name, '');
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }
        
        /**
         * Checks if provided string value is in allowed items list
         * @param string $val
         */
        private function validateItem($val) {
            $items = explode(";", $this->fld->items);
            foreach($items as $item) {
                if ($val == trim($item)) {
                    return;
                }
            }
            
            throw new Exceptions\DXCustomException("Laukam " . $this->fld->db_name . " nav atrodams sinhronizējamais saistītā reģistra lauks! Sazinieties ar sistēmas uzturētāju.");

        }

    }

}