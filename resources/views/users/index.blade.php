<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuários</h2>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    + Novo Usuário
                </a>
            @endcan
        </div>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/users/users.index.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div id="notify" class="hidden mb-4 p-4 rounded text-sm font-medium"></div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="users-table">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->role->label() }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">Editar</a>
                                @endcan
                                @can('delete', $user)
                                    <button
                                        data-action="delete-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-url="{{ route('users.destroy', $user) }}"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Excluir
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum usuário
                                cadastrado.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Confirmar exclusão</h3>
            <p class="text-sm text-gray-600 mb-6">
                Deseja realmente excluir o usuário <strong id="modal-user-name"></strong>? Esta ação não pode ser
                desfeita.
            </p>
            <div class="flex justify-end space-x-3">
                <button id="modal-cancel" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                    Cancelar
                </button>
                <button id="modal-confirm" class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                    Excluir
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
