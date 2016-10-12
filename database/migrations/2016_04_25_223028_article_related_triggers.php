<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArticleRelatedTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  
        Schema::create('in_last_changes', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('code', 50)->nullable()->comment = "Izmaiņu kods";
         
            $table->datetime('change_time')->nullable();
            
            $table->index('code');
        });
        
        $art_date = DB::table('in_articles')->max('modified_time');
        
        DB::table('in_last_changes')->insert([
            ['code' => 'ARTICLE', 'change_time' => $art_date]
        ]);
        
        $this->makeTrigger('in_articles', 'insert');
        $this->makeTrigger('in_articles', 'update');
        $this->makeTrigger('in_articles', 'delete');
        
        $this->makeTrigger('in_articles_files', 'insert');
        $this->makeTrigger('in_articles_files', 'update');
        $this->makeTrigger('in_articles_files', 'delete');
        
        $this->makeTrigger('in_articles_img', 'insert');
        $this->makeTrigger('in_articles_img', 'update');
        $this->makeTrigger('in_articles_img', 'delete');
        
        $this->makeTrigger('in_articles_vid', 'insert');
        $this->makeTrigger('in_articles_vid', 'update');
        $this->makeTrigger('in_articles_vid', 'delete');
        
        $this->makeTrigger('in_tags_article', 'insert');
        $this->makeTrigger('in_tags_article', 'update');
        $this->makeTrigger('in_tags_article', 'delete');
        
        $this->makeTrigger('in_tags', 'update');       
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deleteTrigger('in_articles', 'insert');
        $this->deleteTrigger('in_articles', 'update');
        $this->deleteTrigger('in_articles', 'delete');
        
        $this->deleteTrigger('in_articles_files', 'insert');
        $this->deleteTrigger('in_articles_files', 'update');
        $this->deleteTrigger('in_articles_files', 'delete');
        
        $this->deleteTrigger('in_articles_img', 'insert');
        $this->deleteTrigger('in_articles_img', 'update');
        $this->deleteTrigger('in_articles_img', 'delete');
        
        $this->deleteTrigger('in_articles_vid', 'insert');
        $this->deleteTrigger('in_articles_vid', 'update');
        $this->deleteTrigger('in_articles_vid', 'delete');
        
        $this->deleteTrigger('in_tags_article', 'insert');
        $this->deleteTrigger('in_tags_article', 'update');
        $this->deleteTrigger('in_tags_article', 'delete');
        
        $this->deleteTrigger('in_tags', 'update');
        
        Schema::dropIfExists('in_last_changes');
    }
    
    private function makeTrigger($tbl_name, $operation) {
        $sql = 
        "CREATE TRIGGER `tr_" . $tbl_name . "_" . $operation . "` BEFORE " . $operation . " ON `" . $tbl_name . "`
            FOR EACH ROW
                UPDATE in_last_changes SET change_time=now() WHERE code='ARTICLE';            
        ";
        
        DB::connection()->getPdo()->exec($sql);
    }
    
    private function deleteTrigger($tbl_name, $operation) {
        DB::connection()->getPdo()->exec('drop trigger if exists tr_' . $tbl_name . '_' . $operation);
    }
}
