<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Agendamento</h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/schedule/schedule.create.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div id="notify" class="hidden mb-4 p-4 rounded text-sm font-medium"></div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form id="schedule-form"
                      data-url="{{ route('appointments.store') }}"
                      data-slots-url="{{ route('schedule.slots') }}"
                      data-redirect="{{ route('appointments.index') }}"
                      novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="attendant_id" class="block text-sm font-medium text-gray-700">Atendente <span
                                class="text-red-500">*</span></label>
                        <select id="attendant_id" name="attendant_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Selecione o atendente...</option>
                            @foreach($attendants as $att)
                                <option value="{{ $att->id }}">{{ $att->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="attendant_id"></p>
                    </div>

                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700">Data <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="date" name="date"
                               min="{{ now()->format('Y-m-d') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="date"></p>
                    </div>

                    <div class="mb-4" id="slots-section" style="display:none">
                        <label for="slot" class="block text-sm font-medium text-gray-700">Horário disponível <span
                                class="text-red-500">*</span></label>
                        <select id="slot" name="slot"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                            <option value="">Selecione um horário...</option>
                        </select>
                        <p id="no-slots-msg" class="hidden mt-1 text-sm text-yellow-700">Nenhum horário disponível para
                            esta data.</p>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="slot"></p>
                        <!-- Campos ocultos preenchidos via JS ao selecionar o slot -->
                        <input type="hidden" id="start_time" name="start_time"/>
                        <input type="hidden" id="end_time" name="end_time"/>
                    </div>

                    <hr class="my-6"/>

                    <div class="mb-4">
                        <label for="client_name" class="block text-sm font-medium text-gray-700">Nome do Cliente <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="client_name" name="client_name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="client_name"></p>
                    </div>

                    <div class="mb-6">
                        <label for="client_phone" class="block text-sm font-medium text-gray-700">Telefone do Cliente
                            <span class="text-red-500">*</span></label>
                        <input type="tel" id="client_phone" name="client_phone"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required/>
                        <p class="mt-1 text-sm text-red-600 hidden" data-error-for="client_phone"></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('appointments.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                            Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
