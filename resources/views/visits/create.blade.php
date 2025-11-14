<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Visit Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($appointment)
                    <!-- Show appointment details if pre-selected -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-md font-semibold mb-2">Appointment Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="font-medium">Patient:</span> {{ $appointment->patient->user->name }}
                            </div>
                            <div>
                                <span class="font-medium">Doctor:</span> {{ $appointment->doctor->user->name }}
                            </div>
                            <div>
                                <span class="font-medium">Appointment Date:</span> {{ $appointment->appointment_date->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('visits.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Appointment -->
                            <div>
                                <x-input-label for="appointment_id" :value="__('Appointment')" />
                                <select id="appointment_id" name="appointment_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required {{ $appointment ? 'readonly' : '' }}>
                                    @if($appointment)
                                    <option value="{{ $appointment->id }}" selected>
                                        {{ $appointment->patient->user->name }} - {{ $appointment->doctor->user->name }} - {{ $appointment->appointment_date->format('M d, Y') }}
                                    </option>
                                    @else
                                    <option value="">Select Appointment</option>
                                    @foreach($appointments as $apt)
                                    <option value="{{ $apt->id }}" {{ old('appointment_id') == $apt->id ? 'selected' : '' }}>
                                        {{ $apt->patient->user->name }} - {{ $apt->doctor->user->name }} - {{ $apt->appointment_date->format('M d, Y') }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('appointment_id')" class="mt-2" />
                            </div>

                            <!-- Visit Date -->
                            <div>
                                <x-input-label for="visit_date" :value="__('Visit Date & Time')" />
                                <x-text-input id="visit_date" class="block mt-1 w-full" type="datetime-local" name="visit_date" :value="old('visit_date', now()->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('visit_date')" class="mt-2" />
                            </div>

                            <!-- Diagnosis -->
                            <div>
                                <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                                <textarea id="diagnosis" name="diagnosis" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('diagnosis') }}</textarea>
                                <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                            </div>

                            <!-- Treatment -->
                            <div>
                                <x-input-label for="treatment" :value="__('Treatment')" />
                                <textarea id="treatment" name="treatment" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('treatment') }}</textarea>
                                <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                            </div>

                            <!-- Prescription -->
                            <div>
                                <x-input-label for="prescription" :value="__('Prescription (Optional)')" />
                                <textarea id="prescription" name="prescription" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prescription') }}</textarea>
                                <x-input-error :messages="$errors->get('prescription')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('visits.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Visit') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>