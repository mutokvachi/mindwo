<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

/**
 * Hides master_key field from dx_crypto_masterkeys form view
 */
class DxCryptoMasterkeysHideMasterKeyUi extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            $list = App\Libraries\DBHelper::getListByTable('dx_crypto_masterkeys');

            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list->id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                            ->where('list_id', '=', $list->id)
                            ->where('db_name', '=', 'master_key')
                            ->first()->id)
                    ->update([
                        'is_hidden' => 1
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            $list = App\Libraries\DBHelper::getListByTable('dx_crypto_masterkeys');

            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list->id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                            ->where('list_id', '=', $list->id)
                            ->where('db_name', '=', 'master_key')
                            ->first()->id)
                    ->update([
                        'is_hidden' => 0
            ]);
        });
    }
}
