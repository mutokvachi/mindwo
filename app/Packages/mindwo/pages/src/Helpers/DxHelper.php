<?php

use mindwo\pages\ConfigFields;
use mindwo\pages\Exceptions\PagesException;
use mindwo\pages\Rights;

/**
 *
 * Publiskās palīgfunkcijas
 *
 *
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 *
 */

/**
 * Izgūst reģistra datu apskates/ievades formas URL
 * 
 * @param integer $list_id  Reģistra ID
 * @return string           Formas URL
 */
function getListFormURL($list_id)
{
    $first_form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

    if ($first_form)
    {
        if ($first_form->form_type_id == 2)
        {
            return $first_form->custom_url;
        }
        else
        {
            return "form";
        }
    }
    else
    {
        throw new PagesException("Reģistram nav definēta datu ievades forma!");
    }
}

/**
 * Izgūst reģistra skatus attēlošanai izkrītošajā izvēlnē
 * 
 * @param  int  $list_id Reģistra idnetifikators no tabulas dx_lists
 * @return Array Masīvs ar skatu informāciju
 */
function getViewsComboItems($list_id, $tab_id)
{
    $fld_is_hidden = 'is_hidden_from_main_grid';

    if (strlen($tab_id) > 0)
    {
        $fld_is_hidden = 'is_hidden_from_tabs';
    }

    return DB::table('dx_views')->where('list_id', '=', $list_id)->whereIn('view_type_id', [1, 9])->where($fld_is_hidden, '=', 0)->orderBy('title')->get();
}

/**
 * Izgūst reģistra skatu pēc ID (kā int vai kā string)
 * Skatu URL tiek veidoti formātā /skats_{id} vai /skats_{unikāls nosaukums}
 * 
 * @param  string  $page_url    Lapa, no kuras pieprasīts skats
 * @param  mixed   $id          Skata ID (no dx_views lauks id) vai unikāls url nosaukums (no dx_views lauks url)
 * @return Object               Objekts ar skata informāciju
 */
function getViewRowByID($page_url, $id)
{
    $view_row = null;

    if (is_numeric($id))
    {
        $view_row = DB::table('dx_views')
                ->where('id', '=', $id)
                ->first();
    }
    else
    {
        if (strlen($id) > 0)
        {
            $view_row = DB::table('dx_views')
                    ->where('url', '=', $id)
                    ->first();
        }
    }

    if ($view_row == null)
    {
        throw new PagesException(sprintf(trans('mindwo/pages::errors.view_not_found'), $page_url), trans('mindwo/pages::errors.attention'));
    }

    return $view_row;
}

/**
 * Ģenerē HTML kodu datetime pickerim
 * 
 * @param string $uniq_formid  HTML FORM elementa unikālais GUID
 * @param string $item_field   Lauka nosaukums
 * @param string $value        Sākotnējā vērtība
 * @return string HTML datetime pickerim
 */
function get_htm_for_datetime_field($uniq_formid, $item_field, $value)
{
    return view('fields.datetime_html', [
                'frm_uniq_id' => $uniq_formid,
                'item_field' => $item_field,
                'item_value' => $value,
                'is_disabled' => false,
                'fld_width' => '105',
                'tm_format' => 'd.m.Y',
                'is_time' => 'false',
                'is_required' => false
            ])->render();
}

/**
 * Ģenerē JavaScript kodu datetime pickerim
 * 
 * @param string $uniq_formid  HTML FORM elementa unikālais GUID
 * @param string $item_field   Lauka nosaukums
 * @return string   JS
 */
function get_js_for_datetime_field($uniq_formid, $item_field)
{
    return view('fields.datetime_js', [
                'frm_uniq_id' => $uniq_formid,
                'item_field' => $item_field,
                'is_disabled' => false,
                'tm_format' => 'd.m.Y',
                'is_time' => 'false'
            ])->render();
}

/**
 * Formatē HTML tagu IMG atribūtus SRC - pievienot domēna pilno ceļu attēlu avotiem
 * Formatē HTML tagu A atribūtus HREF - pievieno domēna pilno ceļu relatīvajām (portāla iekšējām) saitēm
 * Formatē HTML tagu SOURCE atribūtus SRC - pievieno domēna pilno ceļu video avotiem
 * 
 * @param string $root_url  Domēna URL
 * @param string $html      Formatējamais HTML
 * @return string           Formatēts HTML
 */
function format_html_img($root_url, $html)
{
    $getImgURL = function ($matches) use ($root_url)
    {
        return $matches[1] . $root_url . "/" . $matches['2'];
    };

    $getLinkURL = function ($matches) use ($root_url)
    {
        if (strpos($matches['2'], 'http') !== false || strpos($matches['2'], '#') !== false)
        {
            return $matches[1] . $matches['2'];
        }
        else
        {
            return $matches[1] . $root_url . "/" . $matches['2'];
        }
    };

    $getVideoURL = function ($matches) use ($root_url)
    {
        return $matches[1] . $root_url . "/" . $matches['2'];
    };

    $html = preg_replace_callback("/(<a[^>]*href *= *[\"']?)([^\"']*)/i", $getLinkURL, $html);

    $html = preg_replace_callback("/(<source[^>]*src *= *[\"']?)([^\"']*)/i", $getVideoURL, $html);

    return preg_replace_callback("/(<img[^>]*src *= *[\"']?)([^\"']*)/i", $getImgURL, $html);
}

/**
 * Izgūst konfigurācijas parametra vērtību (no tabulas dx_config)
 * 
 * @param string $config_name Konfigurācijas parametra nosaukums
 * @return string Konfigurācijas parametra vērtība
 */
function get_portal_config($config_name)
{
    // Mēģinam izgūt parametra vērtību no cache datnes
    $val = ConfigFields\ConfigFieldFactory::getConfigFromFile($config_name);
    
    if ($val == "[[NOT SET]]") {
        // Izgūstam parametra vērtību no db (un arī saglabā cache nākamajai reizei)
        $val = ConfigFields\ConfigFieldFactory::build_config($config_name)->getConfigValue();
    }
    
    return $val;
}

/**
 * Atgriež rakstu izgūšanas objekta sākuma daļu - kopgi izmantojama ziņu plūsmā, ziņu meklēšanā, iezīmju rakstos
 * 
 * @return Object Rakstu izgūšanas objekta sākuma daļa
 */
function get_article_query()
{
    $root = Request::root();
    
    return DB::table('in_articles')
                    ->leftJoin('in_sources', 'in_sources.id', '=', 'in_articles.source_id')
                    ->leftJoin('in_article_types', 'in_articles.type_id', '=', 'in_article_types.id')
                    ->leftJoin('in_tags', 'in_sources.tag_id', '=', 'in_tags.id')
                    ->select(DB::raw("      
                                in_articles.*, 
                                ifnull(in_sources.feed_color,'#f1f4f6') as feed_color,
                                in_sources.title as source_title,
                                in_article_types.name as type_name,
                                in_article_types.code as type_code,
                                in_article_types.is_for_galeries,
                                in_article_types.picture_name as type_picture, 
                                in_article_types.file_guid as placeholder_pic, 
                                in_article_types.hover_hint,
                                (SELECT group_concat(tag_id SEPARATOR ';') FROM in_tags_article WHERE article_id=in_articles.id  GROUP BY 'all') as tag_ids,
                                in_tags.name as source_tag_title,
                                in_sources.tag_id as source_tag_id,
                                in_sources.icon_class as source_tag_icon,
                                (SELECT id FROM in_articles_files WHERE article_id = in_articles.id LIMIT 0, 1) as file_added_id,
                                CASE in_articles.content_id
                                    WHEN 2 THEN in_articles.outer_url
                                    WHEN 3 THEN CONCAT('" . $root . "/img/', in_articles.dwon_file_guid)
                                    ELSE CONCAT('" . $root . "/ieraksts/', CASE WHEN in_articles.alternate_url IS NULL THEN in_articles.id ELSE in_articles.alternate_url END)
                                END as open_article_url
                            ")
                    )->whereDate('in_articles.publish_time', '<=', date('Y-n-d H:i:s'));
}

/**
 * Apvieno visus meklēšanas kritērijus vienā teksta rindā, kritērijus atdalot ar komatu.
 * Meklēšanu var veikt pēc 1 vai vairākiem kritērijiem.
 * Apvienotā teksta rinda nepieciešma meklēšanas rezultātu virsrakstam.
 * 
 * @param       string  $criteria_all Jau apvienoto kritēriju teksta rinda
 * @param       string  $criteria_new Jaunais kritērijs
 * @return      string  Apvienotā kritēriju rinda, kurai galā pievienots jaunais kritērijs
 */
function addCriteria($criteria_all, $criteria_new)
{
    if (strlen($criteria_new) == 0)
    {
        return $criteria_all;
    }

    if (strlen($criteria_all) > 0)
    {
        $criteria_all .= ", ";
    }

    return $criteria_all . $criteria_new;
}

/**
 * Pārbauda, vai lietotājam ir ierakstu dzēšanas tiesības norādītajā reģistrā
 * 
 * @param integer $list_id Reģistra ID
 * @throws Exceptions\DXCustomException
 */
function checkDeleteRights($list_id)
{
    $right = Rights::getRightsOnList($list_id);

    if ($right == null)
    {
        throw new PagesException(trans('mindwo/pages::errors.no_rights_on_register'));
    }
    else
    {
        if ($right->is_delete_rights == 0)
        {
            throw new PagesException(trans('mindwo/pages::errors.no_rights_to_delete'));
        }
    }
}

/**
 * Pārbauda, vai ierakstu drīkst dzēst, t.i., vai citos reģistros nav atsauces uz šo ierakstu
 * Ja ierakstu nedrīkst dzēst, tad tiek atgriezta kļūda
 * 
 * @param   integer  $list_id    Dzēšamā ieraksta reģistra ID
 * @param   integer  $item_id    Dzēšamā ieraksta ID
 * @return  void
 */
function validateRelations($list_id, $item_id)
{
    $rels = DB::table('dx_lists_fields as lf')
            ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
            ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
            ->select('lf.list_id', 'lf.db_name as field_name', 'l.object_id', 'o.db_name as table_name', 'l.list_title')
            ->where('lf.rel_list_id', '=', $list_id)
            ->where('l.is_cascade_delete', '=', 0)
            ->get();

    foreach ($rels as $rel)
    {
        $count = DB::table($rel->table_name)->where($rel->field_name, '=', $item_id)->count();

        if ($count > 0)
        {
            throw new PagesException(sprintf(trans('mindwo/pages::errors.cant_delete_used_record'), $rel->list_title, $count));
        }
    }
    
    // for tasks we dont have foreign key constraint in db
    $task = DB::table('dx_tasks')->where('list_id','=',$list_id)->where('item_id','=',$item_id)->count();
    
    if ($task > 0) {
        
        throw new PagesException(sprintf(trans('mindwo/pages::errors.cant_delete_used_record'), trans('mindwo/pages::errors.task_list'), $task));
    }
}

/**
 * Izgūst YouTube video ID no YouTube video saites
 * 
 * @param string $url YouTube video saite, piemēram https://www.youtube.com/watch?v=nPGQ5Le851o
 * @return string YouTube video ID
 */
function getYoutubeID($url)
{
    $array_of_vars = array();
    parse_str( parse_url( $url, PHP_URL_QUERY ), $array_of_vars );
    
    $keys = array_keys($array_of_vars);
    
    if(in_array('v', $keys))
    {
        return $array_of_vars['v']; 
    }
    
    return ""; // nekorekts Youtube URL
}

/**
 * Pievieno tālruņa numuriem click2call funkcionalitāti
 * 
 * @param string $phone Tālruņa numuri, atdalīti ar semikolonu
 * @param string $click2call_url click2call vietnes URL
 * @param string $fixed_phone_part Organizācijas iekšējā tālruņa numura sākuma daļa
 * @return string   HTML ar tālruņa numuriem kā saitēm uz click2call
 */
function phoneClick2Call($phone, $click2call_url, $fixed_phone_part){
    if (strlen($click2call_url) == 0) {
        return $phone;
    }
    
    $phone = str_replace(" ", "", $phone);
    $phone_arr = explode(";", $phone);
    $htm = "";
    
    foreach($phone_arr as $phone_nr){
        if (strlen($phone_nr) == 4)
        {
            $phone_nr = $fixed_phone_part . $phone_nr;
        }
        $url = str_replace("#phone#", $phone_nr, $click2call_url);
        
        if (strlen($htm) > 0)
        {
            $htm .= "; ";
        }
        
        $htm .= view('elements.click2call', [
                'url' => $url,
                'phone_nr' => $phone_nr
                ])->render();
    }
    
    return $htm;
}