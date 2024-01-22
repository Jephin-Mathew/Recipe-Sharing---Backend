<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = '_tags';

    protected $fillable = [
        'name',
    ];

    // Relationships
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tags');
    }
}
