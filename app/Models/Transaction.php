<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'phone',
        'amount',
        'status',
        'training_id',
        'user_id',
        'transaction_time'
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
