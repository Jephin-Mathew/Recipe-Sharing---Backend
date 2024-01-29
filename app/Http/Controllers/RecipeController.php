<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeImage;
use App\Models\Tag;
use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class RecipeController extends Controller
{
    // Display a list of recipes
    public function index()
    {
        $recipes = Recipe::with('image')->with(['likes'=>function($query){
            $query->where('users.id',\Auth::user()->id);
        }])->get();

        return response()->json(compact('recipes'));
    }

    // Show a specific recipe
    public function show(Recipe $recipe)
    {
        $recipe = Recipe::with('image')->find($recipe->id);

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
            'difficulty_level' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:100000',
        ]);

        $attributes['user_id'] = auth()->user()->id;

        $recipe = Recipe::create($attributes);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('uploads', $filename, 'public');
            $recipe->images()->create([
                'filename' => $imagePath,
            ]);
        }
    

        $activity = Activities::create([
            'user_id' => auth()->user()->id,
            'recipe_id' => $recipe->id,
        ]);

        return response()->json(['message' => 'Recipe created successfully', 'recipe' => $recipe]);
    }
    

    // Edit and update a recipe
    public function update(Request $request, Recipe $recipe)
    {
        $attributes = $request->validate([
            'title' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',required|
            'cooking_hours' => 'integer',
            'cooking_minutes' => 'integer',
            'difficulty_level' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:100000',
        ]);
        

        $attributes['user_id'] = auth()->user()->id;

        $recipe->update($attributes);

        // Check if an image is provided
        if ($request->has('image')) {
            // Update or create the associated image
            $recipeImage = RecipeImage::updateOrCreate(
                ['recipe_id' => $recipe->id],
                ['filename' => $request->input('image')]
            );
        }

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
