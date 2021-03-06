<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Exceptions;
use App\Libraries\Rights;
use Auth;

/**
 * Reģistru kontrolieris
 * Nodrošina dokumentu reģistru saistīto funkcionalitāti
 */
class RegisterController extends Controller
{

    /**
     * Reģistra ID
     * 
     * @var integer 
     */
    private $list_id = 0;

    /**
     * Ieraksta ID
     * 
     * @var integer
     */
    private $item_id = 0;

    /**
     * Reģistrēšanas numura lauka ID
     * 
     * @var integer 
     */
    private $regn_nr_field_id = 0;

    /**
     * Reģistrācijas numurs
     * 
     * @var string
     */
    private $reg_nr = "";

    /**
     * Dokumentu reģistrēšana
     * Nodrošina manuālo dokumentu reģistrēšanas iespēju
     * 
     * @param       Request $request        GET/POST pieprasījuma objekts
     * @return      Response                JSON rezultāts
     */
    public function registerDocument(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer',
            'regn_nr_field_id' => 'required|integer|exists:dx_lists_fields,id'
        ]);

        $this->setParams($request);

        Rights::checkListItemEditRights($this->list_id, $this->item_id);

        return $this->doRegistaration();
    }

    /**
     * Get state for reg. nr. field - can it be edited and weather is reg. button visible
     * 
     * @param object $fld_attr Reg. nr. field object (row from table dx_lists_fields - but is added linked tables fields as well)
     * @param string $item_value Current value for reg. nr.
     * @return array Array with state info: reg_btn_shown and reg_fld_editable
     */
    public static function getRegNrState($fld_attr, $item_value)
    {

        //By default we hide reg. button and allow to edit text field
        $state_arr = ['reg_btn_shown' => 0, 'reg_fld_editable' => 1];

        if (!$fld_attr->is_manual_reg_nr) {
            // There is not set up functionality for manual reg nr assigning - so we dont show reg. button
            return $state_arr;
        }

        if (strlen($item_value) > 0) {
            // There is set reg. nr. manual assinging but number is allready assigned - so we dont show reg. button and dissable editing
            $state_arr['reg_fld_editable'] = 0;
            return $state_arr;
        }

        if (!$fld_attr->reg_role_id) {
            // It is set manual reg functionality but not set role - so everyone can register manually
            $state_arr['reg_btn_shown'] = 1;
            return $state_arr;
        }

        $role = DB::table('dx_users_roles')
                ->where('role_id', '=', $fld_attr->reg_role_id)
                ->where('user_id', '=', Auth::user()->id)
                ->first();

        if ($role) {
            $state_arr['reg_btn_shown'] = 1; // have rights to register
        }
        else {
            $state_arr['reg_fld_editable'] = 0; // don't have rights to register
        }

        return $state_arr;
    }

    /**
     * Veic dokumenta reģistrēšanu
     * @throws Exceptions\DXCustomException
     */
    private function doRegistaration()
    {

        $reg_fld = DB::table('dx_lists_fields')
                ->where('list_id', '=', $this->list_id)
                ->where('id', '=', $this->regn_nr_field_id)
                ->first();

        if (!$reg_fld) {
            throw new Exceptions\DXCustomException("Nav atrasta informācija par reģistrēšanas numura lauku!");
        }

        $table = DB::table('dx_lists as l')
                ->select('o.db_name')
                ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                ->where('l.id', '=', $this->list_id)
                ->first();

        if (!$table) {
            throw new Exceptions\DXCustomException("Nekorekta sistēmas konfigurācija! Nav atrodama reģistrēšanas lauka tabula.");
        }

        // Uzstādam arī pirmo datuma lauku, kuram norādīts parametrs, ka tiek izmantots manuālajā dokumentu reģistrēšanā
        $dat_fld = DB::table('dx_lists_fields')
                ->where('list_id', '=', $this->list_id)
                ->where('is_manual_reg_nr', '=', 1)
                ->where('type_id', '=', 9)
                ->first();

        DB::transaction(function () use ($reg_fld, $table, $dat_fld)
        {
            $this->reg_nr = RegisterController::generateRegNr($reg_fld->numerator_id, $table->db_name, $reg_fld->db_name, $this->item_id);

            $arr_data = array($reg_fld->db_name => $this->reg_nr);

            if ($dat_fld) {
                // ģenerējam šodienas datumu
                $arr_data[$dat_fld->db_name] = date('Y-n-d');
            }

            DB::table($table->db_name)
                    ->where('id', '=', $this->item_id)
                    ->update($arr_data);
        });

        $arr_response = array('success' => 1, 'reg_nr' => $this->reg_nr);

        if ($dat_fld) {
            $arr_response['reg_date_fld'] = $dat_fld->db_name;
            $arr_response['reg_date_htm'] = format_event_time(date('Y-n-d'));
        }

        return response()->json($arr_response);
    }

    /**
     * Uzstāda meklēšanas kritērijus
     * 
     * @param $request POST pieprasījuma objekts
     */
    private function setParams(Request $request)
    {
        $this->list_id = trim($request->input('list_id', 0));
        $this->item_id = $request->input('item_id', 0);
        $this->regn_nr_field_id = $request->input('regn_nr_field_id', 0);
    }

    /**
     * Ģenerē reģistrācijas numuru izmantotjot reģistram piesaistīto numeratoru
     * 
     * @return string Noģenerētais reģistrācijas numurs
     * @throws Exceptions\DXCustomException
     */
    public static function generateRegNr($numerator_id, $table_name, $field_name, $item_id)
    {
        $numerator = DB::table('dx_numerators')->where('id', '=', $numerator_id)->first();

        if (!$numerator) {
            throw new Exceptions\DXCustomException("Nevar saglabāt datus! Reģistrācijas numura laukam nav atrodams numerators!");
        }

        $y = date('Y');
        $nr = $numerator->next_counter;

        $nr = sprintf('%0' . $numerator->counter_lenght . 'd', $nr);

        $reg_nr = str_replace('{GGGG}', $y, $numerator->mask);
        $reg_nr = str_replace('{N}', $nr, $reg_nr);

        RegisterController::checkRegNrUnique($table_name, $field_name, $item_id, $reg_nr);

        DB::table('dx_numerators')
                ->where('id', $numerator_id)
                ->update(['next_counter' => ($numerator->next_counter + 1)]);

        return $reg_nr;
    }

    /**
     * Pārbauda reģistrācijas numura unikalitāti
     * 
     * @param string $reg_nr Reģistrācijas numurs
     * @throws Exceptions\DXCustomException
     */
    public static function checkRegNrUnique($table_name, $field_name, $item_id, $reg_nr)
    {
        $row = DB::table($table_name)->where($field_name, '=', $reg_nr)->where('id', '!=', $item_id)->first();

        if ($row) {
            throw new Exceptions\DXCustomException("Nevar saglabāt datus! Reģistrā jau ir dokuments ar numuru '" . $reg_nr . "'!");
        }
    }

}
