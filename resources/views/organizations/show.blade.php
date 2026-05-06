<x-app-layout>
    <div class="page-title">🏢 {{ $organization->name }}</div>

    <div class="grid-2">

        {{-- Info da Organização --}}
        <div class="card">
            <div class="card-title">Informações</div>
            <p><span style="color:#5a6a8a;">Nome:</span> {{ $organization->name }}</p>
            <p><span style="color:#5a6a8a;">Email:</span> {{ $organization->email ?? 'Não definido' }}</p>
            <p><span style="color:#5a6a8a;">Telefone:</span> {{ $organization->phone ?? 'Não definido' }}</p>
            <p><span style="color:#5a6a8a;">Plano:</span>
                <span class="badge">{{ ucfirst($organization->plan) }}</span>
            </p>
        </div>

        {{-- Membros --}}
        <div class="card">
            <div class="card-title">👥 Membros</div>

            @foreach($members as $member)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #eef2f8;">
                    <div>
                        <div style="font-weight:600; color:#0f1f3d;">{{ $member->name }}</div>
                        <div style="font-size:0.85rem; color:#5a6a8a;">{{ $member->email }}</div>
                    </div>
                    <span class="badge">{{ ucfirst($member->role) }}</span>
                </div>
            @endforeach

            @if(auth()->user()->isAdmin())
                <a href="{{ route('invitations.create') }}" class="btn-gold" style="width:100%; text-align:center; display:block; margin-top:1.5rem;">
    + Convidar Membro
</a>
            @endif
        </div>

    </div>
</x-app-layout>
