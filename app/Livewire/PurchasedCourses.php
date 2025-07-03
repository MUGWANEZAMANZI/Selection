<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use App\Models\Transaction;
use App\Models\Examination;
use Illuminate\Support\Facades\Auth;

class PurchasedCourses extends Component
{
    public $purchasedTrainings = [];

    public function mount()
    {
        $this->loadPurchasedTrainings();
    }

    public function loadPurchasedTrainings()
    {
        // Get training IDs from successful transactions for the authenticated user
        $successfulTransactionTrainingIds = Transaction::where('status', 'successful')
            ->where('user_id', Auth::id())
            ->pluck('training_id')
            ->toArray();

        // Get the actual training records
        $this->purchasedTrainings = Training::whereIn('id', $successfulTransactionTrainingIds)->get();
    }

    public function launchExam($trainingId)
    {
        // Check if user has purchased this training
        $hasPurchased = Transaction::where('status', 'successful')
            ->where('user_id', Auth::id())
            ->where('training_id', $trainingId)
            ->exists();

        if (!$hasPurchased) {
            session()->flash('error', 'You have not purchased this course.');
            return;
        }

        // Find the corresponding examination
        $examination = Examination::where('training_id', $trainingId)
            ->where('is_active', true)
            ->first();

        if (!$examination) {
            session()->flash('error', 'No exam available for this course yet.');
            return;
        }

        // Redirect to exam page
        return redirect()->route('dashboard.exams', ['examId' => $examination->id]);
    }

    public function render()
    {
        return view('livewire.purchased-courses', [
            'trainings' => $this->purchasedTrainings,
        ]);
    }
}
