<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('in_departments');
        
        Schema::create('in_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id')->nullable()->comment = "Datu avots";
            $table->integer('parent_id')->nullable()->unsigned()->comment = "Augstākā struktūrvienība";
            $table->string('title', 1000)->nullable()->comment = "Nosaukums";
            $table->string('code', 100)->nullable()->comment = "HR kods";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('parent_id');
            $table->index('source_id');
            $table->unique('code');
            
            $table->foreign('source_id')->references('id')->on('in_sources');
            $table->foreign('parent_id')->references('id')->on('in_departments');
        });
        
        DB::table('in_departments')->insert([
            ['source_id' => '1', 'title' => 'Finanšu departaments'],
            ['source_id' => '1', 'title' => 'Juridiskais departaments'],
            ['source_id' => '1', 'title' => 'Drošības departaments']
        ]);
        
        DB::table('in_departments')->insert([
            ['source_id' => '1', 'parent_id' => 1, 'title' => 'Finanšu analīzes nodaļa'],
            ['source_id' => '1', 'parent_id' => 1, 'title' => 'Finanšu audita nodaļa'],
            ['source_id' => '1', 'parent_id' => 1, 'title' => 'Risku novēršanas nodaļa'],
            ['source_id' => '1', 'parent_id' => 2, 'title' => 'Juridisko dokumentu nodaļa'],
            ['source_id' => '1', 'parent_id' => 2, 'title' => 'Revīzijas nodaļa'],
            ['source_id' => '1', 'parent_id' => 2, 'title' => 'Sekretariāts'],
            ['source_id' => '1', 'parent_id' => 3, 'title' => 'Drošības inventāra nodaļa'],
            ['source_id' => '1', 'parent_id' => 3, 'title' => 'Apsardzes dienests'],
            ['source_id' => '1', 'parent_id' => 3, 'title' => 'Sistēmu monitoringa nodaļa'],
        ]);
        
        Schema::table('in_employees', function (Blueprint $table) {
            $table->integer('department_id')->nullable()->unsigned()->comment = "Struktūrvienība";

            $table->index('department_id');
            $table->foreign('department_id')->references('id')->on('in_departments');
            $table->dropColumn('department');
        });
        
        DB::table('in_employees')->update(['department_id' => 4]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_departments');
    }
}
