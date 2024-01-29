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
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:100000',
            'recipe_id' => 'required|exists:recipes,id', // Ensure recipe_id exists in the recipes table
        ]);

        // Store the file
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename,'public'); 

        // Save the file information to the database
        $recipeImage = RecipeImage::create([
            'recipe_id' => $request->input('recipe_id'),
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
