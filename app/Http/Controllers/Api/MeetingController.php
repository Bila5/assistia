<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MeetingController extends Controller
{
    private function getClientId() { return env('GOOGLE_CLIENT_ID'); }
    private function getClientSecret() { return env('GOOGLE_CLIENT_SECRET'); }
    private function getRedirectUri() { return env('GOOGLE_REDIRECT_URI'); }

    public function googleAuthUrl(Request $request)
    {
        $state = hash_hmac('sha256', $request->conversation_id ?? '', env('APP_KEY'));
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => $this->getClientId(),
            'redirect_uri'  => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/calendar.events',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $request->conversation_id . '|' . $state,
        ]);
        return response()->json(['url' => $url]);
    }

    public function googleCallback(Request $request)
    {
        // Valida state para prevenir CSRF
        [$conversationId, $receivedState] = explode('|', $request->state . '|', 2);
        $expectedState = hash_hmac('sha256', $conversationId, env('APP_KEY'));

        if (!hash_equals($expectedState, $receivedState)) {
            return redirect(env('FRONTEND_URL') . '/conversations?error=invalid_state');
        }

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code'          => $request->code,
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri'  => $this->getRedirectUri(),
            'grant_type'    => 'authorization_code',
        ]);

        $token = $response->json();
        $frontendUrl = env('FRONTEND_URL', 'https://assistia-frontend-l9szban2p-bila5s-projects.vercel.app');

        if (isset($token['error'])) {
            return redirect($frontendUrl . '/conversations?error=google_auth_failed');
        }

        return redirect($frontendUrl . '/conversations/' . $conversationId . '?google_token=' . urlencode(json_encode($token)));
    }

    public function createMeet(Request $request, Conversation $conversation)
    {
        $request->validate([
            'access_token' => 'required|string',
            'title'        => 'required|string|max:255',
            'start'        => 'required|date',
            'end'          => 'required|date|after:start',
        ]);

        // Valida que o token pertence ao utilizador autenticado
        $tokenData = json_decode($request->access_token, true);
        if (!$tokenData || !isset($tokenData['access_token'])) {
            return response()->json(['message' => 'Token invalido'], 401);
        }

        // Verifica o token com o Google
        $tokenInfo = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'access_token' => $tokenData['access_token'],
        ])->json();

        if (isset($tokenInfo['error'])) {
            return response()->json(['message' => 'Token Google expirado ou invalido'], 401);
        }

        // Verifica que a conversa pertence ao utilizador autenticado
        if ($conversation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Sem permissao'], 403);
        }

        $response = Http::withToken($tokenData['access_token'])->post(
            'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1',
            [
                'summary' => $request->title,
                'start'   => ['dateTime' => date('c', strtotime($request->start)), 'timeZone' => 'Africa/Maputo'],
                'end'     => ['dateTime' => date('c', strtotime($request->end)), 'timeZone' => 'Africa/Maputo'],
                'conferenceData' => [
                    'createRequest' => [
                        'requestId'           => uniqid(),
                        'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    ],
                ],
            ]
        );

        $event = $response->json();

        if (!isset($event['hangoutLink'])) {
            return response()->json(['message' => 'Erro ao criar reuniao: ' . ($event['error']['message'] ?? 'desconhecido')], 500);
        }

        $conversation->update([
            'meet_link'  => $event['hangoutLink'],
            'meet_title' => $request->title,
        ]);

        return response()->json([
            'meet_link' => $event['hangoutLink'],
            'title'     => $request->title,
        ]);
    }
}
