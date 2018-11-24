<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use App\Events\NewMessage;
use Pusher\Laravel\Facades\Pusher;
use Google\Cloud\Translate\TranslateClient;


class ContactsController extends Controller
{
    public function getContacts($id) 
    {   
        $contacts = User::where('id', '!=', $id)->get();
        return response()->json($contacts);
    }

    public function getMessagesById($id)
    {
        $messages = Message::where('from', $id)->orWhere('to', $id)->get(); // poruke koje su from treba prevesti na zeljeni jezik 
        return response()->json($messages);
    }

    public function send(Request $request) 
    {
        $message = Message::create([
            'from' => $request->from,
            'to' => $request->contact_id,
            'text' => $request->message 
        ]);


        // 1. Uzmemu message i posaljemo prevod dok na dalju obradu tj. krajni korisnik bi video poruku u njegovom zeljenom jeziku (postigli smo live komunikaciju u zeljenom jeziku) -> getTargetLanguage 
        // 2. Sledeci put ce getContactbyID dobiti original ali bez prevoda, live komunikacija tece i dalje
        //$text = $message->text;
        //$lang = 'fr';



         /*
        //
            // Translating data
        //
        */ 
        
        $targetLanguage = 'fr';    
        $sourceLanguage = 'sr';
        $text = $message->text;
      
        $result = self::googleTranslateText($text, $targetLanguage, $sourceLanguage);

        /*
        //
            // Data transmited to the user via pusher
        //
        */ 
        $transMessage = [
            'from' => $request->from,
             'to' => $request->contact_id,
            'text' => $result,
            "updated_at" => $message->updated_at,
            "created_at" => $message->created_at,
            "id" => $message->id
        ];
        
        
        Pusher::trigger('messages-'.$request->contact_id, 'new-message', ['message' => $transMessage]);
        Pusher::getSettings();
        
        return response()->json($message, 201);
    }

    // Translate engine
    public function googleTranslateText($text, $target, $source=null)
    {   
        
            putenv('GOOGLE_APPLICATION_CREDENTIALS=C:\Development tools\xampp\htdocs\chatapp\server_chatapp\configkey.json');
                $translate = new TranslateClient([
                'projectId' => env('GOOGLE_PROJECTID')
                ]);

        if ($text && $target && $source) {
                    $translation = $translate->translate($text, [
                    'target' => $target,
                    'source' => $source,
                    'model' => 'nmt'
                    ]);

                    if (strpos($translation['text'], '&#39;') !== false) {
                        return str_replace("&#39;", "'", $translation['text']);
                    }

                return $translation['text'];
            }
        else if ($text && $target){
                    $translation = $translate->translate($text, [
                        'target' => $target,
                        'model' => 'nmt'
                    ]);
                    if (strpos($translation['text'], '&#39;') !== false) {
                        return str_replace("&#39;", "'", $translation['text']);
                    }

             return $translation['text'];
            }
        else {
                return FALSE;
         }
    }
}