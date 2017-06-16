<?php

/**
 * Globālās palīgfunkcijas - lapām
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 */

use mindwo\pages\Exceptions\PagesException;

/**
 * Izgūst sistēmas lapu pēc ID (kā int vai kā string)
 * Lapu URL tiek veidoti formātā /lapa_{id} vai /lapa_{unikāls nosaukums}
 * 
 * @param  mixed   $id          Lapas ID (no dx_pages lauks id) vai unikāls url nosaukums (no dx_pages lauks url_title)
 * @return Object               Objekts ar lapas informāciju
 */
function getPageRowByID($page_url, $id)
{
    if (strlen($id) == 0) {
        throw new PagesException("Norādītais resurss '" . $page_url . "' nav atrodams, jo nav norādīts korekts identifikators!");
    }
    
    $fld_name = "url_title";
    if (is_numeric($id))
    {
        $fld_name = "id";
    }

    $page_row = DB::table('dx_pages')
                ->where('is_active', '=', 1)
                ->where($fld_name, '=', $id)
                ->where(function($query) {
                
                    $query->where('group_id', '=', Config::get('dx.menu_group_id', 0))
                          ->orWhere(function($query2) {
                              $query2->whereNull('group_id');
                          });
                        
                })
                ->first();
    
    if (!$page_row)
    {
        throw new PagesException("Norādītais resurss '" . $page_url . "' nav atrodams!");
    }

    return $page_row;
}

/**
 * Izgūst noklusētās lapas fona attēla datnes nosaukumu
 * 
 * @return string  Fona attēla datnes nosaukums vai arī tukšums, ja noklusētā lapa nav atrasta
 */
function getDefaultPageBackground()
{
    $def_page_row = DB::table('dx_pages')
                    ->where('is_active', '=', '1')
                    ->where('is_default', '=', '1')
                    ->where(function($query) {
                
                        $query->where('group_id', '=', Config::get('dx.menu_group_id', 0))
                              ->orWhere(function($query2) {
                                  $query2->whereNull('group_id');
                              });

                    })
                    ->first();

    if ($def_page_row)
    {
        return ['file' => $def_page_row->file_guid, 'content_bg_color' => $def_page_row->content_bg_color];
    }

    // Ja nav atrodama aktīva noklusētā lapa, tad nav fona attēla un satura daļas fons ir balts
    return ['file' => "", 'content_bg_color' => "rgba(255,255,255,1)"];
}

/**
 * Include datnei pievieno tās versijas skaitli (versija ir pēdējo izmaiņu datums/laiks skaitliskā vērtība)
 * Tas nepieciešams, lai nodrošinātu Include datņu automātisku Refresh, ja mainās versija
 * 
 * @param string $include Include datnes ceļš (relatīvais), nedrīkst sākties ar /
 * @return string Include datnes nosaukums un ?v=#####
 */
function getIncludeVersion($include) {
    return $include . "?v=" . File::lastModified(public_path() . "/" . $include);
}

/**
 * Atgriež portāla domēna nosaukumu formatētu izmantošanai cache failu nosaukumu veidošanai
 * 
 * @return string Formatēts domēna nosaukums www_domain_com veidā
 */
function getRootForCache() {
    $root = url('/');
    $root = str_replace("http://", "", $root);
    $root = str_replace("https://", "", $root);
    $root = str_replace(".", "_", $root);
    
    return $root;
}