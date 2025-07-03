<div class="container mx-auto px-2 py-2">
    <div class="container mx-auto p-4 text-center" >
        <a href="{{route('dashboard')}}"><x-auth-card class="rounded-lg shadow-xl w-32 h-auto mx-auto hover:opacity-90 transition-opacity duration-300"/></a>
        <h1 class="text-2xl font-bold mb-4">Pay through our Secure Gateway</h1>
        <span class="text-sm text-gray-600">Pay to Trusterlabs</span>
        </div>

    @if($message)
        <div class="max-w-md mx-auto mb-4">
            <div class="p-4 rounded-md {{ $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' }}">
                {{ $message }}
            </div>
        </div>
    @endif

    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <!-- Training Selection -->
        <div class="mb-6">
            <label for="training" class="block text-sm font-medium text-gray-700 mb-2">Select Training Course</label>
            <select wire:model.live="selectedTraining"
                   id="training"
                   class="block rounded-md border-gray-300 shadow-sm w-full h-12 focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">-- Select a Course --</option>
                @foreach($trainings as $training)
                    <option value="{{ $training->id }}">
                        {{ $training->name }} - {{ number_format($training->fees) }} RWF ({{ $training->duration }}) - Class {{ $training->class }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Amount Display (Read-only) -->
        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Course Fee</label>
            <div class="block rounded-md border-gray-300 bg-gray-100 shadow-sm w-full h-12 flex items-center px-3 text-lg font-semibold text-gray-800">
                {{ number_format($amount) }} RWF
            </div>
            <p class="text-xs text-gray-500 mt-1">Amount is automatically set based on selected course</p>
        </div>

        <!-- Course Details -->
        @if($selectedTraining)
            @php
                $selectedCourse = $trainings->find($selectedTraining);
            @endphp
            @if($selectedCourse)
                <div class="mb-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                    <h3 class="font-semibold text-blue-800">{{ $selectedCourse->name }}</h3>
                    <p class="text-sm text-blue-600">Duration: {{ $selectedCourse->duration }}</p>
                    <p class="text-sm text-blue-600">Class: {{ $selectedCourse->class }}</p>
                    @if($selectedCourse->description)
                        <p class="text-sm text-blue-600 mt-1">{{ $selectedCourse->description }}</p>
                    @endif
                </div>
            @endif
        @endif

        <!-- Phone Number -->
        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
            <input type="text"
                   wire:model="phone"
                   id="phone"
                   class="block rounded-md border-gray-300 shadow-sm w-full h-12 focus:border-blue-500 focus:ring-blue-500 px-3"
                   placeholder="Enter phone number">
        </div>

        <h2 class="text-xl font-semibold text-gray-700 mb-4">Payment Options</h2>

        <div class="space-y-4">
            <button wire:click="cashIn"
                    wire:loading.attr="disabled"
                    @if(!$selectedTraining || $amount <= 0) disabled @endif
                    class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded transition duration-200">
                <span wire:loading.remove wire:target="cashIn">Pay for Course</span>
                <span wire:loading wire:target="cashIn">Processing Payment...</span>
            </button>

{{--             <button wire:click="cashOut"--}}
{{--                    wire:loading.attr="disabled"--}}
{{--                    class="w-full bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white font-medium py-3 px-4 rounded transition duration-200">--}}
{{--                <span wire:loading.remove wire:target="cashOut">Cash Out</span>--}}
{{--                <span wire:loading wire:target="cashOut">Processing...</span>--}}
{{--            </button>--}}
{{--            --}}
{{--            <button wire:click="transactions"--}}
{{--                    wire:loading.attr="disabled"--}}
{{--                    class="w-full bg-purple-500 hover:bg-purple-600 disabled:bg-purple-300 text-white font-medium py-3 px-4 rounded transition duration-200">--}}
{{--                <span wire:loading.remove wire:target="transactions">View Transactions</span>--}}
{{--                <span wire:loading wire:target="transactions">Loading...</span>--}}
{{--            </button> --}}
        </div>
    </div>

    @if($result)
        <div class="mt-8 max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Payment Result</h2>
            <div class="bg-gray-100 p-4 rounded-md">
                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($result) }}</pre>
            </div>
        </div>
    @endif
</div>
