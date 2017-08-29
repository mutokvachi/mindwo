<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;

class EduAddGroupsValidators extends EduMigration
{
    private $table_name='edu_publish_validators';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        DB::transaction(function () {
            DB::table($this->table_name)->delete();
            DB::table($this->table_name)->insert(trans('db_' . $this->table_name . '.values'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {
        //
    }
}
