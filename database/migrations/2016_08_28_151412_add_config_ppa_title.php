<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigPpaTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_config')
                    ->insert([
                        ['config_name' => 'PPA_DOC_TITLE', 'config_hint' => 'PPA dokumenta virsraksts', 'field_type_id' => 1, 'val_varchar' => 'Valsts kancelejas normatīvo aktu projektu izstrādes rokasgrāmatas digitālais risinājums'],
                        ['config_name' => 'PPA_DOC_AUTHOR', 'config_hint' => 'PPA dokumenta autors', 'field_type_id' => 1, 'val_varchar' => 'SIA Euroscreen'],                     
                    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')->whereIn('config_name', [
            'PPA_DOC_TITLE',
            'PPA_DOC_AUTHOR',
        ])->delete();
    }
}
