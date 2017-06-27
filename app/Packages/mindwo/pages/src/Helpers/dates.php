<?php

/**
 * Globālās palīgfunkcijas - datumiem
 * Helper funkcijas, kuras tiek izsauktas no PHP klasēm un Blade HTML skatiem
 */

/**
 * Formatē datumu vārdiskā veidā, pieliekot priekšā kalendāra ikonu
 * Formatējuma piemērs: 1 Janvāris, Pirmdiena, 2014, 17:25
 *
 * @param  DateTime  $time   Formatējamais datums MySQL datu bāzes formātā YYYY-MM-DD HH:mm
 * @return string            Datums vāŗdiskā veidā  
 */
function format_event_time($time)
{
    $week_days = trans('mindwo/pages::calendar.days_arr');
    $months = trans('mindwo/pages::calendar.month_arr');

    $tm = strtotime($time);
    
    $m = intval(date('n', $tm));
    $d = date('j', $tm);
    $y = date('Y', $tm);
    $w = intval(date('w', $tm));
    
    $h = date('G', $tm);
    $min = date('i', $tm);

    $tm_time = "";
    if ($h > 0)
    {
        $tm_time = ", " . $h . ":" . $min;
    }
    
    return  view('mindwo/pages::elements.article_date', [
                'day' => $d,
                'month' => $months[$m - 1],
                'week_day' => $week_days[$w],
                'year' => $y,
                'time' => $tm_time
            ])->render();
}

/**
 * Formatē dzimšanas dienas datmu, pieliekot priekšā kalendāra ikonu
 * Formatējuma piemērs: 1 Janvāris, Pirmdiena, 2014, 17:25
 *
 * @param  DateTime  $time   Formatējamais datums MySQL datu bāzes formātā YYYY-MM-DD HH:mm
 * @return string            Datums vārdiskā veidā  
 */
function format_birth_time($time)
{
    $week_days = trans('mindwo/pages::calendar.days_arr');
    $months = trans('mindwo/pages::calendar.month_arr');

    $tm = strtotime($time);

    $m = date('n', $tm);
    $d = date('j', $tm);
    $y = date('Y', $tm);

    $tm_now = strtotime(date('Y') . "-" . $m . "-" . $d);

    $w = date('w', $tm_now);

    $cur_m = date('n', $tm_now);

    return view('mindwo/pages::elements.birth_date', [
                'day' => $d,
                'month' => $months[$m - 1],
                'week_day' => $week_days[$w],
                'is_hide_day' => ($m < $cur_m) ? 1 : 0
            ])->render();
}

/**
 * Formatē datumu īsajā veidā: dd.mm.yyyy
 * Formatējuma piemērs: 2015-11-25 tiks formatēts uz 25.11.2015
 *
 * @param  DateTime  $date   Formatējamais datums MySQL datu bāzes formātā YYYY-MM-DD HH:mm
 * @return string            Datums formātā dd.mm.yyyy 
 */
function short_date($date)
{
    $tm = strtotime($date);
    return date(Config::get('dx.txt_date_format'), $tm);
}

/**
 * Formatē datumu garajā veidā: dd.mm.yyyy HH:mm (bez sekundēm)
 * Formatējuma piemērs: 2015-11-25 15:47:12 tiks formatēts uz 25.11.2015 15:47
 *
 * @param  DateTime  $date   Formatējamais datums MySQL datu bāzes formātā YYYY-MM-DD HH:mm:ss
 * @return string            Datums formātā dd.mm.yyyy HH:mm
 */
function long_date($date)
{
    $tm = strtotime($date);
    return date(Config::get('dx.txt_datetime_format'), $tm);
}

/**
 * Pārbauda, vai norādītais datums atbilst formātam
 * 
 * @param string $my_date  Datums
 * @param string $format   Datuma formāts, piemēram dd.mm.yyyy
 * @return string          Atgriež datumu datu bāzes formātā yyyy-mm-dd H:i, ja datums atbilsts formātam vai arī tukšumu, ja neatbilst
 */
function check_date($my_date, $format)
{
    $d_pos = strpos($format, 'd');
    $m_pos = strpos($format, 'm');
    $y_pos = strpos($format, 'y');

    if ($d_pos === false || $m_pos === false || $y_pos === false)
    {
        return "";
    }

    $day = intval(substr($my_date, $d_pos, 2));
    $month = intval(substr($my_date, $m_pos, 2));
    $year = intval(substr($my_date, $y_pos, 4));

    if (strlen($year) != 4 || $day == 0 || $month == 0 || $year == 0)
    {
        return "";
    }

    if (checkdate($month, $day, $year) == false)
    {
        return "";
    }
    else
    {
        // Check time
        $h_pos = strpos($format, 'H');
        $i_pos = strpos($format, 'i');

        if ($h_pos === false)
        {
            return $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
        }

        // dd.mm.yyyy HH:ii
        $h = substr($my_date, $h_pos, 2);
        $i = substr($my_date, $i_pos, 2);

        if (strlen($h) != 2 || strlen($i) != 2 || intval($h) < 0 || intval($h) > 23 || intval($i) < 0 || intval($i) > 59)
        {
            return "";
        }

        return $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT) . " " . str_pad($h, 2, "0", STR_PAD_LEFT) . ":" . str_pad($i, 2, "0", STR_PAD_LEFT);
    }
}

/**
 * Izparsē datumu un laiku, kas tiek padots POST formai no jQuery datetime picker lauka
 * 
 * @param $dt Datums teksta veidā. Dtuma formāts: dd.mm.yyyy
 * @return \DateTime Datums formatēts kā datuma objekts
 */
function parseDateTime($dt)
{
    if (strlen($dt) > 0)
    {
        return date_create_from_format('d.m.Y', $dt);
    }
    else
    {
        return null;
    }
}

/**
 * Izveido datuma objektu
 * 
 * @param string    $dat_str Datums teksta veidā
 * @param boolean   $is_set_to_1900 Pazīme, vai nepieciešams tukšam datumam uzstādīt 1900. gada 1. janvāri (1 - jā, 0 - nē, uzstādīt šodienas datumu)
 * @return \DateTime Datums izveidots kā datuma objekts
 */
function getDateTimeObj($dat_str, $is_set_to_1900)
{
    $d_obj = new \DateTime();
    if ($dat_str != '')
    {
        $d_obj = parseDateTime($dat_str);
    }
    else
    {
        if ($is_set_to_1900)
        {
            $d_obj->setDate(1900, 1, 1);
        }
    }

    return $d_obj;
}