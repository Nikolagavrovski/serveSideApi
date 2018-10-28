<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use App\Events\NewMessage;

class ContactsController extends Controller
{
    public function getContacts() 
    {
        $contacts = User::all();
        return response()->json($contacts);
    }

    public function getMessagesById($id)
    {
        $messages = Message::where('from', $id)->orWhere('to', $id)->get();
        return response()->json($messages);
    }

    public function send(Request $request) 
    {
        $message = Message::create([
            'from' => $request->from,
            'to' => $request->contact_id,
            'text' => $request->message
        ]);

        broadcast(new NewMessage($message));
        return response()->json($message);
    }
}