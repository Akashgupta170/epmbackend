<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AccessoryAssignController extends Controller
{
    public function addaccessoryassign(Request $request)
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

            // Step 2: Create assignment
            $assignment = Assignment::create([
                'user_id' => $validated['user_id'],
                'accessory_id' => $accessory->id,
                'quantity' => $validated['quantity'],
            ]);

            return response()->json([
                'message' => 'Accessory created and assigned successfully.',
                'accessory' => $accessory,
                'assignment' => $assignment
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
}
