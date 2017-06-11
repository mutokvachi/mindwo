<?php

/**
 * Globālās palīgfunkcijas - blokiem
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 */

use mindwo\pages\Exceptions\PagesException;

/**
 * Izgūst bloka parametra vērtību
 * Parametrus HTML tekstā norāda formātā PARAMETRS=VĒRTĪBA, masīvs veidots ar explode pēc = zīmes
 * 
 * @param  Array $val_arr    Parametra masīvs
 * @return string            Saistītās lapas URL
 */
function getBlockParamVal($val_arr)
{
    try {
        return $val_arr[1];
    }
    catch (\Exception $ex) {
        throw new PagesException("Bloka objekta parametram '" . $val_arr[0] . "' nav norādīta vērtība!");
    }
}

/**
 * Izgūst saistītās lapas URL
 * Parametrus HTML tekstā norāda formātā PARAMETRS=VĒRTĪBA, masīvs veidots ar explode pēc = zīmes
 * 
 * @param  Array $val_arr    Parametra masīvs
 * @return string            Saistītās lapas URL
 */
function getBlockRelPageUrl($val_arr)
{
    $page_id = getBlockParamVal($val_arr);

    try {
        $page_row = getPageRowByID($page_id, $page_id);
        return $page_row->url_title;
    }
    catch (\Exception $ex) {
        throw new PagesException("Bloka parametrā '" . $val_arr[0] . "' norādīts neeksistējošas lapas identifikators (" . $page_id . ")!");
    }
}
