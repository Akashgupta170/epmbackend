<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AccessoryAssignController extends Controller
{
    public function addaccessory(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'accessory_no' => 'required|max:255',
                'model' => 'nullable|max:255',
                'condition' => 'nullable',
                'category_id' => 'required|exists:categories,id',
                'issue_date' => 'nullable',
                'note' => 'nullable',
                'status' => 'nullable',
            ]);

            // Step 1: Create accessory
            $accessory = Accessory::create([
                'accessory_no' => $validated['accessory_no'],
                'model' => $validated['model'],
                'condition' => $validated['condition'],
                'issue_date' => $validated['issue_date'],
                'note' => $validated['note'],
                'status' => $validated['status'],
                'category_id' => $validated['category_id'],
            ]);

            return response()->json([
                'message' => 'Accessory created and assigned successfully.',
                'data' => $accessory,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create accessory and assign.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getaccessory($id)
    {
        try {
            // Only get accessories that belong to the given category_id
            $accessories = Accessory::with('category')
                ->where('category_id', $id)
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'message' => 'Accessories retrieved successfully.',
                'data' => $accessories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch accessories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editaccessory($id)
    {
        try {
            $accessory = Accessory::with('category')->findOrFail($id);

            return response()->json([
                'message' => 'Accessory found.',
                'data' => $accessory
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Accessory not found.'
            ], 404);
        }
    }

    public function updateaccessory(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'accessory_no' => 'required|max:255',
                'model' => 'nullable|max:255',
                'condition' => 'nullable',
                'category_id' => 'required|exists:categories,id',
                'issue_date' => 'nullable|date',
                'note' => 'nullable',
                'status' => 'nullable',
            ]);

            $accessory = Accessory::findOrFail($id);
            $accessory->update($validated);

            return response()->json([
                'message' => 'Accessory updated successfully.',
                'data' => $accessory
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update accessory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteaccessory($id)
    {
        try {
            $accessory = Accessory::findOrFail($id);
            $accessory->delete();

            return response()->json([
                'message' => 'Accessory deleted successfully.',
                'data' => $accessory
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Accessory not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete accessory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
