<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function create()
    {
        return view('organizations.create');
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

        auth()->user()->update([
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        return redirect()->route('dashboard')->with('success', 'Organização criada com sucesso!');
    }

    public function show()
    {
        $organization = auth()->user()->organization;

        if (!$organization) {
            return redirect()->route('organization.create')
                ->with('error', 'Ainda não tens uma organização. Cria uma primeiro!');
        }

        $members = $organization->users()->get();
        return view('organizations.show', compact('organization', 'members'));
    }
}
