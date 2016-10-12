<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDxConfigChartColor extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_config')->insert([
            ['config_name' => 'CHART_SECTIONS_COLORS',
                'config_hint' => 'Diagrammu elementu krāsas. Ja diagramma būs norādīts vairāk krāsu kā šeit, tad pārējās krāsas tiks ģenerētas automātiski. Krāsas pieraksta formātā "\'#ffffff\',\'#111111\',..."',
                'field_type_id' => 1,
                'val_varchar' => "'#4b79bc','#5c7cab','#456086','#385e93','#265ba5','#1859b3','#0b57c0','#889dbb','#739ad0','#5e96e5','#5094f3','#4492ff','#5094f3','#086fff','#2573e2','#3976ce'"]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_config')->where('config_name', 'CHART_SECTIONS_COLORS')->delete();
    }

}
