<?php

namespace App\Libraries;

use Illuminate\Translation\Translator as LaravelTranslator;
use DB;

class BeTranslator extends LaravelTranslator
{
    public function get($key, array $replace = array(), $locale = null) {
        /*
        $row = DB::table('be_webtexts')->where('text_key',$key)->first();
        
        if ($row)
        {
            return $row->text_value;
        }
        */
        return $key; // not found
    }   
}