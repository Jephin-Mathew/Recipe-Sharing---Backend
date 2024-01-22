<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeImage extends Model
{
    protected $fillable = [
        'recipe_id', 'filename',
    ];

    // Relationships
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}