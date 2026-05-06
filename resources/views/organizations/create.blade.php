<x-app-layout>
    <div class="page-title">🏢 Criar Organização</div>

    <div class="card" style="max-width: 550px;">
        <form action="{{ route('organization.store') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="form-label">Nome da Organização</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="Ex: Minha Empresa Lda" class="form-input" required>
            </div>

            <div>
                <label class="form-label">Email (opcional)</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="Ex: geral@empresa.com" class="form-input">
            </div>

            <div>
                <label class="form-label">Telefone (opcional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    placeholder="Ex: +258 84 000 0000" class="form-input">
            </div>

            <button type="submit" class="btn-gold" style="width:100%; text-align:center;">
                Criar Organização
            </button>
        </form>
    </div>
</x-app-layout>
