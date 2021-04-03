<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    //


    public function SendMessage(Request $request) {  //recebe userId, idReceiver  message

         \DB::table('messages')->insert(       
                    ['idSender' => $request->userId, 
                    'idReceiver' => $request->idReceiver, 
                    'message' => $request->message,
                    'created_at' => \Carbon\Carbon::now()]
                ); 
    }

    public function GetMessages(Request $request) { //recebe userId e idReceiver

        $messages = \DB::table('messages')->select('idSender','idReceiver','message','created_at')
            ->where(function($q) use($request) {
                $q->where('idSender', $request->userId)
                ->Where('idReceiver', $request->idReceiver);
            })
            ->orWhere(function($q2) use($request) {
                $q2->where('idSender', $request->idReceiver)
                ->Where('idReceiver', $request->userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            

        return response()
        ->json($messages);  

    }

    public function GetActiveChats($userId) {  //recebe userId
        $ids = array();
        
        $output = array();

        $messages = \DB::table('messages')->select('idSender','idReceiver','message','created_at')
            ->where(function($q) use($userId) {
                $q->where('idSender', $userId);
            })
            ->orWhere(function($q2) use($userId) {
                $q2->where('idReceiver', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            foreach ($messages as $message) {
                if ($userId == $message->idSender) {
                    if (($message->idReceiver != $userId) && (in_array($message->idReceiver, $ids) == false)){
                    $ids[] = $message->idReceiver;
                    $output[] = $message->idReceiver;
                    }
                }
                else {
                    if (($message->idSender != $userId) && (in_array($message->idSender, $ids) == false)){
                    $ids[] = $message->idSender;
                    $output[] = $message->idSender;
                    }
                }
                
            }

        return response()
        ->json($output);  
    }
}
