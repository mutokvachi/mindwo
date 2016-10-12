<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UniqEmployeeCodeUniqPersons extends Migration
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
        if (! $doctrineTable->hasIndex('dx_users_person_code_unique'))
        {
            Schema::table('dx_users', function (Blueprint $table) {
                $table->unique('person_code');
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
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropUnique('dx_users_person_code_unique');
        });        
    }
}
