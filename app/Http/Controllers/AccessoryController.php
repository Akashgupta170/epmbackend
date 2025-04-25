<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessoryController extends Controller
{
    public function addaccessorycategory(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
            ]);

            // Create category
            $category = Category::create([
                'name' => $validated['name'],
            ]);

            return response()->json([
                'message' => 'Category added successfully!',
                'data' => $category
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Optional: log error for debugging
            Log::error('Failed to add category', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Something went wrong. Could not add category.',
                'error' => $e->getMessage(), // Remove in production if needed
            ], 500);
        }
    }

    public function getaccessorycategory()
    {
        try {
            $category = Category::orderBy('id','DESC')->get();

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found.'
                ], 404);
            }

            return response()->json([
                'message' => 'Category fetched successfully.',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editaccessorycategory($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found.',
                ], 404);
            }

            // Return response if found
            return response()->json([
                'message' => 'Category data retrieved successfully!',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Failed to retrieve category', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Something went wrong. Could not retrieve category.',
                'error' => $e->getMessage(), // optional
            ], 500);
        }
    }

    public function updateaccessorycategory(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            // Find the category
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found.'
                ], 404);
            }

            // Update the category
            $category->name = $validated['name'];
            $category->save();

            return response()->json([
                'message' => 'Category updated successfully.',
                'data' => $category
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteaccessorycategory($id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found.'
                ], 404);
            }

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong while deleting the category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
