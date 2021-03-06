<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduRoomsCreate extends Migration
{
    private $table_name = "edu_rooms";
    
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
            
            $table->string('title', 50)->nullable()->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('org_id')->unsigned()->comment = trans('db_' . $this->table_name.'.org_id');
            $table->string('room_nr', 10)->comment = trans('db_' . $this->table_name.'.room_nr');
            $table->boolean('is_computers')->default(false)->nullable()->comment = trans('db_' . $this->table_name.'.is_computers'); 
            $table->boolean('is_elearn')->default(false)->nullable()->comment = trans('db_' . $this->table_name.'.is_elearn');            
            $table->integer('room_limit')->default(1)->comment = trans('db_' . $this->table_name.'.room_limit');                       
                        
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
        
        $trig_sql = "
            BEGIN
                if new.room_limit > 0 then
                    SET new.title = CONCAT(new.room_nr, '  (" . trans('db_' . $this->table_name.'.lbl_limit') . ": ', new.room_limit, ')');                
                else
                    SET new.title = new.room_nr;
                end if;
            END;
        ";
        
        DB::unprepared("CREATE TRIGGER tr_edu_rooms_insert BEFORE INSERT ON edu_rooms FOR EACH ROW " . $trig_sql);
              
        DB::unprepared("CREATE TRIGGER tr_edu_rooms_update BEFORE UPDATE ON edu_rooms FOR EACH ROW " . $trig_sql);
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
