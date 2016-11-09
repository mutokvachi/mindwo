<?php

use Illuminate\Database\Seeder;
use App\Libraries\Structure;

class UserDocumentSeeder extends Seeder
{
    private $arr_countr = [
        ['name' => 'Georgia', 'code' => 'GEO'],
        ['name' => 'Iceland', 'code' => 'ISL'],
        ['name' => 'Netherlands', 'code' => 'NLD'],
        ['name' => 'USA', 'code' => 'US'],
        ['name' => 'Ukraine', 'code' => 'UKR'],
        ['name' => 'Latvia', 'code' => 'LVA'],
        ['name' => 'Hong Kong', 'code' => 'HKG']
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setCountries();
        
        DB::table('in_personal_docs')->delete();
        
        $doc1 = new App\Models\Employee\PersonalDocument();
        $doc1->name = 'Passport or ID card';
        $doc1->description = 'Valid identification (passport or identity card)';
        $doc1->save();

        $doc2 = new App\Models\Employee\PersonalDocument();
        $doc2->name = 'Employment history';
        $doc2->description = '';
        $doc2->save();

        $doc3 = new App\Models\Employee\PersonalDocument();
        $doc3->name = 'Education certificate';
        $doc3->description = 'Document certifying education';
        $doc3->save();
        
        $doc4 = new App\Models\Employee\PersonalDocument();
        $doc4->name = 'Military ID';
        $doc4->description = '';
        $doc4->save();
        
        $doc5 = new App\Models\Employee\PersonalDocument();
        $doc5->name = 'Insurance certificate';
        $doc5->description = 'Medical insurance certificate';
        $doc5->save();
        
        $doc6 = new App\Models\Employee\PersonalDocument();
        $doc6->name = 'Social Security Number (SSN)';
        $doc6->description = '';
        $doc6->save();
        
        $doc7 = new App\Models\Employee\PersonalDocument();
        $doc7->name = 'Form I-9';
        $doc7->description = '';
        $doc7->save();
        
        $doc8 = new App\Models\Employee\PersonalDocument();
        $doc8->name = 'Tax payment certificate';
        $doc8->description = 'Copy of the single tax payment certificate or VAT payer registration certificate. If the private entrepreneur is not registered as VAT or single tax payer, he must submit only the document evidencing registration of a taxpayer.';
        $doc8->save();
        
        $doc9 = new App\Models\Employee\PersonalDocument();
        $doc9->name = 'Certificate of State Registration';
        $doc9->description = '';
        $doc9->save();
        
        $doc10 = new App\Models\Employee\PersonalDocument();
        $doc10->name = 'Bank details';
        $doc10->description = '';
        $doc10->save();
        
        $doc11 = new App\Models\Employee\PersonalDocument();
        $doc11->name = 'Tax identification code';
        $doc11->description = 'Copy of the tax identification code';
        $doc11->save();
        
        $doc12 = new App\Models\Employee\PersonalDocument();
        $doc12->name = 'Worker ID';
        $doc12->description = '';
        $doc12->save();
        
        $doc13 = new App\Models\Employee\PersonalDocument();
        $doc13->name = 'Medical Report';
        $doc13->description = '';
        $doc13->save();

        foreach($this->arr_countr as $country) {
            
            $arr_doc = [];
            if ($country['name'] == 'Georgia') {
                array_push($arr_doc, $doc1->id);
                array_push($arr_doc, $doc2->id);
                array_push($arr_doc, $doc3->id);
                array_push($arr_doc, $doc4->id);
                array_push($arr_doc, $doc5->id);
            }
            
            if ($country['name'] == 'Iceland') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc5->id);
            }
            
            if ($country['name'] == 'Netherlands') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc3->id);
            }
            
            if ($country['name'] == 'USA') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc6->id);
                array_push($arr_doc, $doc7->id);
            }
            
            if ($country['name'] == 'Ukraine') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc8->id); 
                array_push($arr_doc, $doc9->id); 
                array_push($arr_doc, $doc10->id); 
                array_push($arr_doc, $doc11->id); 
            }
            
            if ($country['name'] == 'Latvia') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc5->id);
            }
            
            if ($country['name'] == 'Hong Kong') {
                array_push($arr_doc, $doc1->id);                
                array_push($arr_doc, $doc3->id);
                array_push($arr_doc, $doc12->id);
                array_push($arr_doc, $doc13->id);
            }
            
            $country['obj']->personalDocs()->attach($arr_doc);
        }
        
        $this->setRightsMenu_in_employees_personal_docs();
        
        $this->setRightsMenu_in_personal_docs();
         
        $this->setRightsMenu_in_personal_docs_countries();
        
        // force cache for menu to be updated
        DB::table('in_last_changes')->where('code','=','MENU')->update(['change_time'=>date('Y-n-d H:i:s')]);
    }
    
    private function setCountries() {
        for($i=0; $i<count($this->arr_countr); $i++) {
            $country = $this->arr_countr[$i];
            $country_row = App\Models\Country::where('title', '=', $country['name'])->first();
            if (!$country_row) {
                $country_row = new App\Models\Country();
                $country_row->title = $country['name'];
                $country_row->code = $country['code'];
                $country_row->save();
            }
            $this->arr_countr[$i]['obj'] = $country_row;
        }
    }
    
    private function setRightsMenu_in_employees_personal_docs() {
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs')->id;       
        
        // rights
        DB::table('dx_roles_lists')->where('list_id','=',$list_id)->delete();
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
    }
    
    private function setRightsMenu_in_personal_docs() {
        
        // get list
        $pdoc_list_id = App\Libraries\DBHelper::getListByTable('in_personal_docs')->id;
        
        // rights
        DB::table('dx_roles_lists')->where('list_id','=',$pdoc_list_id)->delete();        
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $pdoc_list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $pdoc_list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        DB::table('dx_menu')->where('list_id','=',$pdoc_list_id)->delete();
        DB::table('dx_menu')->insert(['parent_id' => 252, 'title'=>'Employee document types', 'list_id'=>$pdoc_list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
    }
    
    private function setRightsMenu_in_personal_docs_countries() {
        
        // get list or create if not exists
        try {
            $pdcountr_list_id = App\Libraries\DBHelper::getListByTable('in_personal_docs_countries')->id;        
        }
        catch(\Exception $e) {
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'in_personal_docs_countries', 'title' => 'Employee countries documents' , 'is_history_logic' => 1]);

            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = "Employee countries documents";
            $list_gen->form_title = "Country document";
            $list_gen->doMethod();
            
            $pdcountr_list_id = App\Libraries\DBHelper::getListByTable('in_personal_docs_countries')->id;
        }
        
        // rights
        DB::table('dx_roles_lists')->where('list_id','=',$pdcountr_list_id)->delete();        
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $pdcountr_list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $pdcountr_list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        DB::table('dx_menu')->where('list_id','=',$pdcountr_list_id)->delete(); 
        DB::table('dx_menu')->insert(['parent_id' => 252, 'title'=>'Employee countries documents', 'list_id'=>$pdcountr_list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
}
