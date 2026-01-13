@if (Auth::user()->tipo === 'medico')

    {{-- FORMULÁRIO DE UPLOAD DA ASSINATURA --}}
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Atualizar Assinatura') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Upload de assinatura digitalizada (exclusivo para médicos).") }}
            </p>
        </header>

        <form method="post"
              action="{{route('profile.update-signature')}}"
              class="mt-6 space-y-6"
              enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="signature" value="Assinatura" />

                <input
                    id="signature"
                    name="signature"
                    type="file"
                    accept="image/*"
                    required
                    class="mt-1 block w-full text-sm text-gray-700
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                        border border-gray-300 rounded-md"
                />

                <x-input-error class="mt-2" :messages="$errors->get('signature')" />
            </div>

            <x-primary-button>
                {{ __('Salvar Assinatura') }}
            </x-primary-button>
        </form>
    </section>

@else
    <div class="mt-6 p-4 rounded-md bg-yellow-50 border border-yellow-200">
        <p class="text-sm text-yellow-800">
            ⚠️ Upload de assinatura digitalizada disponível apenas para usuários do tipo <strong>médico</strong>.
        </p>
    </div>
@endif
