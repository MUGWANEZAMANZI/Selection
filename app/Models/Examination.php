<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Training;

class Examination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'json_file_path',
        'category',
        'time_limit',
        'passing_score',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope a query to only include active examinations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the questions from the JSON file.
     *
     * @return array
     */
    public function getQuestions()
    {
        if (empty($this->json_file_path)) {
            return [];
        }

        $path = database_path($this->json_file_path);

        if (!file_exists($path)) {
            return [];
        }

        $jsonContent = file_get_contents($path);
        $data = json_decode($jsonContent, true);

        return $data['questions'] ?? [];
    }

    public function training()
    {
        return $this->belongsToMany(Training::class);
    }
}
