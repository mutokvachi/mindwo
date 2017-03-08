<?php

namespace App\Models;

use App\Libraries\Image\Image;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webpatser\Uuid\Uuid;

/**
 * Class MailAttachment
 *
 * Model representing files attached to emails.
 *
 * @package App\Models
 */
class MailAttachment extends Model
{
	/**
	 * Disable timestamps on the model.
	 *
	 * @var bool
	 */
	public $timestamps = false;
	/**
	 * Database table for mail attachments.
	 *
	 * @var string
	 */
	protected $table = 'dx_mail_attachments';
	
	/**
	 * Override delete() method so that it removes file and thumbnail (for images) from filesystem.
	 *
	 * @return bool|null
	 */
	public function delete()
	{
		file_exists($file = $this->getFilePath()) && is_writable($file) && unlink($file);
		$this->is_image && file_exists($file = $this->getThumbPath()) && is_writable($file) && unlink($file);
		
		return parent::delete();
	}
	
	/**
	 * Get size of the file in human readable format.
	 *
	 * @return string
	 */
	public function formatFileSize()
	{
		$bytes = $this->file_size;
		
		$types = ['B', 'KiB', 'MiB', 'GiB'];
		for($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);
		
		return (round($bytes, 2)." ".$types[$i]);
	}
	
	/**
	 * Get full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return storage_path(config('assets.private_file_path')) . '/' . $this->file_guid;
	}
	
	/**
	 * Get full path to file's thumbnail.
	 *
	 * @return string
	 */
	public function getThumbPath()
	{
		return public_path(config('dx.email.thumbnail_path')) . '/' . $this->file_guid;
	}
	
	/**
	 * Create a new MailAttachment instance and initialize it with data from passed UploadedFile instance.
	 *
	 * @param $file
	 * @return MailAttachment
	 */
	static public function createFromUploadedFile(UploadedFile $file)
	{
		$filePath = storage_path(config('assets.private_file_path')) . '/';
		$thumbPath = public_path(config('dx.email.thumbnail_path')) . '/';
		$fileGuid = Uuid::generate(4) . '.' . $file->getClientOriginalExtension();
		$isImage = (strpos($file->getMimeType(), 'image/') !== false);
		
		$attachment = new self;
		$attachment->file_name = $file->getClientOriginalName();
		$attachment->file_guid = $fileGuid;
		$attachment->file_size = $file->getClientSize();
		$attachment->mime_type = $file->getMimeType();
		$attachment->is_image = $isImage;
		
		$file->move($filePath, $fileGuid);
		
		if($isImage)
		{
			$image = new Image;
			$image->resize($filePath, $fileGuid, 120, 120, $thumbPath);
		}
		
		return $attachment;
	}
}