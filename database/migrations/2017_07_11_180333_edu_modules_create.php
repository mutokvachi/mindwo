<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduModulesCreate extends Migration
{
    private $table_name = "edu_modules";
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->table_name);
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title_full', 250)->nullable()->comment = trans('db_' . $this->table_name.'.title_full');
            $table->string('title', 240)->comment = trans('db_' . $this->table_name.'.title');            
            $table->integer('programm_id')->unsigned()->comment = trans('db_' . $this->table_name.'.programm_id');
            $table->string('code', 3)->comment = trans('db_' . $this->table_name.'.code');  
            $table->integer('avail_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.avail_id');
            $table->integer('icon_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.icon_id');
            $table->text('description')->nullable()->comment = trans('db_' . $this->table_name.'.description');            
            $table->integer('qualify_test_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.qualify_test_id');
            $table->integer('cert_numerator_id')->nullable()->comment = trans('db_' . $this->table_name.'.cert_numerator_id');
            $table->integer('subj_quota_percent')->unsigned()->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.subj_quota_percent');
            
            $table->integer('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            
            $table->index('icon_id');            
            $table->foreign('icon_id')->references('id')->on('dx_icons_files');
            
            $table->index('avail_id');            
            $table->foreign('avail_id')->references('id')->on('edu_modules_avail');
                        
            $table->index('qualify_test_id');            
            $table->foreign('qualify_test_id')->references('id')->on('in_tests');
            
            $table->index('programm_id');            
            $table->foreign('programm_id')->references('id')->on('edu_programms');
            
            $table->index('cert_numerator_id');            
            $table->foreign('cert_numerator_id')->references('id')->on('dx_numerators');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
        
        $sql_trig = "BEGIN
                        DECLARE proj varchar(50);

                        SET proj = (SELECT code FROM edu_programms WHERE id = new.programm_id);

                        SET new.title_full = CONCAT(proj, '-', new.code, ': ', new.title);                
                    END;";
                
        DB::unprepared("CREATE TRIGGER tr_edu_modules_insert BEFORE INSERT ON edu_modules FOR EACH ROW " . $sql_trig);
        
        DB::unprepared("CREATE TRIGGER tr_edu_modules_update BEFORE UPDATE ON edu_modules FOR EACH ROW " . $sql_trig);
          
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
