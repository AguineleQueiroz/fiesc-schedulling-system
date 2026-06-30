<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuário</h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/users/users.form.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div id="notify" class="hidden mb-4 p-4 rounded text-sm font-medium"></div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form id="user-form"
                      data-action="edit"
                      data-url="{{ route('users.update', $user) }}"
                      data-redirect="{{ route('users.index') }}"
                      novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                               value="{{ $user->name }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required autofocus/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="name"></p>
                    </div>

                    {{-- Role: apenas admin pode ver e alterar; e não pode alterar o próprio role --}}
                    @if(auth()->user()->isAdmin() && auth()->id() !== $user->id)
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700">Tipo de Usuário <span
                                    class="text-red-500">*</span></label>
                            <select id="role" name="role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="admin" @selected($user->role->value === 'admin')>Administrador</option>
                                <option value="atendente" @selected($user->role->value === 'atendente')>Atendente
                                </option>
                            </select>
                            <p class="mt-1 text-sm text-red-600 hidden" data-error-for="role"></p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
