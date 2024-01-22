<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Activities;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // Display a list of recipes
    public function index()
    {
        $recipes = Recipe::all();

        return response()->json(compact('recipes'));
    }

// Show a specific recipe
public function show(Recipe $recipe)
{
    if ($recipe) {
        return response()->json(compact('recipe'));
    } else {
        return response()->json(['message' => 'No recipe found'], 404);
    }
}

// Create and store a new recipe
public function store(Request $request)
{
    $attributes = $request->validate([
        'title' => 'required|string',
        'ingredients' => 'required|string',
        'steps' => 'required|string',
        'cooking_hours' => 'integer',
        'cooking_minutes' => 'integer',
        'difficulty_level' => 'integer',
        'image' => 'string', // Adjust the rule based on your requirements
        // Add other validation rules as needed
    ]);

    // Set the user_id
    $attributes['user_id'] = auth()->user()->id;

    // Create the recipe
    $recipe = Recipe::create($attributes);

    // Create an associated activity with the recipe_id explicitly set
    $activity = Activities::create([
        'user_id' => auth()->user()->id,
        'recipe_id' => $recipe->id, // Set the recipe_id explicitly
    ]);

    return response()->json(['message' => 'Recipe created successfully', 'recipe' => $recipe]);
}



    // Edit and update a recipe
    public function update(Request $request, Recipe $recipe)
    {
        $attributes = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'cooking_hours' => 'integer',
            'cooking_minutes' => 'integer',
            'difficulty_level' => 'integer',
            'image' => 'string', // Adjust the rule based on your requirements
            // Add other validation rules as needed
        ]);
        $attributes['user_id'] = auth()->user()->id;
        
        $recipe->update($attributes);
    
        return response()->json(['message' => 'Recipe updated successfully', 'recipe' => $recipe]);
    }
    // Delete a recipe
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }

    // Like a recipe
    public function like(Request $request, Recipe $recipe)
{
    $user = auth()->user();

    // Check if the user has already liked the recipe
    if (!$user->likes()->where('recipe_id', $recipe->id)->exists()) {
        // Attach the user to the recipe's likes
        $user->likes()->attach($recipe);

        return response()->json(['message' => 'Recipe liked successfully']);
    }

    return response()->json(['message' => 'Recipe already liked']);
}

    // Unlike a recipe
    public function unlike(Request $request, Recipe $recipe)
    {
        $user = auth()->user();
    
        // Check if the user has liked the recipe
        if ($user->likes()->where('recipe_id', $recipe->id)->exists()) {
            // Detach the user from the recipe's likes
            $user->likes()->detach($recipe);
    
            return response()->json(['message' => 'Recipe unliked successfully']);
        }
    
        return response()->json(['message' => 'Recipe not liked']);
    }

    // Add tags to a recipe
    public function addTags(Request $request, Recipe $recipe)
    {
        $attributes = $request->validate([
            'tags' => 'required|array',
        ]);

        // Assuming tags are strings; adjust as per your Tag model structure
        foreach ($attributes['tags'] as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $recipe->tags()->attach($tag);
        }

        return response()->json(['message' => 'Tags added to recipe successfully']);
    }
}
