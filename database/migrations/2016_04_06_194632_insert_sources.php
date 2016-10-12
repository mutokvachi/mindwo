<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertSources extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $source1 = \App\Models\Source::where('title', 'Latvenergo')->first();
        if ($source1) {
            $source1->code = 22;
            $source1->save();
        }

        $source2 = \App\Models\Source::where('title', 'Sadales tīkls')->first();
        if ($source2) {
            $source2->code = 3107;
            $source2->save();
        }

        $source1 = new \App\Models\Source;
        $source1->title = 'SIA Liepājas Enerģija';
        $source1->code = 2340;
        $source1->save();

        $source1 = new \App\Models\Source;
        $source1->title = 'Elektrum Lietuva UAB';
        $source1->code = 3619;
        $source1->save();

        $source1 = new \App\Models\Source;
        $source1->title = 'AS Augstsprieguma tīkls';
        $source1->code = 3839;
        $source1->save();

        $source1 = new \App\Models\Source;
        $source1->title = 'Elektrum Eesti OU';
        $source1->code = 4380;
        $source1->save();

        $source1 = new \App\Models\Source;
        $source1->title = 'AS Latvijas elektriskie tīkli';
        $source1->code = 4440;
        $source1->save();

        $source1 = new \App\Models\Source;
        $source1->title = 'AS Enerģijas publiskais tirgotājs';
        $source1->code = 5503;
        $source1->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $source1 = \App\Models\Source::where('title', 'Latvenergo')->first();
        if ($source1) {
            $source1->code = null;
            $source1->save();
        }

        $source2 = \App\Models\Source::where('title', 'Sadales tīkls')->first();
        if ($source2) {
            $source2->code = null;
            $source2->save();
        }
        
        \App\Models\Source::where('code', 2340)->delete();
        \App\Models\Source::where('code', 3619)->delete();
        \App\Models\Source::where('code', 3839)->delete();
        \App\Models\Source::where('code', 4380)->delete();
        \App\Models\Source::where('code', 4440)->delete();
        \App\Models\Source::where('code', 5503)->delete();
    }
}