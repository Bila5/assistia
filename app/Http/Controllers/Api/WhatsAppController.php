<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function webhook(Request $request)
    {
        $from        = $request->input('From');
        $body        = $request->input('Body');
        $profileName = $request->input('ProfileName', 'WhatsApp User');

        if (!$from || !$body) {
            return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 400)
                ->header('Content-Type', 'text/xml');
        }

        $conversation = Conversation::firstOrCreate(
            ['whatsapp_from' => $from],
            [
                'title'   => 'WhatsApp - ' . $profileName,
                'type'    => 'whatsapp',
                'user_id' => 1,
            ]
        );

        $conversation->messages()->create([
            'content'     => $body,
            'sender_role' => 'user',
        ]);

        return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 200)
            ->header('Content-Type', 'text/xml');
    }
}
