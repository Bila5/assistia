<x-app-layout>
    <div class="page-title">🔗 Convidar Membro</div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('invite_link'))
        <div class="card" style="border:2px solid #c9a84c;">
            <div class="card-title">🔗 Link de Convite</div>
            <p style="color:#5a6a8a; margin-bottom:0.5rem;">Partilha este link com o membro:</p>
            <div style="background:#f8faff; border:1px solid #dce4f0; padding:12px; border-radius:6px; word-break:break-all; color:#0f1f3d; font-family:monospace;">
                {{ session('invite_link') }}
            </div>
            <p style="color:#e05555; font-size:0.85rem; margin-top:0.5rem;">⚠️ Este link só pode ser usado uma vez!</p>
        </div>
    @endif

    <div class="card" style="max-width:500px;">
        <div class="card-title">Gerar Link de Convite</div>
        <form action="{{ route('invitations.store') }}" method="POST">
            @csrf
            <label class="form-label">Papel do membro</label>
            <select name="role" class="form-input">
                <option value="member">Membro</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" class="btn-gold" style="width:100%;">Gerar Link</button>
        </form>
    </div>

    {{-- Convites anteriores --}}
    <div class="card">
        <div class="card-title">Convites Gerados</div>
        @forelse($invitations as $invitation)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #eef2f8;">
                <div>
                    <span class="badge">{{ ucfirst($invitation->role) }}</span>
                    <span style="font-size:0.8rem; color:#5a6a8a; margin-left:8px;">{{ $invitation->created_at->diffForHumans() }}</span>
                </div>
                <span style="font-size:0.85rem; color:{{ $invitation->used ? '#2a8a2a' : '#c9a84c' }};">
                    {{ $invitation->used ? '✅ Usado' : '⏳ Pendente' }}
                </span>
            </div>
        @empty
            <p style="color:#556677; font-style:italic;">Ainda não geraste convites.</p>
        @endforelse
    </div>

</x-app-layout>
