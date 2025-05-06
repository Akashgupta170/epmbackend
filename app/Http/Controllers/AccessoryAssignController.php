<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccessoryAssign;
use Illuminate\Http\Request;

class AccessoryAssignController extends Controller
{
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

            $accessoryAssign = new AccessoryAssign();
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
            $assignments = AccessoryAssign::with(['user', 'accessory'])->get();
            return response()->json(['message' => 'all fetch successfully', 'data' => $assignments],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve accessory assignments', 'error' => $e->getMessage()], 500);
        }
    }

    // Get a specific accessory assignment
    public function editaccessoryassign($id)
    {
        try {
            $assignment = AccessoryAssign::findOrFail($id);
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

            $assignment = AccessoryAssign::findOrFail($id);
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
            $assignment = AccessoryAssign::findOrFail($id);
            $assignment->delete();

            return response()->json(['message' => 'Accessory assignment deleted successfully','data' => $assignment],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete accessory assignment', 'error' => $e->getMessage()], 500);
        }
    }
}
