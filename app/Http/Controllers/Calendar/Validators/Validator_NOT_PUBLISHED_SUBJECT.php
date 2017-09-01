<?php

namespace App\Http\Controllers\Calendar\Validators
{
    use DB;
    
    /**
     * Validates if all groups have published subjects
     */
    class Validator_NOT_PUBLISHED_SUBJECT extends Validator
    {
        public function validateGroup() {
            $subj = DB::table('edu_subjects')
                    ->select('is_published')
                    ->where('id', '=', $this->group->subject_id)
                    ->first();
            
            if (!$subj->is_published) {
                $list = \App\Libraries\DBHelper::getListByTable('edu_subjects');
                $this->setError($list->id, $this->group->subject_id, trans('db_edu_publish_validators.err_action_edit_subject'));
            }
        }
    }

}