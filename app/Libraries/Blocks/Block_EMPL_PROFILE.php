<?php
namespace App\Libraries\Blocks {
	
	use Request;
	use App\Exceptions;
	use Input;
	use DB;
	use Auth;
	use App\Libraries\Rights;
	use Config;
	use Log;
	
	/**
	 * Darbinieka profila attēlošanas klase
	 */
	class Block_EMPL_PROFILE extends Block
	{
		/**
		 * Darbinieka ID
		 * @var integer
		 */
		private $empl_id = 0;
		/**
		 * Darbinieku reģistra ID
		 * @var type
		 */
		private $empl_list_id = 0;
		/**
		 * Darbinieka datu objekts (no tabulas dx_users)
		 * @var object
		 */
		private $empl_row = null;
		/**
		 * Tiesības uz darbinieku reģistru
		 * @var object
		 */
		private $empl_list_rights = null;
		
		/**
		 * Izgūst bloka HTML
		 *
		 * @return string Bloka HTML
		 */
		public function getHTML()
		{
			$is_my_profile = ($this->empl_row->id == Auth::user()->id);
			$view_name = 'blocks.' . (($is_my_profile || !$this->empl_list_rights->is_edit_rights) ? 'empl_profile' : 'empl_profile_large');
			
			return view($view_name, [
				'empl_list_id' => $this->empl_list_id,
				'empl_row' => $this->empl_row,
				'is_my_profile' => $is_my_profile,
				'is_empl_edit_rights' => $this->empl_list_rights->is_edit_rights,
				'external' => $this->getExternalValues()
			])->render();
		}
		
		/**
		 * Izgūst bloka JavaScript
		 *
		 * @return string Bloka JavaScript loģika
		 */
		public function getJS()
		{
			return "";
		}
		
		/**
		 * Izgūst bloka CSS
		 *
		 * @return string Bloka CSS
		 */
		public function getCSS()
		{
			return ""; //view('blocks.view_css')->render();
		}
		
		/**
		 * Izgūst bloka JSON datus
		 *
		 * @return string Bloka JSON dati
		 */
		public function getJSONData()
		{
			return "";
		}
		
		protected function getExternalValues()
		{
			$result = [];
			
			if($this->empl_row->termination_date)
			{
				$result['activity'] = [
					'button' => 'Left',
					'class' => 'grey',
					'title' => 'Employee has left'
				];
			}
			
			elseif($this->empl_row->join_date && !$this->empl_row->termination_date)
			{
				$result['activity'] = [
					'button' => 'Active',
					'class' => 'green-jungle',
					'title' => 'Employee is at work'
				];
			}
			
			else
			{
				$result['activity'] = [
					'button' => 'Potential',
					'class' => 'yellow-lemon',
					'title' => 'The person is in process of hiring'
				];
			}
			
			$result['manager'] =
				DB::table('dx_users')
					->select('display_name')
					->addSelect('position_title')
					->addSelect(DB::raw('(SELECT title FROM in_departments WHERE in_departments.id = dx_users.department_id) AS department_title'))
					->where('id', '=', $this->empl_row->manager_id)
					->first();
			
			$result['flag'] =
				DB::table('dx_countries')
					->select('flag_file_name')
					->addSelect('flag_file_guid')
					->where('id', '=', $this->empl_row->country_id)
					->first();
			
			return $result;
		}
		
		/**
		 * Izgūst bloka parametra vērtības
		 * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [OBJ=...|VIEW_ID=...]
		 *
		 * @return void
		 */
		protected function parseParams()
		{
			$this->empl_id = Input::get('empl_id', 0);
			
			if($this->empl_id == 0)
			{
				$this->empl_id = Auth::user()->id;
			}
			
			$this->empl_row =
				DB::table('dx_users')
					->select('*')
					->addSelect(DB::raw('(SELECT title FROM in_departments WHERE id=dx_users.department_id) AS department_title'))
					->addSelect(DB::raw('(SELECT title FROM dx_countries WHERE id=dx_users.location_country_id) AS country_title'))
					->where('dx_users.id', '=', $this->empl_id)
					->first();
			
			//var_dump($this->empl_row);
			//exit;
			
			if(!$this->empl_row)
			{
				throw new Exceptions\DXCustomException("Darbinieks pēc norādītā ID (" . $this->empl_id . ") nav atrasts!");
			}
			
			$this->empl_list_id = Config::get('dx.employee_list_id');
			
			$this->empl_list_rights = Rights::getRightsOnList($this->empl_list_id);
			
			$this->fillIncludesArr();
		}
		
		/**
		 * Aizpilda masīvu ar JavaScript iekļāvumiem
		 */
		private function fillIncludesArr()
		{
			
			if(Request::ajax() || !$this->empl_list_rights->is_edit_rights)
			{
				return;
			}
			/*
			//Krāsu izvēlnes komponente
			$this->addJSInclude('metronic/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js');
			
			// Datņu ievilkšanas komponente - n datņu vienlaicīga augšuplāde
			$this->addJSInclude('metronic/global/plugins/dropzone/dropzone.min.js');
			
			$this->addJSInclude('metronic/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js');
						
			// Datnes pievienošanas lauka komponente
			$this->addJSInclude('plugins/jasny-bootstrap/js/jasny-bootstrap.js');
			
			// Sazarota koka attēlošanas komponente
			$this->addJSInclude('plugins/tree/jstree.min.js');
			
			// Datņu lejuplādes caur AJAX komponente
			$this->addJSInclude('js/file_download.js');
			
			// Programmēšanas koda ievades komponente
			$this->addJSInclude('plugins/codemirror/js/codemirror.js');
			$this->addJSInclude('plugins/codemirror/js/mode/javascript/javascript.js');
			
			// Datu ievades lauku validācijas komponente
			$this->addJSInclude('plugins/validator/validator.js');
			
			// Teksta redaktora komponente
			$this->addJSInclude('plugins/tinymce/tinymce.min.js');
			
			// Datuma lauka izkrītošā kalendāra komponente
			$this->addJSInclude('plugins/datetimepicker/jquery.datetimepicker.js');
			
			// SVS tabulāro sarakstu komponenete
			$this->addJSInclude('js/dx_grids_core.js');
			
			// SVS formu komponente
			$this->addJSInclude('js/dx_forms_core.js');
			
			// Skata bloka komponente
			$this->addJSInclude('js/blocks/view.js');
			
			// Formu funkcionalitāte
			$this->addJSInclude('js/pages/form_logic.js');
			
			// Darbinieku profili
			$this->addJSInclude('js/pages/date_range.js');
			$this->addJSInclude('js/blocks/empl_profile.js');
			
			// Lookup izkrītošās izvēlnes komponente
			$this->addJSInclude('plugins/select2/select2.min.js');
			$this->addJSInclude('plugins/select2/select2_locale_lv.js');
			
			 // Daudzlīmeņu lauka komponente
			$this->addJSInclude('js/fields/tree.js');
			*/
		}
	}
}