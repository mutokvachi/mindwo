<?php

/**
 * Globālās palīgfunkcijas - skatījumu parametriem
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 */

use mindwo\pages\Exceptions\PagesException;
use mindwo\pages\Menu;

/**
 * Uzstāda visiem lapu skatiem kopīgos parametrus
 * 
 * @return void
 */
function set_default_view_params()
{    
    view()->share('menu_htm', (new Menu())->getHTML());
    
    view()->share('portal_name', get_portal_config('PORTAL_NAME'));
    view()->share('portal_logo', get_portal_config('PORTAL_LOGO_FILE'));

    $page_colors = getDefaultPageBackground();
    view()->share('background_file', $page_colors['file']);
    view()->share('content_bg_color', $page_colors['content_bg_color']);
    /*
    $block_spec = Blocks\BlockFactory::build_block("OBJ=SPECDAYS");
    view()->share('special_days', $block_spec->getHtml());

    $block_weather = Blocks\BlockFactory::build_block("OBJ=WEATHER");
    view()->share('weather', $block_weather->getHtml());
    */
}

/**
 * Uzstāda CMS sistēmai visiem lapu skatiem kopīgos parametrus
 */
function set_cms_view_params() {
    view()->share('user_tasks_count', getUserActualTaskCount());
    view()->share('visit_count', getPortalVisitCount());
    
    $block_spec = mindwo\pages\Blocks\BlockFactory::build_block("OBJ=SPECDAYS");
    view()->share('special_days', $block_spec->getHtml());
}

/**
 * Uzstāda sesijas publisko lietotāju no iestatījumiem
 * 
 * @param Request $request  POST/GET pieprasījuma objekts
 * @throws Exceptions\DXCustomException
 */
function set_public_user($request)
{
    if (!Auth::check())
    {
        $public_user_id = Config::get('dx.public_user_id');
        if (!$public_user_id)
        {
            throw new PagesException("Resursam '" . $request->url() . "' nav iespējams piekļūt, jo sistēmas iestatījumos nav norādīts publiskais lietotājs!");
        }

        Auth::loginUsingId($public_user_id);
    }
}

/**
 * Izgūst lietotāja aktuālo (izpildāmo) uzdevumu skaitu
 * 
 * @return integer Uzdevumu skaits
 */
function getUserActualTaskCount()
{
    $cnt = 0;

    if (Auth::check())
    {
        $cnt = DB::table('dx_tasks')
                ->where('task_employee_id', '=', Auth::user()->id)
                ->whereNull('task_closed_time')
                ->count();
    }

    return $cnt;
}

/**
 * Izgūst portāla šodienas unikālo apmeklējumu skaitu
 * 
 * @return integer Portāla šodienas unikālo apmeklējumu skaits
 */
function getPortalVisitCount()
{
    $visit_count = 0;
    $visits = DB::select('select count(*) as cnt from in_visit_log where DATEDIFF(now(), visit_time)=0');

    if (count($visits) > 0)
    {
        $visit_count = $visits[0]->cnt;
    }

    return $visit_count;
}