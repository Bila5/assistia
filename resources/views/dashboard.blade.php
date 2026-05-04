<x-app-layout>
    <div class="page-title">📊 Dashboard</div>

    {{-- Estatísticas --}}
    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1.5rem; margin-bottom:1.5rem;">

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#c9a84c; font-family:'Playfair Display',serif;">{{ $totalConversations }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">Conversas</div>
        </div>

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#c9a84c; font-family:'Playfair Display',serif;">{{ $totalMessages }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">Mensagens</div>
        </div>

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#c9a84c; font-family:'Playfair Display',serif;">{{ $totalTasks }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">Tarefas</div>
        </div>

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#e05555; font-family:'Playfair Display',serif;">{{ $pendingTasks }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">Tarefas Pendentes</div>
        </div>

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#2a8a5a; font-family:'Playfair Display',serif;">{{ $doneTasks }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">Tarefas Concluídas</div>
        </div>

        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; color:#e05555; font-family:'Playfair Display',serif;">{{ $highPriorityTasks }}</div>
            <div style="color:#5a6a8a; font-size:0.9rem; margin-top:4px;">🔴 Alta Prioridade</div>
        </div>

    </div>

    {{-- Conversas Recentes --}}
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <div class="card-title" style="margin:0; border:none;">🕐 Conversas Recentes</div>
            <a href="{{ route('conversations.index') }}" class="btn-gold" style="font-size:0.85rem; padding:7px 14px;">Ver Todas</a>
        </div>

        @forelse($recentConversations as $conversation)
            <div class="conversation-item">
                <div>
                    <a href="{{ route('conversations.show', $conversation) }}" class="conversation-link">
                        {{ $conversation->title }}
                    </a>
                    <span class="badge">{{ $conversation->type === 'meeting' ? 'Reunião' : 'Chat' }}</span>
                </div>
                <span style="color:#5a6a8a; font-size:0.8rem;">{{ $conversation->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <p style="color:#556677; font-style:italic;">Ainda não tens conversas.</p>
        @endforelse
    </div>

</x-app-layout>
