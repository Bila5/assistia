<x-app-layout>
    <div class="page-title">{{ $conversation->title }} <span class="badge">{{ $conversation->type === 'meeting' ? 'Reunião' : 'Chat' }}</span></div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    {{-- Resumo --}}
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <div class="card-title" style="margin:0; border:none;">🤖 Resumo com IA</div>
            <form action="{{ route('conversations.summarize', $conversation) }}" method="POST">
                @csrf
                <button type="submit" class="btn-gold">Gerar Resumo</button>
            </form>
        </div>
        @if($conversation->summary)
            <div class="summary-box">{!! nl2br(e($conversation->summary)) !!}</div>
        @else
            <p style="color:#556677; font-style:italic;">Ainda não há resumo. Adiciona mensagens e clica em "Gerar Resumo".</p>
        @endif
    </div>

    <div class="grid-2">

        {{-- Mensagens --}}
        <div class="card">
            <div class="card-title">💬 Mensagens</div>

            @forelse($messages as $message)
                <div class="message-item">
                    <div class="message-sender">{{ $message->sender_name }}</div>
                    <div class="message-content">{{ $message->content }}</div>
                </div>
            @empty
                <p style="color:#556677; font-style:italic; margin-bottom:1rem;">Ainda não há mensagens.</p>
            @endforelse

            <form action="{{ route('messages.store', $conversation) }}" method="POST" style="margin-top:1.5rem;">
                @csrf
                <label class="form-label">Nome</label>
                <input type="text" name="sender_name" placeholder="Ex: João" class="form-input" required>
                <label class="form-label">Mensagem</label>
                <textarea name="content" placeholder="Escreve a mensagem..." class="form-input" rows="3" required></textarea>
                <button type="submit" class="btn-blue">Enviar Mensagem</button>
            </form>
        </div>

        {{-- Tarefas --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <div class="card-title" style="margin:0; border:none;">✅ Tarefas</div>
                <form action="{{ route('conversations.extractTasks', $conversation) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-gold" style="font-size:0.8rem; padding:7px 14px;">
                        🤖 Extrair Tarefas
                    </button>
                </form>
            </div>

            @forelse($tasks as $task)
                <div class="task-item">
                    <div>
                        <span class="{{ $task->status === 'done' ? 'task-done' : '' }}">
                            @if($task->priority === 'high') 🔴
                            @elseif($task->priority === 'medium') 🟡
                            @else 🟢
                            @endif
                            {{ $task->title }}
                        </span>
                        @if($task->assigned_to)
                            <span style="color:#556677; font-size:0.85rem;"> ({{ $task->assigned_to }})</span>
                        @endif
                    </div>
                    <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="{{ $task->status === 'pending' ? 'btn-toggle-done' : 'btn-toggle-pending' }}">
                            {{ $task->status === 'pending' ? 'Concluir' : 'Reabrir' }}
                        </button>
                    </form>
                </div>
            @empty
                <p style="color:#556677; font-style:italic; margin-bottom:1rem;">Ainda não há tarefas.</p>
            @endforelse

            <form action="{{ route('tasks.store', $conversation) }}" method="POST" style="margin-top:1.5rem;">
                @csrf
                <label class="form-label">Título da Tarefa</label>
                <input type="text" name="title" placeholder="Ex: Preparar relatório" class="form-input" required>
                <label class="form-label">Atribuir a (opcional)</label>
                <input type="text" name="assigned_to" placeholder="Ex: Maria" class="form-input">
                <label class="form-label">Prioridade</label>
                <select name="priority" class="form-input">
                    <option value="low">🟢 Baixa</option>
                    <option value="medium" selected>🟡 Média</option>
                    <option value="high">🔴 Alta</option>
                </select>
                <button type="submit" class="btn-green">Adicionar Tarefa</button>
            </form>
        </div>

    </div>
</x-app-layout>
