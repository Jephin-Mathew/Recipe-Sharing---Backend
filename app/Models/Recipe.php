<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'title', 'ingredients', 'steps',
        'cooking_hours', 'cooking_minutes', 'difficulty_level', 'image',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function images()
    {
        return $this->hasMany(RecipeImage::class);
    }
}
