<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Source;
use App\Models\Team;
use App\Models\Mail;

class MailController extends Controller
{
	protected $sources;
	protected $teams;
	protected $messages;
	protected $folderId;
	protected $folderName;
	protected $folders = [
		'sent' => 'Sent',
		'draft' => 'Draft',
		'scheduled' => 'Scheduled',
	];
	
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
		])->render();
		
		return $result;
	}
	
	public function folder(Request $request)
	{
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
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'folderId' => $this->getFolderId(),
			'folderName' => $this->getFolderName(),
			'folders' => $this->folders,
			'toId' => $toId,
			'toTitle' => $toTitle,
		])->render();
		
		return $result;
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public
	function store(Request $request)
	{
		$to = $request->input('to');
		$subject = $request->input('subject');
		$body = $request->input('body');
		$folder = $request->input('folder');
		
		$mail = new Mail;
		
		$mail->user_id = Auth::user()->id;
		$mail->to = $to;
		$mail->subject = $subject;
		$mail->body = $body;
		$mail->folder = $folder;
		$mail->is_read = false;
		$mail->sent_time = Carbon::now();
		$mail->created_time = Carbon::now();
		
		$mail->save();
		
		$result = [
			'success' => 1,
		];
		
		return response($result);
	}
	
	public
	function ajaxToAutocomplete(Request $request)
	{
		$term = $request->input('term');
		
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
		
		$result = [];
		
		$result['results'][] = [
			'id' => '0',
			'text' => trans('mail.all_company')
		];
		
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
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public
	function show($id)
	{
		$message = Mail::find($id);
		
		$result = view('mail.show', [
			'sources' => $this->getSources(),
			'teams' => $this->getTeams(),
			'folders' => $this->folders,
			'message' => $message,
		])->render();
		
		return $result;
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public
	function edit($id)
	{
		//
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public
	function update(Request $request, $id)
	{
		//
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public
	function destroy($id)
	{
		//
	}
	
	public
	function upload(Request $request)
	{
	}
	
	protected
	function sendMessage($mail)
	{
	}
	
	protected
	function getSources()
	{
		if(!$this->sources)
		{
			$this->sources = Source::orderBy('title', 'asc')->get();
		}
		
		return $this->sources;
	}
	
	protected
	function getTeams()
	{
		if(!$this->teams)
		{
			$this->teams = Team::orderBy('title', 'asc')->get();
		}
		
		return $this->teams;
	}
	
	protected
	function getMessages()
	{
		if(!$this->messages)
		{
			$this->messages = Mail::where('folder', '=', $this->getFolderId())
				->orderBy('sent_time', 'desc')
				->get();
		}
		
		return $this->messages;
	}
	
	protected
	function getFolderId()
	{
		if(!$this->folderId)
		{
			$id = basename(Route::current()->uri());
			
			if(!$id || !isset($this->folders[$id]))
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
	
	protected
	function getFolderName()
	{
		if(!$this->folderName)
		{
			$this->folderName = $this->folders[$this->getFolderId()];
		}
		
		return $this->folderName;
	}
}
