<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;

class MeetingController extends Controller
{
    private function getGoogleClient($accessToken)
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessToken($accessToken);
        return $client;
    }

    public function googleAuthUrl(Request $request)
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setScopes([Calendar::CALENDAR_EVENTS]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $state = $request->conversation_id ?? '';
        $client->setState($state);

        return response()->json(['url' => $client->createAuthUrl()]);
    }

    public function googleCallback(Request $request)
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return redirect(env('FRONTEND_URL', 'https://assistia-frontend-l9szban2p-bila5s-projects.vercel.app') . '/conversations?error=google_auth_failed');
        }

        $conversationId = $request->state ?? '';
        $redirectUrl = env('FRONTEND_URL', 'https://assistia-frontend-l9szban2p-bila5s-projects.vercel.app');

        return redirect($redirectUrl . '/conversations/' . $conversationId . '?google_token=' . urlencode(json_encode($token)));
    }

    public function createMeet(Request $request, Conversation $conversation)
    {
        $request->validate([
            'access_token' => 'required',
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        try {
            $client = $this->getGoogleClient(json_decode($request->access_token, true));
            $service = new Calendar($client);

            $event = new Event([
                'summary' => $request->title,
                'start' => new EventDateTime(['dateTime' => date('c', strtotime($request->start)), 'timeZone' => 'Africa/Maputo']),
                'end' => new EventDateTime(['dateTime' => date('c', strtotime($request->end)), 'timeZone' => 'Africa/Maputo']),
                'conferenceData' => new ConferenceData([
                    'createRequest' => new CreateConferenceRequest([
                        'requestId' => uniqid(),
                        'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    ]),
                ]),
            ]);

            $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
            $meetLink = $event->getHangoutLink();

            $conversation->update(['meet_link' => $meetLink, 'meet_title' => $request->title]);

            return response()->json([
                'meet_link' => $meetLink,
                'event_id' => $event->getId(),
                'title' => $request->title,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar reuniao: ' . $e->getMessage()], 500);
        }
    }
}
