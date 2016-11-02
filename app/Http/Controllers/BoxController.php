<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use Config;
use Request;

class BoxController extends Controller
{

    public static function generateSlideMenu()
    {
		$parent_id = null;
		$slide = null;

        $sets = BoxController::fillMenuItems(0, 0);
        foreach ($sets as $k => $set) {
            if ($k == 0) {
                $parent_id = $set['parent_id'];
                $slide = $k;
            }
            else if ($set['parent_id'] < $parent_id) {
                $parent_id = $set['parent_id'];
                $slide = $k;
            }
        }

        $first_slide = array(
            'parent_id' => $parent_id,
            'slide' => $slide
        );


        $view = \Illuminate\Support\Facades\View::make('box.htm', compact('sets', 'first_slide'));
        return $view->render();
    }

    private static function fillMenuItems($parent_id, $level)
    {
        $items = BoxController::getMenuRows($parent_id);

        $arr_items = [];
        $arr_sets = [];

        foreach ($items as $item) {
            $rez = BoxController::fillMenuItems($item->id, $level + 1);

            if (count($rez) > 0) {
                $item->is_register = 0;
                $item->href = '';

                foreach ($rez as $set) {
                    array_push($arr_sets, $set);
                }
                $item->item_count = count($rez);
            }
            else if ($item->list_id > 0) {
                $item->href = Request::root() . '/skats_' . $item->view_url;
                $item->is_register = 1;
                $item->item_count = 0;
            }

            if (isset($item->is_register)) {
                array_push($arr_items, $item);
            }
        }

        if (count($arr_items) > 0) {
            array_push($arr_sets, ['parent_id' => $parent_id, 'items' => $arr_items]);
        }

        //Log::info("Līmenis: " . $level . " vērtības: " . json_encode($arr_sets));

        return $arr_sets;
    }

    private static function getMenuRows($parent_id)
    {
        return DB::table('dx_menu as m')
                        ->leftJoin('dx_views as v', function($join)
                        {
                            $join->on('m.list_id', '=', 'v.list_id')
                            ->where('v.is_default', '=', 1);
                        })
                        ->leftJoin('dx_lists as l', 'm.list_id', '=', 'l.id')
                        ->select('m.id', 'm.title', 'm.list_id', 'm.fa_icon', DB::raw('case when m.list_id is null then v.url else CONCAT("skats_",ifnull(v.url, v.id)) end as view_url'), 'l.list_title')
                        ->where(function($query)
                        {
                            $query->whereNull('m.list_id')
                            ->orWhere(function($query_or)
                            {
                                $query_or->whereIn('m.list_id', function($query_in)
                                {
                                    $query_in->select('rl.list_id')
                                    ->from('dx_users_roles as ur')
                                    ->join('dx_roles_lists as rl', 'ur.role_id', '=', 'rl.role_id')
                                    ->where('ur.user_id', '=', Auth::user()->id)
                                    ->distinct();
                                });
                            });
                        })
                        ->where(function($query) use ($parent_id)
                        {
                            if ($parent_id > 0) {
                                $query->where('m.parent_id', '=', $parent_id);
                            }
                            else {
                                $query->whereNull('m.parent_id');
                            }
                        })
                        ->where(function($query)
                        {
                            $query->whereNull('m.group_id')
                            ->orWhere('m.group_id', '=', Config::get('dx.menu_group_id', 0));
                        })
                        ->orderBy('m.order_index')
                        ->get();
    }

}
