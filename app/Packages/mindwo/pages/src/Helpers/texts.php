<?php

/**
 * Globālās palīgfunkcijas - tekstu apstrādei
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 */

/**
 * UTF-8 tekstu aizvietošanas funkcija
 * 
 * @param string $str Oriģinālais teksts
 * @param string $repl Ievietojamā frāze
 * @param integer $start Ievietošanas sākuma pozīcija
 * @param integer $length Ievietošanas beigu pozīcija
 * @return string Teksts ar ievietoto frāzi atbilstošajā pozīcijā
 */
function utf8_substr_replace($str, $repl, $start, $length = null)
{
    preg_match_all('/./us', $str, $ar);
    preg_match_all('/./us', $repl, $rar);
    $length = is_int($length) ? $length : utf8_strlen($str);
    array_splice($ar[0], $start, $length, $rar[0]);
    return implode($ar[0]);
}

/**
 * Iekrāso un ieliek treknrakstā tekstā frāzei atbilstošos fragmentus
 * 
 * @param string $orig_text Oriģinālais teksts
 * @param string $criteria Meklēšanas frāze
 * @return string Formatēts teksts ar iekrāsotu frāzi
 */
function mark_phrase($orig_text, $criteria) {
    $crit_count = mb_strlen($criteria, 'UTF-8');
    $crit_pos_part = mb_strpos(mb_strtoupper($orig_text, 'UTF-8'), mb_strtoupper($criteria, 'UTF-8'), 0, 'UTF-8');
    
    $bold = '<b style="background-color: yellow;">';
    
    while(!($crit_pos_part === false)) {
        $orig_text = utf8_substr_replace($orig_text, $bold, $crit_pos_part, 0);
        $orig_text = utf8_substr_replace($orig_text, '</b>', ($crit_pos_part + $crit_count + strlen($bold)), 0);
        $crit_pos_part = mb_strpos(mb_strtoupper($orig_text, 'UTF-8'), mb_strtoupper($criteria, 'UTF-8'), $crit_pos_part + strlen($bold) + $crit_count + 4, 'UTF-8');
    }
    
    return $orig_text;
}

/**
 * Nogriež teksta frāzei sākumu un beigas kā arī iemarķē frāzei atbilstošo tekstu
 * Ja teksts tiek nogriezts tad pievieno daudzpunktes ... 
 * 
 * @param string $orig_text Oriģinālais teksts
 * @param string $criteria Meklēšanas frāze
 * @param integer $extract_len Simpobu skaits no meklēšanas frāzes, kas tiks atstāti
 * @return string Formatēta teksta frāze - pieliktas daudzpunktes un iemarķēts atbilstošais teksts
 */
function mark_search_criteria($orig_text, $criteria, $extract_len) {

    $text = mb_strtoupper($orig_text, 'UTF-8');
    $crit_upper = mb_strtoupper($criteria, 'UTF-8');

    $crit_count = mb_strlen($crit_upper, 'UTF-8');

    $crit_pos = mb_strpos($text, $crit_upper, 0, 'UTF-8');    

    $demo_start = $crit_pos - $extract_len;
    $demo_end = mb_strlen($crit_upper, 'UTF-8') + 2*$extract_len;

    $text_len = mb_strlen($text, 'UTF-8');

    if ($demo_start < 0) {
        $demo_start = 0;
        $part = '';
    } else {
        $part = '...';
    }

    $part .= mb_substr($orig_text, $demo_start, $demo_end, 'UTF-8');

    if ($text_len > $demo_end) {
        $part .= '...';
    }
    
    return mark_phrase($part, $criteria);
   
}