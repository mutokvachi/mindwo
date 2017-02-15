<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;
use App\Models\Source;
use App\Models\Team;
use App\Models\Mail;

/**
 * Class MailController
 *
 * Controller for email sending interface.
 *
 * @package App\Http\Controllers\
 */
class MailController extends Controller
{
	protected $sources;
	protected $teams;
	protected $messages;
	protected $counts;
	protected $folderId;
	protected $folderName;
	protected $pageCount;
	protected $itemsPerPage;
	protected $folders = ['sent', 'draft', 'scheduled'];
	protected $models = [
		'dept' => 'App\Models\Source',
		'team' => 'App\Models\Team',
		'empl' => 'App\User',
	];
	
	public function __construct()
	{
		$this->itemsPerPage = config('dx.email.items_per_page', 20);
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$result = view('mail.index', [
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'messages' => $this->getMessages(),
			'folderId' => $this->getFolderId(),
			'folderName' => $this->getFolderName(),
			'folders' => $this->folders,
			'counts' => $this->getCounts(),
			'pageCount' => $this->getPageCount(),
			'page' => $request->input('page', 1),
			'itemsPerPage' => $this->itemsPerPage,
		])->render();
		
		return $result;
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$message = Mail::findOrFail($id);
		
		$result = view('mail.show', [
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'folders' => $this->folders,
			'message' => $message,
			'counts' => $this->getCounts(),
		])->render();
		
		return $result;
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$toId = $toTitle = '';
		
		if(($to = $request->input('to', null)) !== null)
		{
			if($to == '0')
			{
				$toId = '0';
				$toTitle = trans('mail.all_company');
			}
			elseif(strpos($to, ':') > 0)
			{
				list($type, $id) = explode(':', $to);
				
				if(in_array($type, ['dept', 'team']))
				{
					if($type == 'dept')
					{
						$item = Source::find($id);
					}
					else
					{
						$item = Team::find($id);
					}
					
					if($item)
					{
						$toId = "$type:$id";
						$toTitle = $item->title;
					}
				}
			}
		}
		
		$result = view('mail.compose', [
			'mode' => 'compose',
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'folders' => $this->folders,
			'counts' => $this->getCounts(),
			'toId' => $toId,
			'toTitle' => $toTitle,
		])->render();
		
		return $result;
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$message = Mail::findOrFail($id);
		
		$result = view('mail.compose', [
			'mode' => 'edit',
			'message' => $message,
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'folders' => $this->folders,
			'counts' => $this->getCounts(),
			'folderId' => $this->getFolderId(),
			'folderName' => $this->getFolderName(),
		])->render();
		
		return $result;
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$to = $request->input('to');
		$subject = $request->input('subject');
		$sendTime = $request->input('sendTime');
		$body = $request->input('body');
		$folder = $request->input('folder');
		
		$mail = new Mail;
		
		$mail->to = serialize($this->convertRecipientsList($to));
		$mail->subject = $subject;
		$mail->body = $body;
		$mail->is_read = false;
		$mail->created_time = Carbon::now();
		$mail->created_user_id = Auth::user()->id;
		$mail->modified_time = Carbon::now();
		$mail->modified_user_id = Auth::user()->id;
		
		if($sendTime)
		{
			$mail->send_time = Carbon::createFromFormat(config('dx.txt_datetime_format', 'Y-m-d H:i'), $sendTime)->toDateTimeString();
			
			if($folder != 'draft')
			{
				$folder = 'scheduled';
			}
		}
		
		$mail->folder = $folder;
		$mail->save();
		
		if($folder == 'sent')
		{
			$mail->send();
		}
		
		$result = [
			'success' => 1,
			'id' => $mail->id,
			'folder' => $folder,
			'count' => $this->getCounts()[$folder],
		];
		
		return response($result);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$to = $request->input('to');
		$subject = $request->input('subject');
		$sendTime = $request->input('sendTime');
		$body = $request->input('body');
		$folder = $request->input('folder');
		
		$mail = Mail::findOrFail($id);
		
		$mail->to = serialize($this->convertRecipientsList($to));
		$mail->subject = $subject;
		$mail->body = $body;
		$mail->modified_time = Carbon::now();
		$mail->modified_user_id = Auth::user()->id;
		
		if($sendTime)
		{
			$mail->send_time = Carbon::createFromFormat(config('dx.txt_datetime_format', 'Y-m-d H:i'), $sendTime)->toDateTimeString();
			
			if($folder != 'draft')
			{
				$folder = 'scheduled';
			}
		}
		
		$mail->folder = $folder;
		$mail->save();
		
		if($folder == 'sent')
		{
			$mail->send();
		}
		
		$result = [
			'success' => 1,
			'id' => $mail->id,
			'folder' => $folder,
			'count' => $this->getCounts()[$folder],
		];
		
		return response($result);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Mail::destroy($id);
		
		$result = [
			'success' => 1
		];
		
		return response($result);
	}
	
	/**
	 * Delete arbitrary number of messages by their ids.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function massDelete(Request $request)
	{
		$ids = $request->input('ids', []);
		
		if(is_array($ids) && !empty($ids))
		{
			Mail::whereIn('id', $ids)->delete();
		}
		
		$result = [
			'success' => 1,
		];
		
		return $result;
	}
	
	public function upload(Request $request)
	{
	}
	
	/**
	 * Search departments, teams and user names for pattern entered by user in To field, and return results in format
	 * required by Select2 plugin.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxToAutocomplete(Request $request)
	{
		$term = trim($request->input('term'));
		$result = [
			'results' => []
		];
		
		if(!strlen($term))
		{
			return response($result);
		}
		
		$departments = [];
		foreach(Source::where('title', 'like', '%' . $term . '%')->orderBy('title')->get() as $source)
		{
			$departments[] = [
				'id' => 'dept:' . $source->id,
				'text' => $source->title
			];
		}
		
		$teams = [];
		foreach(Team::where('title', 'like', '%' . $term . '%')->orderBy('title')->get() as $team)
		{
			$teams[] = [
				'id' => 'team:' . $team->id,
				'text' => $team->title
			];
		}
		
		$employees = [];
		foreach(User::where('display_name', 'like', '%' . $term . '%')->orderBy('display_name')->get() as $employee)
		{
			$employees[] = [
				'id' => 'empl:' . $employee->id,
				'text' => $employee->display_name
			];
		}
		
		$allCompany = trans('mail.all_company');
		
		if(mb_stripos($allCompany, $term) !== false)
		{
			$result['results'][] = [
				'id' => '0:0',
				'text' => $allCompany
			];
		}
		
		if(!empty($departments))
		{
			$result['results'][] = [
				'text' => trans('mail.departments'),
				'children' => $departments
			];
		}
		if(!empty($teams))
		{
			$result['results'][] = [
				'text' => trans('mail.teams'),
				'children' => $teams
			];
		}
		if(!empty($employees))
		{
			$result['results'][] = [
				'text' => trans('mail.employees'),
				'children' => $employees
			];
		}
		
		return response($result);
	}
	
	/**
	 * Convert value of the To field to an associative array, suitable to store in database.
	 *
	 * @param $to
	 * @return array
	 */
	protected function convertRecipientsList($to)
	{
		if(!is_array($to))
		{
			$to = [];
		}
		
		$allCompany = false;
		$recipients = false;
		
		$tmp = [
			'dept' => [],
			'team' => [],
			'empl' => [],
		];
		
		// put ids to associative array
		foreach($to as $value)
		{
			if(!strpos($value, ':'))
			{
				continue;
			}
			
			list($type, $id) = explode(':', $value);
			
			if($type == '0' && $id == '0')
			{
				$allCompany = true;
			}
			
			elseif(in_array($type, array_keys($this->models)))
			{
				$recipients = true;
				$tmp[$type][] = $id;
			}
		}
		
		$result = [];
		
		if($allCompany && !$recipients)
		{
			$result[0][] = [
				'id' => 0,
				'text' => trans('mail.all_company')
			];
		}
		
		else
		{
			foreach($tmp as $type => $ids)
			{
				if(empty($ids))
				{
					continue;
				}
				
				$model = $this->models[$type];
				$collection = $model::whereIn('id', $ids)->get();
				
				foreach($collection as $item)
				{
					$result[$type][] = [
						'id' => $item->id,
						'text' => ($type == 'empl' ? $item->display_name : $item->title),
					];
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Get number of messages in folders.
	 *
	 * @return mixed
	 */
	protected function getCounts()
	{
		if(!$this->counts)
		{
			foreach($this->folders as $id)
			{
				$this->counts[$id] = Mail::where('folder', '=', $id)->count();
			}
		}
		
		return $this->counts;
	}
	
	/**
	 * Get ID of current folder.
	 *
	 * @return string
	 */
	protected function getFolderId()
	{
		if(!$this->folderId)
		{
			$id = basename(Route::current()->uri());
			
			if(!$id || !in_array($id, $this->folders))
			{
				$this->folderId = 'sent';
			}
			else
			{
				$this->folderId = $id;
			}
		}
		
		return $this->folderId;
	}
	
	/**
	 * Get name of current folder.
	 *
	 * @return string|\Symfony\Component\Translation\TranslatorInterface
	 */
	protected function getFolderName()
	{
		if(!$this->folderName)
		{
			$this->folderName = trans('mail.'.$this->getFolderId());
		}
		
		return $this->folderName;
	}
	
	/**
	 * Get messages, taking into account current folder, page and number of items per page.
	 *
	 * @return mixed
	 */
	protected function getMessages()
	{
		$page = \Illuminate\Support\Facades\Request::input('page', 1);
		
		if(!$this->messages)
		{
			$this->messages = Mail::where('folder', '=', $this->getFolderId())
				->orderBy($this->getFolderId() == 'sent' ? 'sent_time' : 'modified_time', 'desc')
				->offset(($page - 1) * $this->itemsPerPage)
				->limit($this->itemsPerPage)
				->get();
		}
		
		return $this->messages;
	}
	
	/**
	 * Get number of pages in current folder.
	 *
	 * @return float|int
	 */
	public function getPageCount()
	{
		if($this->pageCount === null)
		{
			$this->pageCount = $this->getCounts()[$this->getFolderId()] / $this->itemsPerPage;
		}
		
		return $this->pageCount;
	}
	
	/**
	 * Get departments ordered by title.
	 *
	 * @return mixed
	 */
	protected function getSources()
	{
		if(!$this->sources)
		{
			$this->sources = Source::orderBy('title', 'asc')->get();
		}
		
		return $this->sources;
	}
	
	/**
	 * Get teams ordered by title.
	 *
	 * @return mixed
	 */
	protected function getTeams()
	{
		if(!$this->teams)
		{
			$this->teams = Team::orderBy('title', 'asc')->get();
		}
		
		return $this->teams;
	}
}
