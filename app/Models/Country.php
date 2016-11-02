<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $table = 'dx_countries';
	
	public function getFlag()
	{
		if($this->flag_file_guid && is_file(public_path("img/{$this->flag_file_guid}")))
		{
			return url("img/{$this->flag_file_guid}");
		}
		
		return '';
	}
}
