<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoClearCryptoData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        DB::transaction(function () {
            $cryptoFields = $this->retrieveCryptedFields();

            foreach ($cryptoFields as $cryptoField) {
                if ($cryptoField->is_file == 1) {
                    \DB::table($cryptoField->table_name)
                    ->update([$cryptoField->column_name => null]);

                    $file_guid_col = $this->getFileGuidColumn($cryptoField);

                    \DB::table($cryptoField->table_name)
                    ->update([$file_guid_col => null]);
                } else {
                    \DB::table($cryptoField->table_name)
                    ->update([$cryptoField->column_name => null]);
                }
            }

            \App\Models\Crypto\Certificate::getQuery()->delete();
            \App\Models\Crypto\Masterkey::getQuery()->delete();
        });
    }

    private function getFileGuidColumn($cryptoField)
    {
        $column_name = $cryptoField->column_name;

        if ($cryptoField->is_file == 1) {
            $column_name = str_replace('_name', '_guid', $cryptoField->column_name);
        }

        return $column_name;
    }

    private function retrieveCryptedFields()
    {
        $cryptoFields = \DB::table('dx_lists AS l')
              ->selectRaw('l.id list_id, o.db_name as table_name, f.db_name as column_name, case when f.type_id = 12 then 1 else 0 end as is_file')
              ->leftJoin('dx_objects AS o', 'o.id', '=', 'l.object_id')
              ->leftJoin('dx_lists_fields AS f', 'f.list_id', '=', 'l.id')
              ->where('f.is_crypted', 1)
              ->get();

        return $cryptoFields;
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
