<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function show(Request $request)
    {
        $organization = $request->user()->organization;

        if (!$organization) {
            return response()->json(['message' => 'Ainda não tens uma organização.'], 404);
        }

        $members = $organization->users()->get();

        return response()->json([
            'organization' => $organization,
            'members' => $members,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'email' => $request->email,
            'phone' => $request->phone,
            'plan' => 'free',
        ]);

        $request->user()->update([
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        return response()->json($organization, 201);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,member',
        ]);

        $organization = $request->user()->organization;

        $invitation = Invitation::create([
            'organization_id' => $organization->id,
            'token' => Str::random(32),
            'role' => $request->role,
        ]);

        $link = url('/api/invitations/' . $invitation->token . '/accept');

        return response()->json([
            'message' => 'Link gerado com sucesso!',
            'link' => $link,
        ]);
    }

    public function acceptInvite(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('used', false)
            ->firstOrFail();

        if (auth('sanctum')->check()) {
            auth('sanctum')->user()->update([
                'organization_id' => $invitation->organization_id,
                'role' => $invitation->role,
            ]);
            $invitation->update(['used' => true]);
            return response()->json(['message' => 'Bem-vindo à organização!']);
        }

        return response()->json([
            'message' => 'Regista-te ou faz login para aceitar o convite.',
            'token' => $token,
        ], 401);
    }
}
