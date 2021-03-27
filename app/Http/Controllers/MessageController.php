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


    public function SendMessage(Request $request) { 

        //validate incoming request 
        $this->validate($request, [
            'idSender' => 'required',
            'idReceiver' => 'required',
            'message' => 'required',
            'token' => 'required'
        ]);

        //todo
        //fazer chamada ao microservice do user para verificar se token é valido

        try {
            
            $message = new Message;
            $message->idSender = $request->input('idSender');
            $message->idReceiver = $request->input('idReceiver');
            $message->message = $request->input('message');

            $message->save();

            //return successful response
            return response()->json(['message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Failed!'], 409);
        }

    }

    public function GetMessages(Request $request) { 

        //todo
        //autenticar o $user atual atraves do microservice dos users usando o token

        $messages = \DB::table('messages')->select('idSender','idReceiver','message','created_at')
            ->where(function($q) use($request,$user) {
                $q->where('idSender', $user -> id)
                ->Where('idReceiver', $request->input('idReceiver'));
            })
            ->orWhere(function($q2) use($request,$user) {
                $q2->where('idSender', $request->input('idReceiver'))
                ->Where('idReceiver', $user -> id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            

        return response()
        ->json($messages);  

    }

    public function GetActiveChats(Request $request) {  #idReceiver
        //todo
        //$user = Auth::User();
        //autenticar o $user atual atraves do microservice dos users usando o token
        
        $ids = array();
        
        $output = array();

        $messages = \DB::table('message')->select('idSender','idReceiver','message','created_at')
            ->where(function($q) use($request,$user) {
                $q->where('idSender', $user -> id);
            })
            ->orWhere(function($q2) use($request,$user) {
                $q2->where('idReceiver', $user -> id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            foreach ($messages as $message) {
                if ($user->id == $message->idSender) {
                    if (($message->idReceiver != $user->id) && (in_array($message->idReceiver, $ids) == false)){
                    $ids[] = $message->idReceiver;
                    $firstName = User::findOrFail($message->idReceiver)->firstName;
                    $lastName = User::findOrFail($message->idReceiver)->lastName;
    
                    $output[] = $message->idReceiver . ',' . $firstName . ',' . $lastName;
                    }
                }
                else {
                    if (($message->idSender != $user->id) && (in_array($message->idSender, $ids) == false)){
                    $ids[] = $message->idSender;
                    $firstName = User::findOrFail($message->idSender)->firstName;
                    $lastName = User::findOrFail($message->idSender)->lastName;
    
                    $output[] = $message->idSender . ',' . $firstName . ',' . $lastName;
                    }
                }
                
            }

        return response()
        ->json($output);  
    }
}
