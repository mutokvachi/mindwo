<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqIndexFromDxUsersDepartm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails('dx_users');

        // alter table "users" add constraint users_email_unique unique ("email")
        if ($doctrineTable->hasIndex('dx_users_is_leader_department_id_unique'))
        {
            Schema::table('dx_users', function (Blueprint $table) {
                $table->dropUnique(['is_leader', 'department_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
