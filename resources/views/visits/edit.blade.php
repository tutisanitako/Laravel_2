<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Visit Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('visits.update', $visit) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Visit Date -->
                            <div>
                                <x-input-label for="visit_date" :value="__('Visit Date & Time')" />
                                <x-text-input id="visit_date" class="block mt-1 w-full" type="datetime-local" name="visit_date" :value="old('visit_date', $visit->visit_date->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('visit_date')" class="mt-2" />
                            </div>

                            <!-- Diagnosis -->
                            <div>
                                <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                                <textarea id="diagnosis" name="diagnosis" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('diagnosis', $visit->diagnosis) }}</textarea>
                                <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                            </div>

                            <!-- Treatment -->
                            <div>
                                <x-input-label for="treatment" :value="__('Treatment')" />
                                <textarea id="treatment" name="treatment" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('treatment', $visit->treatment) }}</textarea>
                                <x-input-error :messages="$errors->get('treatment')" class="mt-2" />
                            </div>

                            <!-- Prescription -->
                            <div>
                                <x-input-label for="prescription" :value="__('Prescription (Optional)')" />
                                <textarea id="prescription" name="prescription" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prescription', $visit->prescription) }}</textarea>
                                <x-input-error :messages="$errors->get('prescription')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('visits.show', $visit) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Visit') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>