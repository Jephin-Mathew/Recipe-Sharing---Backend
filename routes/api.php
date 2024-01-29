<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [UserController::class, 'listUsers']); 

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/activity-feed', [UserController::class, 'activityFeed']);
    Route::post('/upload', [FileController::class, 'upload']);

 // User profile routes
 Route::prefix('/user')->group(function () {
    Route::get('/{user}', [UserController::class, 'show']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::post('/{user}/follow', [UserController::class, 'follow']);
    Route::post('/{user}/unfollow', [UserController::class, 'unfollow']);
    Route::get('/{user}/recipe', [UserController::class, 'recipe']);
    Route::post('/{user}/like/{recipe}', [UserController::class, 'likeRecipe']);
    Route::post('/{user}/rate/{recipe}/{rating}', [UserController::class, 'rateRecipe']);
});
    // Recipe routes
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);
    Route::post('/recipes/{recipe}/like', [RecipeController::class, 'like']);
    Route::post('/recipes/{recipe}/unlike', [RecipeController::class, 'unlike']);
    Route::post('/recipes/{recipe}/tags', [RecipeController::class, 'addTags']);

 // Admin routes (protected by admin middleware)
Route::middleware(['auth:api', 'admin'])->group(function () {
    // Admin dashboard and user management 
    Route::put('/admin/users/{user}/update-role', [UserController::class, 'updateRole']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'listUsers']);
    Route::get('/admin/users/{user}', [AdminController::class, 'viewUser']);
    Route::post('/admin/users/{user}/block', [AdminController::class, 'blockUser']);
    Route::post('/admin/users/{user}/unblock', [AdminController::class, 'unblockUser']);
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);

    // Content moderation
    Route::delete('/admin/content/{recipe}', [AdminController::class, 'deleteContent']);
});
});
