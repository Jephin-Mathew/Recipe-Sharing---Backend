<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Rating;
use App\Models\Activities;

class UserController extends Controller
{
    // Show user profile
    public function show(User $user)
    {
        $recipes = $user->recipes;
        $followers = $user->followers;
        $following = $user->following;

        return response()->json(compact('user', 'recipes', 'followers', 'following'));
    }

    // Update user profile
    public function update(User $user)
    {
        $attributes = request()->validate([
            'bio' => 'nullable|string',
        ]);

        $user->update($attributes);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    // Follow user
    public function follow(User $user)
    {
        auth()->user()->following()->attach($user);

        return response()->json(['message' => 'You are now following ' . $user->name]);
    }

    // Unfollow user
    public function unfollow(User $user)
    {
        auth()->user()->following()->detach($user);

        return response()->json(['message' => 'You have unfollowed ' . $user->name]);
    }

    // Display user's uploaded recipes
    public function recipes(User $user)
    {
        $recipes = $user->recipes;

        return response()->json(compact('recipes'));
    }
    
    // Update user to admin
    public function updateRole(User $user, Request $request)
   {
    $request->validate([
        'is_admin' => 'required|boolean',
    ]);

    $user->update(['is_admin' => $request->input('is_admin')]);

    return response()->json(['message' => 'User role updated successfully']);
    }

    // Handle likes (Assuming you have a likes table)
    public function likeRecipe(User $user, Recipe $recipe)
    {
        $user->likes()->attach($recipe);
    
        return response()->json(['message' => 'You liked the recipe']);
    }

    // Handle rating (Assuming you have a ratings table)
    public function rateRecipe(User $user, Recipe $recipe, $rating)
    {
        Rating::updateOrCreate(
            ['user_id' => $user->id, 'recipe_id' => $recipe->id],
            ['rating' => $rating]
        );

        return response()->json(['message' => 'Recipe rated successfully']);
    }

    //Activity
    public function activityFeed(Request $request)
{
    $user = auth()->user();
    $following = $user->following()->pluck('users.id'); // Specify the table name for the id column

    // Set the page number based on the request or default to 1
    $page = $request->input('page', 1);

    // Set the number of items to retrieve per page
    $perPage = 10;

    // Retrieve paginated activities from users the authenticated user is following
    $activities = Activities::whereIn('user_id', $following)->with('recipe','recipe.images')
        ->latest()
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json(['activities' => $activities]);
}
}
