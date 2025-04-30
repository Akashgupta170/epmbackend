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

    // accessory assign
    public function addaccessoryassign(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'accessory_id' => 'required|exists:accessories,id',
                'assigned_at' => 'nullable|date',
                'status' => 'nullable|in:assigned,vacant,in-repair,lost',
            ]);

            $accessoryAssign = new Assignment();
            $accessoryAssign->user_id = $validated['user_id'];
            $accessoryAssign->accessory_id = $validated['accessory_id'];
            $accessoryAssign->assigned_at = $validated['assigned_at'];
            $accessoryAssign->status = $validated['status'];
            $accessoryAssign->save();

            return response()->json(['message' => 'Accessory assigned successfully', 'data' => $accessoryAssign], 201);
        } catch (\Exception $e) {
            // Catch any exception that occurs during the process
            return response()->json(['message' => 'Failed to assign accessory', 'error' => $e->getMessage()], 500);
        }
    }

// Get all accessory assignments
    public function getaccessoryassign()
    {
        try {
            $assignments = Assignment::with(['user', 'accessory'])->get();
            return response()->json(['message' => 'all fetch successfully', 'data' => $assignments],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve accessory assignments', 'error' => $e->getMessage()], 500);
        }
    }

    // Get a specific accessory assignment
    public function editaccessoryassign($id)
    {
        try {
            $assignment = Assignment::findOrFail($id);
            return response()->json(['message' => 'get assign', 'data' => $assignment],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Accessory assignment not found', 'error' => $e->getMessage()], 404);
        }
    }

    // Update an accessory assignment
    public function updateaccessoryassign(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'accessory_id' => 'required|exists:accessories,id',
                'assigned_at' => 'nullable|date',
                'status' => 'nullable|string',
            ]);

            $assignment = Assignment::findOrFail($id);
            $assignment->user_id = $validated['user_id'];
            $assignment->accessory_id = $validated['accessory_id'];
            $assignment->assigned_at = $validated['assigned_at'] ?? $assignment->assigned_at;
            $assignment->status = $validated['status'] ?? $assignment->status;
            $assignment->save();

            return response()->json(['message' => 'Accessory assignment updated successfully', 'data' => $assignment],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update accessory assignment', 'error' => $e->getMessage()], 500);
        }
    }

// Delete an accessory assignment
    public function deleteaccessoryassign($id)
    {
        try {
            $assignment = Assignment::findOrFail($id);
            $assignment->delete();

            return response()->json(['message' => 'Accessory assignment deleted successfully','data' => $assignment],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete accessory assignment', 'error' => $e->getMessage()], 500);
        }
    }
}
