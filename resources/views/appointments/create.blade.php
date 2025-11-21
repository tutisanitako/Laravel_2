<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book New Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('appointments.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Patient -->
                            <div>
                                <x-input-label for="patient_id" :value="__('Patient')" />
                                <select id="patient_id" name="patient_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->user->name }} - {{ $patient->user->email }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('patient_id')" class="mt-2" />
                            </div>

                            <!-- Doctor -->
                            <div>
                                <x-input-label for="doctor_id" :value="__('Doctor')" />
                                <select id="doctor_id" name="doctor_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
                            </div>

                            <!-- Appointment Date -->
                            <div class="md:col-span-2">
                                <x-input-label for="appointment_date" :value="__('Appointment Date & Time')" />
                                <x-text-input id="appointment_date" class="block mt-1 w-full" type="datetime-local" name="appointment_date" :value="old('appointment_date')" required />
                                <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                                <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('appointments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Book Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>