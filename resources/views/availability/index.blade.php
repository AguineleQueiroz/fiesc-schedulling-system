<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disponibilidade dos Atendentes</h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/modules/availability/availability.form.js'])
    @endpush

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="notify" class="hidden mb-2 p-4 rounded text-sm font-medium"></div>

            <!-- Seleção de atendente -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <label for="attendant-select" class="block text-sm font-medium text-gray-700 mb-2">
                    Selecione o atendente
                </label>
                <select id="attendant-select"
                        class="block w-full sm:w-72 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- selecione --</option>
                    @foreach($attendants as $att)
                        <option value="{{ $att->id }}">{{ $att->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lista de disponibilidades -->
            <div id="availability-section" class="hidden bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800">Janelas cadastradas</h3>
                    <button id="btn-add-availability"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                        + Adicionar
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200" id="availability-table">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dia da Semana</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fim</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ativo</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                    </thead>
                    <tbody id="availability-body" class="divide-y divide-gray-100"></tbody>
                </table>

                <p id="availability-empty" class="hidden text-sm text-gray-500 mt-4">Nenhuma disponibilidade
                    cadastrada.</p>
            </div>

            <!-- Formulário de cadastro/edição -->
            <div id="availability-form-section" class="hidden bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4" id="form-title">Nova Disponibilidade</h3>

                <form id="availability-form" novalidate>
                    <input type="hidden" id="availability-id" value=""/>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dia da Semana <span
                                    class="text-red-500">*</span></label>
                            <select id="day_of_week" name="day_of_week"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="">Selecione...</option>
                                <option value="0">Domingo</option>
                                <option value="1">Segunda-feira</option>
                                <option value="2">Terça-feira</option>
                                <option value="3">Quarta-feira</option>
                                <option value="4">Quinta-feira</option>
                                <option value="5">Sexta-feira</option>
                                <option value="6">Sábado</option>
                            </select>
                            <p class="mt-1 text-sm text-red-600 hidden" data-error-for="day_of_week"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ativo? <span
                                    class="text-red-500">*</span></label>
                            <select id="active" name="active"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora Inicial <span
                                    class="text-red-500">*</span></label>
                            <input type="time" id="start_time" name="start_time"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   required/>
                            <p class="mt-1 text-sm text-red-600 hidden" data-error-for="start_time"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora Final <span
                                    class="text-red-500">*</span></label>
                            <input type="time" id="end_time" name="end_time"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   required/>
                            <p class="mt-1 text-sm text-red-600 hidden" data-error-for="end_time"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" id="btn-cancel-form"
                                class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                            Cancelar
                        </button>
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
