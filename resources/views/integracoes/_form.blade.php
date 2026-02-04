<div>
    <label for="sistema" class="block text-sm font-medium text-foreground mb-2">Sistema <span class="text-destructive">*</span></label>
    <input
        type="text"
        id="sistema"
        name="sistema"
        value="{{ old('sistema', optional($integracao)->sistema ?? '') }}"
        required
        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        placeholder="Ex: Sistema PACS X"
    />
    <x-input-error class="mt-1" :messages="$errors->get('sistema')" />
</div>

<div>
    <label for="descricao" class="block text-sm font-medium text-foreground mb-2">Descrição</label>
    <textarea
        id="descricao"
        name="descricao"
        rows="2"
        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        placeholder="Descrição da integração"
    >{{ old('descricao', optional($integracao)->descricao ?? '') }}</textarea>
    <x-input-error class="mt-1" :messages="$errors->get('descricao')" />
</div>

<div>
    <label for="endpoint" class="block text-sm font-medium text-foreground mb-2">Endpoint <span class="text-destructive">*</span></label>
    <input
        type="text"
        id="endpoint"
        name="endpoint"
        value="{{ old('endpoint', optional($integracao)->endpoint ?? '') }}"
        required
        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        placeholder="https://api.exemplo.com/v1"
    />
    <x-input-error class="mt-1" :messages="$errors->get('endpoint')" />
</div>

<div>
    <label for="slug" class="block text-sm font-medium text-foreground mb-2">Slug <span class="text-destructive">*</span></label>
    <input
        type="text"
        id="slug"
        name="slug"
        value="{{ old('slug', optional($integracao)->slug ?? '') }}"
        required
        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        placeholder="sistema-pacs-x"
    />
    <p class="text-xs text-muted-foreground mt-1">Identificador único em minúsculas, separado por hífens</p>
    <x-input-error class="mt-1" :messages="$errors->get('slug')" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="username" class="block text-sm font-medium text-foreground mb-2">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            value="{{ old('username', optional($integracao)->username ?? '') }}"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Usuário da API"
        />
        <x-input-error class="mt-1" :messages="$errors->get('username')" />
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-foreground mb-2">Senha</label>
        <input
            type="password"
            id="password"
            name="password"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="{{ $integracao ? 'Deixe em branco para manter' : 'Senha da API' }}"
        />
        @if($integracao)
            <p class="text-xs text-muted-foreground mt-1">Deixe em branco para manter a senha atual</p>
        @endif
        <x-input-error class="mt-1" :messages="$errors->get('password')" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="auth" class="block text-sm font-medium text-foreground mb-2">Tipo de Autenticação <span class="text-destructive">*</span></label>
        <select
            id="auth"
            name="auth"
            required
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        >
            <option value="basic" {{ old('auth', optional($integracao)->auth ?? '') === 'basic' ? 'selected' : '' }}>Basic</option>
            <option value="bearer" {{ old('auth', optional($integracao)->auth ?? '') === 'bearer' ? 'selected' : '' }}>Bearer</option>
            <option value="wss" {{ old('auth', optional($integracao)->auth ?? '') === 'wss' ? 'selected' : '' }}>WebSocket (WSS)</option>
        </select>
        <x-input-error class="mt-1" :messages="$errors->get('auth')" />
    </div>
    <div>
        <label for="tipo" class="block text-sm font-medium text-foreground mb-2">Tipo de Integração <span class="text-destructive">*</span></label>
        <select
            id="tipo"
            name="tipo"
            required
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        >
            <option value="rest" {{ old('tipo', optional($integracao)->tipo ?? '') === 'rest' ? 'selected' : '' }}>REST</option>
            <option value="soap" {{ old('tipo', optional($integracao)->tipo ?? '') === 'soap' ? 'selected' : '' }}>SOAP</option>
        </select>
        <x-input-error class="mt-1" :messages="$errors->get('tipo')" />
    </div>
</div>
