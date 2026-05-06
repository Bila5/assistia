<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function create()
    {
        $organization = auth()->user()->organization;
        $invitations = $organization->invitations()->latest()->get();
        return view('invitations.create', compact('organization', 'invitations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,member',
        ]);

        $organization = auth()->user()->organization;

        $invitation = Invitation::create([
            'organization_id' => $organization->id,
            'token' => Str::random(32),
            'role' => $request->role,
        ]);

        $link = route('invitations.accept', $invitation->token);

        return redirect()->route('invitations.create')
            ->with('success', 'Link gerado com sucesso!')
            ->with('invite_link', $link);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('used', false)
            ->firstOrFail();

        if (auth()->check()) {
            auth()->user()->update([
                'organization_id' => $invitation->organization_id,
                'role' => $invitation->role,
            ]);
            $invitation->update(['used' => true]);
            return redirect()->route('dashboard')->with('success', 'Bem-vindo à organização!');
        }

        session(['invitation_token' => $token]);
        return redirect()->route('register')->with('info', 'Regista-te para aceitar o convite!');
    }
}
