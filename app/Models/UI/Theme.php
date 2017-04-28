<?php

namespace App\Models\UI;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Theme extends Model
{
	const CREATED_AT = 'created_time';
	const UPDATED_AT = 'modified_time';
	protected $table = 'dx_ui_themes';
	
	public function renderLinkTag()
	{
		if(!$this->file_name && !$this->file_guid)
		{
			return '';
		}
		
		if(strpos($this->file_guid, 'elix') !== false)
		{
			return '<link href="' . elixir('css/' . $this->file_guid) . '" rel="stylesheet" type="text/css"/>';
		}
		
		return '<link href="' . asset('themes/' . $this->file_guid) . '" rel="stylesheet" type="text/css"/>';
	}
	
	static public function getLinkStylesheetTag()
	{
		$user = User::find(Auth::user()->id);
		
		if($user->ui_theme_id == null)
		{
			$theme = Theme::findDefault();
		}
		
		else
		{
			$theme = $user->ui_theme;
		}
		
		return $theme->renderLinkTag();
	}
	
	static public function findDefault()
	{
		return Theme::where('is_default', 1)->get()->first();
	}
}
