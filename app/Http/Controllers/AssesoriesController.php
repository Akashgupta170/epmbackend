<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assesories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssesoriesController extends Controller
{
    public function addassesories(Request $request)
    {
        // Custom validator so we can handle response ourselves
        $validator = Validator::make($request->all(), [
            'assesories_no' => 'required',
            'name' => 'required',
            'stock' => 'required',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            $data = $request->only([
                'assesories_no',
                'name',
                'stock',
            ]);

            Assesories::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Accessories added successfully.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while saving the data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getassesories()
    {
        try {
            $assesories = Assesories::select(
                'id',
                'assesories_no',
                'name',
                'stock',
            )->get();

            return response()->json([
                'success' => true,
                'message' => 'Assesories fetched successfully.',
                'data' => $assesories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assesories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editassesories(Request $request,$id)
    {
        $assesories = Assesories::find('id',$id)->get();


    }
}
