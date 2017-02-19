<?php

namespace App\Libraries\Workflows\Activities
{    
    /**
     * HelpDesk logic - set programmer as responsible person
     * This object works only on list based on table dx_hd_requests
     */
    class Activity_HD_SET_PROGRAMMER extends Activity_HD_SET_ADMIN
    {
        /**
         * Performs custom activity
         */
        public function performActivity()
        {
            return $this->setResponsible("resp_programmer_id");
        }

    }

}