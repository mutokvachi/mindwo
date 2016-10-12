<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactFieldsToDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('reg_addr_country_id')->nullable()->unsigned()->comment = "Registered address country";            
            $table->index('reg_addr_country_id');
            $table->foreign('reg_addr_country_id')->references('id')->on('dx_countries');
            
            $table->string('reg_addr_city', 100)->nullable()->comment="Registered address city";
            $table->string('reg_addr_street', 200)->nullable()->comment="Registered address street";
            $table->string('reg_addr_zip', 20)->nullable()->comment="Registered address ZIP";
            
            $table->integer('curr_addr_country_id')->nullable()->unsigned()->comment = "Current address country";            
            $table->index('curr_addr_country_id');
            $table->foreign('curr_addr_country_id')->references('id')->on('dx_countries');
            
            $table->string('curr_addr_city', 100)->nullable()->comment="Current address city";
            $table->string('curr_addr_street', 200)->nullable()->comment="Current address street";
            $table->string('curr_addr_zip', 20)->nullable()->comment="Current address ZIP";
            
            $table->string('workphone1', 30)->nullable()->comment="Work phone 1";
            $table->string('workphone2', 30)->nullable()->comment="Work phone 2";
            $table->string('other_email', 200)->nullable()->comment="Other email";
            $table->string('skype', 100)->nullable()->comment="Skype";
            $table->string('viber', 100)->nullable()->comment="Viber";
            $table->string('whatsapp', 100)->nullable()->comment="WhatsApp";
            $table->string('telegram', 100)->nullable()->comment="Telegram";
            $table->string('emergency_contacts', 200)->nullable()->comment="Emergency contacts";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['reg_addr_country_id']);
            $table->dropColumn(['reg_addr_country_id']);
            
            $table->dropForeign(['curr_addr_country_id']);
            $table->dropColumn(['curr_addr_country_id']); 
                        
            $table->dropColumn(['reg_addr_city']);
            $table->dropColumn(['reg_addr_street']);
            $table->dropColumn(['reg_addr_zip']);
            $table->dropColumn(['curr_addr_city']);
            $table->dropColumn(['curr_addr_street']);
            $table->dropColumn(['curr_addr_zip']);
                       
            $table->dropColumn(['workphone1']);
            $table->dropColumn(['workphone2']);
            $table->dropColumn(['other_email']);
            $table->dropColumn(['skype']);
            $table->dropColumn(['viber']);
            $table->dropColumn(['whatsapp']);
            $table->dropColumn(['telegram']);
            $table->dropColumn(['emergency_contacts']);
        });
    }
}
