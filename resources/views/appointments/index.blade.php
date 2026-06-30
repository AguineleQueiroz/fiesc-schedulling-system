<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendamentos</h2>
            <a href="{{ route('appointments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                + Novo Agendamento
            </a>
        </div>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/appointments/appointments.list.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div id="notify" class="hidden mb-4 p-4 rounded text-sm font-medium"></div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atendente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr data-appointment-id="{{ $appointment->id }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $appointment->attendant->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $appointment->client_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->client_phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $appointment->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Illuminate\Support\Str::substr($appointment->start_time, 0, 5) }}
                                – {{ \Illuminate\Support\Str::substr($appointment->end_time, 0, 5) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($appointment->status->value === 'scheduled')
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            {{ $appointment->status->label() }}
                                        </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            {{ $appointment->status->label() }}
                                        </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($appointment->status->value === 'scheduled')
                                    <button
                                        data-action="cancel-appointment"
                                        data-id="{{ $appointment->id }}"
                                        data-url="{{ route('appointments.destroy', $appointment) }}"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Cancelar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum agendamento
                                encontrado.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de cancelamento -->
    <div id="modal-cancel" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Cancelar agendamento</h3>
            <p class="text-sm text-gray-600 mb-6">
                Deseja realmente cancelar este agendamento? O horário ficará disponível novamente.
            </p>
            <div class="flex justify-end space-x-3">
                <button id="modal-cancel-btn"
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Fechar
                </button>
                <button id="modal-confirm-btn" class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                    Cancelar Agendamento
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
