<x-app-layout>
    <div class="page-title">+ Nova Conversa</div>

    <div class="card" style="max-width: 550px;">
        <form action="{{ route('conversations.store') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="form-label">Título</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    placeholder="Ex: Reunião de equipa" class="form-input" required>
            </div>

            <div>
                <label class="form-label">Tipo</label>
                <select name="type" class="form-input">
                    <option value="meeting">Reunião</option>
                    <option value="chat">Chat</option>
                </select>
            </div>

            <button type="submit" class="btn-gold" style="width:100%; text-align:center;">
                Criar Conversa
            </button>
        </form>
    </div>
</x-app-layout>
