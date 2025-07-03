<div class="p-4 m-4">
    <h2 class="pt-4 pl-4 text-xl font-semibold text-gray-700 mb-4">My Purchased Courses</h2>
    
    @if(session()->has('error'))
        <div class="mx-4 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('success'))
        <div class="mx-4 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($trainings->isEmpty())
        <div class="mx-4 p-8 text-center bg-gray-50 rounded-lg">
            <div class="text-gray-500 text-lg mb-4">No purchased courses yet</div>
            <p class="text-gray-400 mb-6">Start your learning journey by purchasing a course</p>
            <a href="{{ url('dashboard/payments') }}" 
               class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Browse Courses
            </a>
        </div>
    @else
        <div class="flex w-full space-x-6 overflow-x-auto px-4">
            @foreach($trainings as $training)
                <div class="relative group w-96 h-72 flex-shrink-0 bg-white rounded-lg shadow-md p-8 transition duration-200 hover:shadow-lg">
                    <div class="h-full flex flex-col">
                        <h3 class="font-semibold text-gray-800 text-lg mb-3">{{ $training->name }}</h3>
                        <p class="text-base text-gray-600 mb-2">Duration: {{ $training->duration }}</p>
                        <p class="text-base text-gray-600 mb-2">Class: {{ $training->class }}</p>
                        <p class="text-base text-gray-600 mb-4">Paid: {{ number_format($training->fees) }} RWF</p>
                        
                        <div class="mt-auto">
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                ✓ Purchased
                            </span>
                        </div>
                    </div>

                    <!-- Hidden details on hover -->
                    <div class="absolute inset-0 bg-white bg-opacity-95 rounded-lg p-8 flex flex-col justify-center items-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                        <h3 class="font-semibold text-gray-800 mb-3 text-lg text-center">{{ $training->name }}</h3>
                        <p class="text-base text-gray-600 mb-4 text-center">{{ $training->description }}</p>
                        
                        <div class="flex flex-col space-y-2 w-full">
                            <a href="{{ route('dashboard.exams', ['examId' => $training->id]) }}"
                                    class="w-full px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                Take Exam
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</div>