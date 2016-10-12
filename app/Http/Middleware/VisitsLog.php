<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use DB;
use Webpatser\Uuid\Uuid;

class VisitsLog
{
    /**
      *
      * Portāla apmeklējumu statistikas klase
      *
      *
      * Klase tiek izsaukta no viduslīmeņa katram pieprasījumam
      *
     */

    /**
     * Apstrādā apmeklējumu - ja šodien vēl nav reģistrēts, tad reģistrē apmeklējumu tabulā
     * Uzstāda cookie šodienas datumu un katram lietotājam piešķirto unikālo GUID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $last_time = $request->cookie('last_visit_time');
        $user_guid = $request->cookie('user_guid');
        
        $time_now = date_create(date('Y-n-d'));

        if (!$last_time)
        {
            // cookie nav uzstdīts, uzstādam uz vēsturisku datumu
            $last_time = date_create('2015-01-01');
        }
        else
        {
            try
            {
                $last_time = date_create($last_time);
            }
            catch (\Exception $e)
            {
                // drošības pēc - ja nu kāds no consoles pamaina cookie uz nekorektu datumu
                $last_time = date_create(date('Y-n-d'));
            }
        }                

        $diff = date_diff($last_time, $time_now)->days;

        if ($diff > 0)
        {
            if (!$user_guid)
            {
                $user_guid = Uuid::generate(4);
            }
        
            DB::table('in_visit_log')->insert([[
                'user_guid' => $user_guid,
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->getClientIp(),
                'visit_time' => date('Y-n-d H:i:s')
            ]]);
        }

        Cookie::queue('last_visit_time', date('Y-n-d'));
        Cookie::queue('user_guid', $user_guid);

        return $next($request);
    }

}
