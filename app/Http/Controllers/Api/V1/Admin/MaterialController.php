<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Materials::query();

            // Apply search
            $search = $request->input('search');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%");
                });
            }

            // Apply filtering by created_at
            $created_at = $request->input('created_at');
            if (!empty($created_at)) {
                $query->whereDate('created_at', $created_at);
            }

            // Paginate the results
            $perPage = $request->input('per_page', 10);
            $material = $query->paginate($perPage);

            $data = [
                'material' => $material
            ];

            return responseJson('All e learning', 200, 'Success', $data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category' => 'required',
                'description' => 'required',
                'file' => 'required|image|mimes:jpeg,jpg,png,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            $material = new Materials();
            $material->title = $request->input('title');
            $material->category = $request->input('category');
            $material->description = $request->input('description');

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('uploads/material/file', 'public');
                $material->image = $path;
            }

            $material->save();

            return responseJson('Add e learning', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function show($id)
    {
        try {
            $material = Materials::where('id',$id)->first();
            if (!$material) {
                return responseJson('Data not found', 404, 'Error');
            }
            $data = [
                'material' => $material
            ];
            return responseJson('Show material', 200, 'Success',$data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            return responseJson('Update material', 200, 'Success', [
                'title'=>$request->input('title'),
                'category' => $request->input('category'),
                'description' => $request->input('description'),
                'file' => $request->file('file'),
                'all' => $request->all()
            ]);

            $material = Materials::find($id);
            if (!$material) {
                return responseJson('Data not found', 404, 'Error');
            }
            // Validate the updated data
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required',
                'description' => 'sometimes|required',
                'file' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            // Update the e_learning data
            $material->title = $request->title;
            $material->description = $request->description;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('uploads/material/file', 'public');
                $material->image = $path;
            }

            $material->save();

            return responseJson('Update material', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function destroy($id)
    {
        try {
            $material = Materials::find($id);
            if (!$material) {
                return responseJson('Data not found', 404, 'Error');
            }
            $material->delete();
            return responseJson('Delete e_learning', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }
}
