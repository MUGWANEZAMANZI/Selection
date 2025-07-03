<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'address',
        'date_of_birth',
        'user_id',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean'
    ];

    // Get the student's full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Training model through enrollments
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'enrollments')
            ->withTimestamps()
            ->withPivot(['has_paid', 'enrolled_at', 'notes']);
    }

    // Relationship with enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Relationship with transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope for active students
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for inactive students
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
