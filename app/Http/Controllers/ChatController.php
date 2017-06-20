<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;

/**
 * Controlls form's chat window
 */
class ChatController extends Controller
{
    /**
     * Saves message
     * @param Request $request Request's data
     * @return JSON Status of request
     */
    public function saveMessage(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|exists:dx_lists,id',
            'item_id' => 'required',
            'message' => 'required_without:file|string',
            'file' => 'required_without:message',
        ]);

        $list_id = $request->input('list_id');
        $item_id =$request->input('item_id');

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        $user_id = \Auth::user()->id;

        if (!$chat) {
            $chat = new \App\Models\Chat\Chat();
            $chat->list_id = $list_id;
            $chat->item_id = $item_id;
            $chat->created_user_id = $user_id;
            $chat->created_time = new \DateTime();
            $chat->modified_user_id = $user_id;
            $chat->modified_time = new \DateTime();
            $chat->save();
        }

        $message = $request->input('message');

        if($message){
            $this->saveTextMessage($message,  $user_id, $chat->id);
        }else{            
            $this->saveFileMessage($request->file('file'),  $user_id, $chat->id);
        }

        $this->addUserToChatByChatID($chat->id, $user_id);

        return response()->json(['success' => 1]);
    }

    /**
     * Saves text message
     *
     * @param string $message Message text
     * @param integer $user_id User's ID
     * @param integer $chat_id Chats's ID
     * @return void
     */
    private function saveTextMessage($message,  $user_id, $chat_id){
        $msg = new \App\Models\Chat\Message();
        $msg->message = $message;
        $msg->created_user_id = $user_id;
        $msg->created_time = new \DateTime();
        $msg->modified_user_id = $user_id;
        $msg->modified_time = new \DateTime();
        $msg->chat_id = $chat_id;
        $msg->save();
    }

    /**
     * Saves file
     *
     * @param File $files File which will be saved
     * @param integer $user_id User's ID
     * @param integer $chat_id Chats's ID
     * @return void
     */
    private function saveFileMessage($files, $user_id, $chat_id){
        $document_path = storage_path(config('assets.private_file_path'));

        foreach($files as $file){
            // Gets random name for file
            $file_path = tempnam($document_path, 'cht');
            
            // Extract file name from full path
            $file_guid = pathinfo($file_path, PATHINFO_FILENAME) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);

            // Gets files real name provided by user
            $file_name = $file->getClientOriginalName();

            // Moves uploaded file from temp directory to storage
            $file->move($document_path, $file_guid);
            $msg = new \App\Models\Chat\Message();
            $msg->file_guid = $file_guid;
            $msg->file_name = $file_name;
            $msg->created_user_id = $user_id;
            $msg->created_time = new \DateTime();
            $msg->modified_user_id = $user_id;
            $msg->modified_time = new \DateTime();
            $msg->chat_id = $chat_id;
            $msg->save();
        }
    }

    public function getFile($chat_id, $message_id){
        $chat = \App\Models\Chat\Chat::find($chat_id);

        $msg =  \App\Models\Chat\Message::find($message_id);

        if (!$chat || !$msg || $msg->chat_id !=  $chat->id) {
            throw new Exceptions\DXCustomException(trans('errors.access_denied_title'));
        }

        $fileCntrl = new FileController();

        $list_msgs = \App\Libraries\DBHelper::getListByTable('dx_chats_msgs');        
        if (!$list_msgs) {
            throw new Exceptions\DXCustomException(trans('errors.access_denied_title'));
        }        
        $list_msgs_id = $list_msgs->id;        

        return $fileCntrl->getFileByOtherRights($chat->list_id, $chat->item_id, $message_id, $list_msgs_id, 'file_name');
    }

    /**
     * Removes user from chat
     *
     * @param Request $request Request's data
     * @return JSON Response
     */
    public function removeUserFromChat(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|exists:dx_lists,id',
            'item_id' => 'required',
            'user_id' => 'required|exists:dx_users,id',
         ]);

        $list_id = $request->input('list_id');
        $item_id =$request->input('item_id');
        $user_id = $request->input('user_id');

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if ($chat) {
            \App\Models\Chat\User::where('chat_id', $chat->id)
                ->where('user_id', $user_id)
                ->delete();

            return response()->json(['success' => 1]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    /**
     * Saves user to chat if he is not yet saved to it
     *
     * @param Request $request Request's data
     * @return JSON Response
     */
    public function addUserToChat(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|exists:dx_lists,id',
            'item_id' => 'required',
            'user_id' => 'required|exists:dx_users,id',
         ]);

        $list_id = $request->input('list_id');
        $item_id =$request->input('item_id');
        $user_id = $request->input('user_id');

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if ($chat) {
            $res =  $this->addUserToChatByChatID($chat->id, $user_id);

            if($res){
                return response()->json(['success' => 1]);
            }else{
                return response()->json(['success' => 0, 'msg' => trans('form.chat.e_user_exist')]);
            }
        } else {
            return response()->json(['success' => 0]);
        }
    }

    /**
     * Saves user to chat if he is not yet saved to it
     *
     * @param int $chat_id Chat's ID
     * @param int $user_id User's ID which will be added to chat
     * @return boolean True if user added, false means it has been already added before
     */
    private function addUserToChatByChatID($chat_id, $user_id)
    {
        $chat_user = \App\Models\Chat\User::where('chat_id', $chat_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$chat_user) {
            $chat_user = new \App\Models\Chat\User();
            $chat_user->chat_id = $chat_id;
            $chat_user->user_id = $user_id;

            // Modified and created user can differ from user which is being added
            $chat_user->created_user_id = \Auth::user()->id;
            $chat_user->created_time = new \DateTime();
            $chat_user->modified_user_id = \Auth::user()->id;
            $chat_user->modified_time = new \DateTime();

            $chat_user->save();

            return true;
        } else{
            return false;
        }
    }

    /**
     * Retrieves users who are added to chat
     *
     * @param int $list_id List's ID
     * @param int $item_id Item's ID
     * @return JSON Response
     */
    public function getChatUsers($list_id, $item_id)
    {
        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if ($chat) {
            $view = view('forms.chat.users', [
                    'users' => $chat->users
                ])->render();

            return response()->json(['success' => 1, 'view' => $view]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    /**
     * Retrieves messages from server
     *
     * @param int $list_id List's ID
     * @param int $item_id Item's ID
     * @param int $last_message_id Latest message ID which was retrieved from server. If it is 0, then retrieve all messages
     * @return JSON Response containing messages
     */
    public function getMessages($list_id, $item_id, $last_message_id)
    {
       /* $this->validate($request, [
            'list_id' => 'required|exists:dx_lists,id',
            'item_id' => 'required',
            'is_init' => 'required|boolean',
        ]);*/

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if (!$chat) {
            return response()->json(['success' => 0]);
        }

        if ($last_message_id == 0) {
            $msgs = $chat->messages;
        } else {
            $msgs = \App\Models\Chat\Message::where('chat_id', $chat->id)
                        ->where('id', '>', $last_message_id)
                        ->get();
        }

         $view = '';

        foreach ($msgs as $msg) {
            $view .= view('forms.chat.record', [
                    'msg' => $msg
                ])->render();
        }

        if ($msgs->count() > 0) {
            $last_message_id = $msg->id;
        } else {
            $last_message_id = 0;
        }

        return response()->json(['success' => 1, 'view' => $view, 'last_message_id' => $last_message_id, 'chat_id' => $chat->id]);
    }
}
