<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ReportedContent;
use App\Models\Recipe;

class AdminController extends Controller
{


    // Admin dashboard for content moderation and user management
    public function dashboard()
    {
        // Provide an overview of key metrics or information for the admin dashboard
        return response()->json(['message' => 'Admin dashboard']);
    }

    // User Management

    // List all users
    public function listUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    // View user details
    public function viewUser(User $user)
    {
        return response()->json(['user' => $user]);
    }

    // Block a user
    public function blockUser(User $user)
    {
        $user->update(['blocked' => true]);
        return response()->json(['message' => 'User blocked successfully']);
    }

    // Unblock a user
    public function unblockUser(User $user)
    {
        $user->update(['blocked' => false]);
        return response()->json(['message' => 'User unblocked successfully']);
    }

    // Delete a user
    public function deleteUser(User $user)
    {
        // Soft delete the user
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // Content Moderation

    // Delete content
    public function deleteContent(Recipe $recipe)
    {
        // Soft delete the content
        $recipe->delete();
        return response()->json(['message' => 'Content deleted successfully']);
    }
}
