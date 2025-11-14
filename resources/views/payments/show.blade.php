<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-lg font-semibold">Payment Information</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            @if($payment->status === 'paid') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Patient</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->appointment->patient->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $payment->visit->appointment->patient->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Doctor</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->appointment->doctor->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $payment->visit->appointment->doctor->specialization }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Visit Date</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->visit_date->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Payment Date</p>
                            <p class="text-base text-gray-900">{{ $payment->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Amount</p>
                                <p class="text-2xl font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Payment Method</p>
                                <p class="text-base text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Invoice Number</p>
                                <p class="text-base text-gray-900">#INV-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isReceptionist())
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('payments.edit', $payment) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Payment
                        </a>
                        
                        @if($payment->status === 'unpaid')
                        <form action="{{ route('payments.mark-paid', $payment) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Mark as Paid
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Visit Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Related Visit Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Diagnosis</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->diagnosis }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Treatment</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->treatment }}</p>
                        </div>
                        @if($payment->visit->prescription)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Prescription</p>
                            <p class="text-base text-gray-900">{{ $payment->visit->prescription }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('visits.show', $payment->visit) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            View Full Visit Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>