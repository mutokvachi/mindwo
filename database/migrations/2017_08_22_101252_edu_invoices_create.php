<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduInvoicesCreate extends Migration
{
    private $table_name = "edu_invoices";
    
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
            
            $table->string('type', 250)->nullable()->comment = 'Personas veids';
            $table->string('name', 250)->nullable()->comment = 'Maksātāja nosaukums';
            $table->string('address', 250)->nullable()->comment = 'Adrese';
            $table->string('regnr', 250)->nullable()->comment = 'Reģistrācijas numurs\Personas kods';
            $table->string('bank', 250)->nullable()->comment = 'Banka';
            $table->string('swift', 250)->nullable()->comment = 'Bankas kods';
            $table->string('account', 250)->nullable()->comment = 'Bankas konta numurs';
            $table->string('email', 250)->nullable()->comment = 'E-pasts';
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });    
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
