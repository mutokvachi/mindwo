<?php

use Illuminate\Database\Seeder;

class EnArticlesSeeder extends Seeder
{  
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        // translate articles types to english
        DB::table('in_article_types')->where('id','=', 1)->update(['name' => 'Articles', 'hover_hint' => 'Articles']);
        DB::table('in_article_types')->where('id','=', 2)->update(['name' => 'HR news', 'hover_hint' => 'HR news']);
        DB::table('in_article_types')->where('id','=', 3)->update(['name' => 'Pictures', 'hover_hint' => 'Pictures galeries']);
        DB::table('in_article_types')->where('id','=', 2)->update(['name' => 'Video', 'hover_hint' => 'Video galeries']);
        
        // reset articles views criteria
        DB::table('dx_views_fields')->where('list_id', '=', 60)->where('field_id', '=', 1171)->update(['criteria'=>"'Articles'"]);
    }   
   
}
