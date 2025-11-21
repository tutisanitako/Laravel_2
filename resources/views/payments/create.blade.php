<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Payment Record') }}
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
                    @if($visit)
                    <!-- Show visit details if pre-selected -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-md font-semibold mb-2">Visit Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="font-medium">Patient:</span> {{ $visit->appointment->patient->user->name }}
                            </div>
                            <div>
                                <span class="font-medium">Doctor:</span> {{ $visit->appointment->doctor->user->name }}
                            </div>
                            <div>
                                <span class="font-medium">Visit Date:</span> {{ $visit->visit_date->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Visit -->
                            <div class="md:col-span-2">
                                <x-input-label for="visit_id" :value="__('Visit')" />
                                <select id="visit_id" name="visit_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required {{ $visit ? 'readonly' : '' }}>
                                    @if($visit)
                                    <option value="{{ $visit->id }}" selected>
                                        {{ $visit->appointment->patient->user->name }} - {{ $visit->appointment->doctor->user->name }} - {{ $visit->visit_date->format('M d, Y') }}
                                    </option>
                                    @else
                                    <option value="">Select Visit</option>
                                    @foreach($visits as $v)
                                    <option value="{{ $v->id }}" {{ old('visit_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->appointment->patient->user->name }} - {{ $v->appointment->doctor->user->name }} - {{ $v->visit_date->format('M d, Y') }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('visit_id')" class="mt-2" />
                            </div>

                            <!-- Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Amount')" />
                                <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="amount" :value="old('amount')" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Method</option>
                                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="insurance" {{ old('payment_method') === 'insurance' ? 'selected' : '' }}>Insurance</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <x-input-label for="status" :value="__('Payment Status')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="unpaid" {{ old('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('payments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Payment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>