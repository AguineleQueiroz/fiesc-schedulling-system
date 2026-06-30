<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Usuário</h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/users/users.form.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div id="notify" class="hidden mb-4 p-4 rounded text-sm font-medium"></div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form id="user-form"
                      data-action="create"
                      data-url="{{ route('users.store') }}"
                      data-redirect="{{ route('users.index') }}"
                      novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required autofocus/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="name"></p>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail <span
                                class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="email"></p>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Tipo de Usuário <span
                                class="text-red-500">*</span></label>
                        <select id="role" name="role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Selecione...</option>
                            <option value="admin">Administrador</option>
                            <option value="atendente">Atendente</option>
                        </select>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="role"></p>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha <span
                                class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required minlength="8"/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="password"></p>
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirme a
                            Senha <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="password_confirmation"></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                            Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
