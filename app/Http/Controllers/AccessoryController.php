<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\AccessoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccessoryController extends Controller
{
    public function addaccessory(Request $request)
    {
        try {
            // Validate the input fields
            $validated = $request->validate([
                'category_id' => 'required|exists:accessory_categories,id', // Category ID is required
                'brand_name' => 'nullable|max:255',
                'vendor_name' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'amount' => 'nullable|integer|min:0',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'note' => 'nullable|string',
                'status' => 'nullable|string|max:255',
            ]);

            // Fetch category code from accessory_categories based on category_id
            $category = AccessoryCategory::findOrFail($validated['category_id']);
            $categoryCode = $category->category_code; // Assuming you have a column named category_code in accessory_categories

            $lastAccessory = Accessory::where('category_id', $validated['category_id'])
                ->orderBy('accessory_no', 'desc')
                ->first();

            $lastNumber = 1;
            if ($lastAccessory) {
                $lastAccessoryNo = $lastAccessory->accessory_no;
                $lastNumber = (int) substr($lastAccessoryNo, strrpos($lastAccessoryNo, '-') + 1) + 1;
            }

            $accessoryNo = $categoryCode . '-' . $lastNumber;

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Store each image and get the path
                    $path = $image->store('images/accessories', 'public');
                    $imagePaths[] = $path; // Save the path to an array
                }
            }

            $validated['images'] = json_encode($imagePaths);

            // Create a new accessory record
            $accessory = new Accessory();
            $accessory->accessory_no = $accessoryNo;
            $accessory->brand_name = $validated['brand_name'];
            $accessory->category_id = $validated['category_id'];
            $accessory->vendor_name = $validated['vendor_name'];
            $accessory->condition = $validated['condition'];
            $accessory->purchase_date = $validated['purchase_date'];
            $accessory->amount = $validated['amount'];
            $accessory->images = $validated['images'];
            $accessory->note = $validated['note'];
            $accessory->status = $validated['status'];
            $accessory->save();

            return response()->json([
                'message' => 'Accessory created successfully.',
                'data' => $accessory,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create accessory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function allaccessory()
    {
        try {
            $allaccessories = Accessory::orderByDesc('id')->get();
            return response()->json([
                'message' => 'all Accessories retrieved successfully.',
                'data' => $allaccessories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch all accessories.',
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
            // Validate the request data
            $validated = $request->validate([
                'accessory_no' => 'nullable|max:255',
                'brand_name' => 'nullable|max:255',
                'category_id' => 'required|exists:accessory_categories,id',
                'vendor_name' => 'nullable|string|max:255',
                'condition' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'amount' => 'nullable|integer|min:0',
                'images' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Only single image
                'note' => 'nullable|string',
                'status' => 'nullable|string|max:255',
            ]);

            // Handle image upload if a new image is provided
            if ($request->hasFile('images')) {
                // Find the existing accessory record
                $accessory = Accessory::findOrFail($id);

                // Delete the existing image (if any)
                $existingImages = json_decode($accessory->images, true);

                if ($existingImages) {
                    // Delete existing image file from storage
                    foreach ($existingImages as $image) {
                        // Delete the existing image file
                        Storage::disk('public')->delete($image);
                    }
                }

                // Store the new image and get the path
                $image = $request->file('images');
                $path = $image->store('uploads/accessories', 'public');

                // Store the new image path as a JSON array
                $validated['images'] = json_encode([$path]); // Store it as an array, even if it's a single image
            }

            // Find the accessory and update it with validated data
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
        // Find the accessory by ID
        $accessory = Accessory::findOrFail($id);

        // Check if the accessory has images
        if ($accessory->images) {
            // Decode the images (JSON stored in the database)
            $images = json_decode($accessory->images, true);

            // Loop through each image and delete from the storage
            foreach ($images as $image) {
                // Delete the image file from storage
                Storage::disk('public')->delete($image);
            }
        }

        // Delete the accessory from the database
        $accessory->delete();

        return response()->json([
            'message' => 'Accessory and its images deleted successfully.',
            'data' => $accessory
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // If the accessory is not found
        return response()->json([
            'message' => 'Accessory not found.'
        ], 404);
    } catch (\Exception $e) {
        // If any unexpected error occurs
        return response()->json([
            'message' => 'Failed to delete accessory.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
