<?php

namespace App\Libraries\DataView 
{
    use Request;
    use Webpatser\Uuid\Uuid;
    use App\Exceptions;
    use Auth;
    
    class DataViewObjConstructor extends DataViewObjAbstract
    {        
        public function __construct($list_id, $view_id, $is_hidden_in_model)
        {
            $grid_id = Request::input('grid_id', '');
            
            $this->session_guid = ($grid_id) ? $grid_id : 'grid_' . Uuid::generate(4); // ģenerējam HTML objekta unikālo id, tiks izmantots arī lai SQL saglabātu sesijā
            
            $this->view_obj = new \App\Libraries\View($list_id, $view_id, Auth::user()->id); // izveidojam skata objektu
                    
            // Tiek uzstādītas vērtības gadījumā, ja registrs izsaukts no formas sadaļas (tad tiek norādīti atbilstoši saistītais lauks un tā vērtība
            $this->view_obj->rel_field_id = Request::input('rel_field_id',0);
            $this->view_obj->rel_field_value = Request::input('rel_field_value',0);
            $this->view_obj->is_hidden_in_model = $is_hidden_in_model;
            
            $this->view_sql = $this->view_obj->get_view_sql(); // procesējam skata SQL sagatavošanu

            if (strlen($this->view_obj->err) > 0)
            {
                throw new Exceptions\DXCustomException($this->view_obj->err); // ToDo: jākoriģē skata objekta loģika lai throw kļūdas, tad šo rindiņu varēs izņemt
            }
                    
            // Saglabājam sesijā, lai uzlabotu ātrdarbību            
            Request::session()->put($this->session_guid . "_view", serialize($this->view_obj));
            Request::session()->put($this->session_guid . "_sql", $this->view_sql);
        }
    }
}
