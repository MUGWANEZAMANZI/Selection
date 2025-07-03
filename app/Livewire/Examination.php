<?php


namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use App\Models\Examination as ExaminationModel;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Examination extends Component
{
    public $exam;
    public $training;
    public $examId;
    public $selectedId;
    public $hasAccess = false;
    public $errorMessage = '';
    public $examStarted = false;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $currentQuestion = null;
    public $userAnswers = [];
    public $currentAnswer = '';
    public $isAnswerCorrect = null;
    public $showResult = false;
    public $isInputDisabled = false;
    public $disableTimer = 0;
    public $examTimeLimit = 0; // in minutes
    public $timeRemaining = 0; // in seconds
    public $timerActive = false;

    public function mount($examId = null)
    {
        if ($examId) {
            $this->examId = $examId;
            $this->selectedId = $examId;
            
            // Get the exam from examinations table
            $this->exam = ExaminationModel::find($examId);
            
            if ($this->exam) {
                $this->examTimeLimit = $this->exam->time_limit ?? 60; // Default to 60 minutes if not set
                
                // Get the associated training for access check
                $this->training = Training::find($this->exam->training_id);
            }
            
            // Temporarily set hasAccess to true for debugging
            $this->hasAccess = true;
            
            // Check if user has purchased this training (commented out for debugging)
            // $this->hasAccess = $this->checkUserAccess();
            
            // if (!$this->hasAccess) {
            //     $this->errorMessage = 'You need to purchase this course to access the exam.';
            // }
        } else {
            $this->errorMessage = 'No exam ID provided.';
        }
    }

    private function checkUserAccess()
    {
        if (!$this->training) {
            return false;
        }

        return Transaction::where('status', 'successful')
            ->where('user_id', Auth::id())
            ->where('training_id', $this->training->id)
            ->exists();
    }

    public function startExam()
    {
        // Add detailed debugging
        \Log::info('=== START EXAM DEBUG ===');
        \Log::info('Exam object: ' . ($this->exam ? 'exists' : 'null'));
        
        if ($this->exam) {
            \Log::info('Exam ID: ' . $this->exam->id);
            \Log::info('Exam name: ' . $this->exam->name);
            \Log::info('JSON file path: ' . ($this->exam->json_file_path ?? 'NULL'));
            \Log::info('Time limit: ' . ($this->exam->time_limit ?? 'NULL'));
            \Log::info('Exam attributes: ' . json_encode($this->exam->getAttributes()));
        }
        
        // Check if exam exists and has json_file_path
        if (!$this->exam) {
            $this->errorMessage = 'Exam not found in database.';
            \Log::error('Exam is null');
            return;
        }
        
        if (!$this->exam->json_file_path) {
            $this->errorMessage = 'Exam does not have a JSON file path configured.';
            \Log::error('JSON file path is null or empty');
            return;
        }
        
        // Load questions from JSON file
        $jsonPath = base_path('database/' . $this->exam->json_file_path);
        
        \Log::info('Full JSON path: ' . $jsonPath);
        \Log::info('File exists: ' . (file_exists($jsonPath) ? 'Yes' : 'No'));
        
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $decodedJson = json_decode($jsonContent, true);
            
            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errorMessage = 'Invalid JSON format: ' . json_last_error_msg();
                \Log::error('JSON decode error: ' . json_last_error_msg());
                return;
            }
            
            // Extract questions from the JSON structure
            if (isset($decodedJson['questions']) && is_array($decodedJson['questions'])) {
                $this->questions = $decodedJson['questions'];
                \Log::info('Questions loaded: ' . count($this->questions));
            } else {
                $this->errorMessage = 'No questions array found in the JSON file.';
                \Log::error('No questions array found');
                return;
            }
            
            if (!empty($this->questions) && is_array($this->questions)) {
                $this->examStarted = true;
                $this->currentQuestionIndex = 0;
                $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
                $this->currentAnswer = '';
                $this->userAnswers = [];
                $this->resetQuestionState();
                
                // Start the exam timer
                $this->timeRemaining = $this->examTimeLimit * 60; // Convert minutes to seconds
                $this->timerActive = true;
                
                \Log::info('Exam started successfully');
            } else {
                $this->errorMessage = 'No questions found in the exam file.';
                \Log::error('No questions found');
            }
        } else {
            $this->errorMessage = 'Exam file not found: ' . $jsonPath;
            \Log::error('Exam file not found: ' . $jsonPath);
        }
    }

    public function startTimer()
    {
        // This will be called every second via Livewire polling
        if ($this->timerActive && $this->timeRemaining > 0) {
            $this->timeRemaining--;
            
            if ($this->timeRemaining <= 0) {
                $this->timerActive = false;
                $this->finishExam();
            }
        }
    }

    public function submitAnswer()
    {
        if ($this->currentQuestion && !$this->isInputDisabled) {
            $correctAnswer = $this->currentQuestion['correct_answer'];
            $userAnswer = trim($this->currentAnswer);
            
            // Store the user's answer
            $this->userAnswers[$this->currentQuestionIndex] = $userAnswer;
            
            // Check if answer is correct (case-insensitive)
            $this->isAnswerCorrect = strcasecmp($userAnswer, $correctAnswer) === 0;
            $this->showResult = true;
            
            // Clear the input field immediately after submission
            $this->currentAnswer = '';
            
            if ($this->isAnswerCorrect) {
                // Correct answer - immediately reset and move to next question
                $this->resetQuestionState();
                $this->moveToNextQuestion();
            } else {
                // Wrong answer - disable input for 5 seconds
                $this->isInputDisabled = true;
                $this->disableTimer = 5;
            }
        }
    }

    public function startDisableTimer()
    {
        // This will be called every second to countdown the disable timer
        if ($this->isInputDisabled && $this->disableTimer > 0) {
            $this->disableTimer--;
            
            if ($this->disableTimer <= 0) {
                $this->enableInput();
            }
        }
    }

    public function enableInput()
    {
        $this->isInputDisabled = false;
        $this->disableTimer = 0;
    }

    public function moveToNextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            // Move to next question
            $this->currentQuestionIndex++;
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
            
            // Ensure state is completely clean for new question
            $this->resetQuestionState();
        }
    }

    public function finishExam()
    {
        $this->timerActive = false;
        // Handle exam completion logic here
        session()->flash('message', 'Exam completed!');
        return redirect()->route('dashboard');
    }

    private function resetQuestionState()
    {
        $this->currentAnswer = '';
        $this->isAnswerCorrect = null;
        $this->showResult = false;
        $this->isInputDisabled = false;
        $this->disableTimer = 0;
    }

    public function getFormattedTimeProperty()
    {
        $minutes = floor($this->timeRemaining / 60);
        $seconds = $this->timeRemaining % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function render()
    {
        return view('livewire.examination')->layout('layouts.app');
    }
}