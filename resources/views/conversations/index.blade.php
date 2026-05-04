<x-app-layout>
    <div class="page-title">💬 Conversas</div>

    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('conversations.create') }}" class="btn-gold">+ Nova Conversa</a>
    </div>

    @forelse($conversations as $conversation)
        <div class="conversation-item">
            <div>
                <a href="{{ route('conversations.show', $conversation) }}" class="conversation-link">
                    {{ $conversation->title }}
                </a>
                <span class="badge">{{ $conversation->type === 'meeting' ? 'Reunião' : 'Chat' }}</span>
            </div>
            <form action="{{ route('conversations.destroy', $conversation) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">Apagar</button>
            </form>
        </div>
    @empty
        <div class="card" style="text-align:center; color:#556677;">
            Ainda não tens conversas. Cria a primeira!
        </div>
    @endforelse
</x-app-layout>
