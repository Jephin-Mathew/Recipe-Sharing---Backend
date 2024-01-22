<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeImage;

class FileController extends Controller
{
    /**
     * Handle file upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the validation rules based on your needs
        ]);

        // Store the file
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename); // Assumes you have 'public/uploads' as your storage path
        // Save the file information to the database
        $recipeImage = RecipeImage::create([
            'recipe_id' => $request->input('recipe_id'), // Assuming you have recipe_id in your request
            'filename' => $filename,
        ]);

        // Return a JSON response with success message and file details
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'recipe_image' => $recipeImage,
            ],
        ], 201);
    }
}
