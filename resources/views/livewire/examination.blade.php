<div class="p-6" wire:poll.1s="startTimer">
    {{-- Show error messages --}}
    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <h3 class="font-semibold text-red-800">Error</h3>
            <p class="text-red-600">{{ $errorMessage }}</p>
        </div>
    @endif

    @if($exam)
        @if(!$examStarted)
            {{-- Exam Details View --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $exam->name }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-700">Duration:</h3>
                        <p class="text-gray-600">{{ $exam->time_limit }} minutes</p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700">Class:</h3>
                        <p class="text-gray-600">{{ $exam->class }}</p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700">Fees:</h3>
                        <p class="text-gray-600">{{ number_format($exam->fees) }} RWF</p>
                    </div>
                </div>
                
                @if($exam->description)
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Description:</h3>
                        <p class="text-gray-600">{{ $exam->description }}</p>
                    </div>
                @endif
                
                <div class="flex space-x-4">
                    <form wire:submit.prevent="startExam">
                        <input type="hidden" 
                        wire:model.live="selectedId"
                        name="examId" value="{{ $exam->id }}">
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            Start Exam
                        </button>
                    </form>
                
                    <a href="{{ url('dashboard/payments') }}" 
                       class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        Back to Courses
                    </a>
                </div>
            </div>
        @else
            {{-- Exam Questions View --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $exam->name }}</h1>
                        <p class="text-gray-600">Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</p>
                    </div>
                    
                    {{-- Timer in top right corner --}}
                    <div class="text-right">
                        <div class="bg-{{ $timeRemaining <= 300 ? 'red' : 'blue' }}-100 border border-{{ $timeRemaining <= 300 ? 'red' : 'blue' }}-200 rounded-lg px-4 py-2">
                            <h3 class="font-semibold text-{{ $timeRemaining <= 300 ? 'red' : 'blue' }}-800">Time Remaining</h3>
                            <p class="text-2xl font-bold text-{{ $timeRemaining <= 300 ? 'red' : 'blue' }}-900">{{ $this->formattedTime }}</p>
                        </div>
                    </div>
                </div>

                @if($currentQuestion)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            {{ $currentQuestion['question'] ?? 'Question not found' }}
                        </h3>

                        {{-- Answer Input --}}
                        <form wire:submit.prevent="submitAnswer" class="mb-4">
                            <div class="space-y-3">
                                <input type="text" 
                                       wire:model="currentAnswer"
                                       placeholder="Enter your answer here and press Enter..."
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $isInputDisabled ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                       {{ $isInputDisabled ? 'disabled' : '' }}
                                       wire:keydown.enter="submitAnswer">
                            </div>
                        </form>

                        {{-- Show Result ONLY when showResult is true --}}
                        @if($showResult === true)
                            <div class="mb-4 p-4 rounded-lg {{ $isAnswerCorrect ? 'bg-green-100 border border-green-200' : 'bg-red-100 border border-red-200' }}">
                                @if($isAnswerCorrect === true)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-green-800 font-semibold">Correct!</span>
                                    </div>
                                @elseif($isAnswerCorrect === false)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-red-800 font-semibold">Incorrect</span>
                                    </div>
                                    @if($isInputDisabled)
                                        <p class="text-red-700 mt-2 font-semibold">Please wait {{ $disableTimer }} seconds before you can try again.</p>
                                        <div wire:poll.1s="startDisableTimer"></div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Show Submit Exam button only on last question --}}
                    @if($currentQuestionIndex === count($questions) - 1 && $isAnswerCorrect === true)
                        <div class="text-center">
                            <button wire:click="finishExam" 
                                    class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                Submit Exam
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">No questions available for this exam.</p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-red-800 mb-2">Exam Not Found</h2>
            <p class="text-red-600 mb-4">The requested exam could not be found or you don't have access to it.</p>
            <a href="{{ url('dashboard') }}" 
               class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Back to Courses
            </a>
        </div>
    @endif
</div>


