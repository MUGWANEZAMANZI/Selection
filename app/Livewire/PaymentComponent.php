<?php

namespace App\Livewire;

use App\Models\User;
//use Auth;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Paypack\Paypack;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Training;
use App\Models\Enrollment;
use App\Models\Transaction;

class PaymentComponent extends Component
{
    public $selectedTraining = null;
    public $amount = 0;
    public $phone = '0787652137';
    public $result = null;
    public $message = '';
    public $messageType = '';
    public $transaction;
    public $trainings;
    public $description;
    public $duration;
    public $hasPaid = false;

    protected $paypack;

    public function mount()
    {
        $this->trainings = Training::all();
        // Remove the default training selection
        // Let user explicitly select a training
        $this->amount = 0;
    }

    public function updatedSelectedTraining($value)
    {
        $this->amount = 0;
        $this->description = null;
        $this->duration = null;
        $this->message = null;

        if ($value) {
            $training = $this->trainings->find($value);
            if ($training) {
                $this->amount = $training->fees;
                $this->description = $training->description;
                $this->duration = $training->duration;
                $this->message = "Selected: {$training->name} - {$training->fees} RWF";
                $this->messageType = 'success';
            }
        }
    }

    private function initializePaypack()
    {
        try {
            $clientId = env('PAYPACK_CLIENT_ID');
            $clientSecret = env('PAYPACK_CLIENT_SECRET');

            if (empty($clientId) || empty($clientSecret)) {
                throw new Exception('Paypack credentials are not configured in .env file');
            }

            $this->paypack = new Paypack();
            $this->paypack->config([
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            Log::info('Paypack initialized successfully');
        } catch (Exception $e) {
            $this->message = 'Configuration error: ' . $e->getMessage();
            $this->messageType = 'error';
            $this->paypack = null;
            Log::error('Paypack configuration error', ['error' => $e->getMessage()]);
        }
    }

    private function ensurePaypackInitialized()
    {
        if ($this->paypack === null) {
            $this->initializePaypack();
        }

        if ($this->paypack === null) {
            throw new Exception('Paypack is not properly configured. Please check your credentials.');
        }
    }

    public function cashIn()
    {
        if (!$this->selectedTraining) {
            $this->message = 'Please select a training course first';
            $this->messageType = 'error';
            return;
        }

        if (!$this->amount || $this->amount <= 0) {
            $this->message = 'Invalid amount. Please ensure a valid training course is selected';
            $this->messageType = 'error';
            return;
        }

        try {
            $this->ensurePaypackInitialized();

            $cashin = $this->paypack->Cashin([
                'phone' => $this->phone,
                'amount' => $this->amount,
            ]);

            Log::info('Cashin request', [
                'phone' => $this->phone,
                'amount' => $this->amount,
                'training_id' => $this->selectedTraining,
            ]);

            $this->result = $cashin;
            $this->message = 'Payment request submitted successfully!';
            $this->messageType = 'success';

            Transaction::create([
                'transaction_id' => $this->result['ref'],
                'phone' => $this->phone,
                'amount' => $this->result['amount'],
                'status' => $this->result['status'],
                'training_id' => $this->selectedTraining,
                'user_id' => Auth::user()->id,
                'student_id' => Auth::user()->id,
            ]);

            if($this->result['status'] === 'success'){
                $this->hasPaid = true;
            }


        } catch (Exception $e) {
            $this->message = 'Payment failed: ' . $e->getMessage();
            $this->messageType = 'error';
            Log::error('Cashin error', ['error' => $e->getMessage()]);
        }
    }

    public function updatedTransaction($value){
        $transactionStatus = $this->paypack->Transaction($value);
        if($transactionStatus['status'] === 'successful'){
            Transaction::where('transaction_id', $value)->update(['status' => 'successful']);

            // Set hasPaid to true to trigger enrollment creation
            $this->hasPaid = true;

            $this->message = 'Congratulations! You have successfully paid for the training course';
            $this->messageType = 'success';
        }
    }






    public function cashOut()
    {
        if (!$this->amount || $this->amount <= 0) {
            $this->message = 'Please enter a valid amount';
            $this->messageType = 'error';
            return;
        }

        try {
            $this->ensurePaypackInitialized();

            $cashout = $this->paypack->Cashout([
                'phone' => $this->phone,
                'amount' => $this->amount,
            ]);

            $this->result = $cashout;
            $this->message = 'Cash Out request submitted successfully!';
            $this->messageType = 'success';
        } catch (Exception $e) {
            $this->message = 'Cash Out failed: ' . $e->getMessage();
            $this->messageType = 'error';
            Log::error('Cashout error', ['error' => $e->getMessage()]);
        }
    }

    public function transactions()
    {
        try {
            $this->ensurePaypackInitialized();

            $transactions = $this->paypack->Transactions([
                'offset' => '0',
                'limit' => '100',
            ]);

            $this->result = $transactions;
            $this->message = 'Transactions loaded successfully!';
            $this->messageType = 'success';
        } catch (Exception $e) {
            $this->message = 'Failed to load transactions: ' . $e->getMessage();
            $this->messageType = 'error';
            Log::error('Transactions error', ['error' => $e->getMessage()]);
        }
    }


    public function updatedHasPaid(bool $value): void
    {
        if($value === true){
            // Check if a training is selected
            if(!$this->selectedTraining) {
                $this->message = "No training course selected. Please select a course first.";
                $this->messageType = 'error';
                return;
            }

            Enrollment::create([
                'student_id' => Auth::user()->id,
                'user_id' => Auth::user()->id,
                'training_id' => $this->selectedTraining,
                'has_paid' => $this->hasPaid,
                'enrolled_at' => now(),
                'notes' => "Successfully registered",
            ]);

            $this->message = "Payment confirmed and enrollment completed successfully!";
            $this->messageType = 'success';
        }
    }

    public function render()
    {
        return view('livewire.payment-component');
    }
}
