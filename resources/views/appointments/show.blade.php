<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-lg font-semibold">Appointment Information</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($appointment->status === 'approved') bg-green-100 text-green-800
                            @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Patient</p>
                            <p class="text-base text-gray-900">{{ $appointment->patient->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->patient->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Doctor</p>
                            <p class="text-base text-gray-900">{{ $appointment->doctor->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->doctor->specialization }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Appointment Date & Time</p>
                            <p class="text-base text-gray-900">{{ $appointment->appointment_date->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Room Number</p>
                            <p class="text-base text-gray-900">{{ $appointment->doctor->room_number ?? 'N/A' }}</p>
                        </div>
                        @if($appointment->notes)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Notes</p>
                            <p class="text-base text-gray-900">{{ $appointment->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($appointment->status !== 'completed' && $appointment->status !== 'canceled')
                            @if(auth()->user()->isAdmin() || auth()->user()->isReceptionist() || auth()->user()->isDoctor())
                                <a href="{{ route('appointments.edit', $appointment) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Edit Appointment
                                </a>
                            @endif

                            @if($appointment->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isReceptionist() || auth()->user()->isDoctor()))
                                <form action="{{ route('appointments.approve', $appointment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Approve Appointment
                                    </button>
                                </form>
                            @endif

                            @if($appointment->status === 'approved' && auth()->user()->isDoctor() && auth()->user()->doctor->id === $appointment->doctor_id)
                                <a href="{{ route('visits.create', ['appointment_id' => $appointment->id]) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Create Visit Record
                                </a>
                            @endif

                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel Appointment
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'completed' && !$appointment->visit && auth()->user()->isDoctor() && auth()->user()->doctor->id === $appointment->doctor_id)
                            <a href="{{ route('visits.create', ['appointment_id' => $appointment->id]) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Create Visit Record
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Visit Information (if exists) -->
            @if($appointment->visit)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Visit Information</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Visit Date</p>
                            <p class="text-base text-gray-900">{{ $appointment->visit->visit_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Diagnosis</p>
                            <p class="text-base text-gray-900">{{ $appointment->visit->diagnosis }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Treatment</p>
                            <p class="text-base text-gray-900">{{ $appointment->visit->treatment }}</p>
                        </div>
                        @if($appointment->visit->prescription)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Prescription</p>
                            <p class="text-base text-gray-900">{{ $appointment->visit->prescription }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('visits.show', $appointment->visit) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            View Full Visit Details →
                        </a>
                    </div>

                    <!-- Payment Information -->
                    @if($appointment->visit->payment)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold mb-3">Payment Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Amount</p>
                                <p class="text-base text-gray-900">${{ number_format($appointment->visit->payment->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                <p class="text-base text-gray-900">{{ ucfirst($appointment->visit->payment->payment_method) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($appointment->visit->payment->status === 'paid') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($appointment->visit->payment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('payments.show', $appointment->visit->payment) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                View Payment Details →
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>