<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertTestDataDocTemplates extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $doc1 = new App\Models\Employee\PersonalDocument();
        $doc1->name = 'Passport';
        $doc1->description = 'Valid passport';
        $doc1->save();

        $doc2 = new App\Models\Employee\PersonalDocument();
        $doc2->name = 'Birth document';
        $doc2->description = '';
        $doc2->save();

        $doc3 = new App\Models\Employee\PersonalDocument();
        $doc3->name = 'Bank reference';
        $doc3->description = 'Reference from bank';
        $doc3->save();


        $country1 = App\Models\Country::find(1);
        if ($country1) {
            DB::table('in_personal_docs_countries')->insert([
                ['country_id' => $country1->id, 'doc_id' => $doc1->id],
                ['country_id' => $country1->id, 'doc_id' => $doc2->id],
                ['country_id' => $country1->id, 'doc_id' => $doc3->id]
            ]);
        }

        $country2 = App\Models\Country::find(2);
        if ($country2) {
            DB::table('in_personal_docs_countries')->insert([
                ['country_id' => $country2->id, 'doc_id' => $doc2->id]
            ]);
        }

        $country3 = App\Models\Country::find(3);
        if ($country3) {
            DB::table('in_personal_docs_countries')->insert([
                ['country_id' => $country3->id, 'doc_id' => $doc1->id],
                ['country_id' => $country3->id, 'doc_id' => $doc3->id]
            ]);
        }
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
