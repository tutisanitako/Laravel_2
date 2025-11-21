<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visit Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Visit Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Patient</p>
                            <p class="text-base text-gray-900">{{ $visit->appointment->patient->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $visit->appointment->patient->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Doctor</p>
                            <p class="text-base text-gray-900">{{ $visit->appointment->doctor->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $visit->appointment->doctor->specialization }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Visit Date & Time</p>
                            <p class="text-base text-gray-900">{{ $visit->visit_date->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $visit->visit_date->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Appointment Date</p>
                            <p class="text-base text-gray-900">{{ $visit->appointment->appointment_date->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Diagnosis</p>
                            <p class="text-base text-gray-900">{{ $visit->diagnosis }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Treatment</p>
                            <p class="text-base text-gray-900">{{ $visit->treatment }}</p>
                        </div>

                        @if($visit->prescription)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 mb-2">Prescription</p>
                            <p class="text-base text-gray-900">{{ $visit->prescription }}</p>
                        </div>
                        @endif
                    </div>

                    @if(auth()->user()->isDoctor() && auth()->user()->doctor->id === $visit->appointment->doctor_id)
                    <div class="mt-6">
                        <a href="{{ route('visits.edit', $visit) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Visit
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Payment Information</h3>

                    @if($visit->payment)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Amount</p>
                            <p class="text-base text-gray-900">${{ number_format($visit->payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Payment Method</p>
                            <p class="text-base text-gray-900">{{ ucfirst($visit->payment->payment_method) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($visit->payment->status === 'paid') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($visit->payment->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('payments.show', $visit->payment) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            View Full Payment Details →
                        </a>
                    </div>
                    @else
                    <p class="text-gray-500">No payment record created yet.</p>
                    @if(auth()->user()->isAdmin() || auth()->user()->isReceptionist())
                    <div class="mt-4">
                        <a href="{{ route('payments.create', ['visit_id' => $visit->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Create Payment Record
                        </a>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>