<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MailAttachment
 *
 * Model representing files attached to emails.
 *
 * @package App\Models
 */
class MailAttachment extends Model
{
	//
	public $timestamps = false;
	protected $table = 'dx_mail_attachments';
	
	public function delete()
	{
		file_exists($file = $this->getFilePath()) && is_writable($file) && unlink($file);
		$this->is_image && file_exists($file = $this->getThumbPath()) && is_writable($file) && unlink($file);
		
		return parent::delete();
	}
	
	public function formatFileSize()
	{
		$bytes = $this->file_size;
		$decimals = 2;
		
		$factor = floor((strlen($bytes) - 1) / 3);
		
		if($factor > 0)
		{
			$sz = 'KMGT';
		}
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
	}
	
	public function getFilePath()
	{
		return base_path(config('assets.private_file_path')) . '/' . $this->file_guid;
	}
	
	public function getThumbPath()
	{
		return public_path(config('dx.email.thumbnail_path')) . '/' . $this->file_guid;
	}
}