<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

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
            'message' => 'required|string',
        ]);

        $list_id = $request->input('list_id');
        $item_id =$request->input('item_id');
        $message = $request->input('message');

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if (!$chat) {
            $chat = new \App\Models\Chat\Chat();
            $chat->list_id = $list_id;
            $chat->item_id = $item_id;
            $chat->created_user_id = \Auth::user()->id;
            $chat->created_time = new \DateTime();
            $chat->modified_user_id = \Auth::user()->id;
            $chat->modified_time = new \DateTime();
            $chat->save();
        }

        $msg = new \App\Models\Chat\Message();
        $msg->message = $message;
        $msg->created_user_id = \Auth::user()->id;
        $msg->created_time = new \DateTime();
        $msg->modified_user_id = \Auth::user()->id;
        $msg->modified_time = new \DateTime();
        $msg->chat_id = $chat->id;
        $msg->save();

        return response()->json(['success' => 1]);
    }

    public function getMessages($list_id, $item_id, $is_init)
    {
       /* $this->validate($request, [
            'list_id' => 'required|exists:dx_lists,id',
            'item_id' => 'required',
            'is_init' => 'required|boolean',
        ]);*/

        $chat = \App\Models\Chat\Chat::where('list_id', $list_id)
            ->where('item_id', $item_id)
            ->first();

        if(!$chat){
            return response()->json(['success' => 0]);
        }

        if($is_init == 1){
            $msgs = $chat->messages;
        } else {
           $msgs =  \App\Models\Chat\Message::where('chat_id', $chat->id)
                        ->where('created_time', '>=', new \DateTime())
                        ->get();           
        }

         $view = '';

        foreach($msgs as  $msg){
            $view .= view('forms.chat.record', [
                    'msg' => $msg
                ])->render();
        }

        return response()->json(['success' => 1, 'view' => $view]);


    }
}
