<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ELearning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ELearningController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ELearning::query();

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
            $e_learning = $query->paginate($perPage);

            $data = [
                'e_learning' => $e_learning
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
                'description' => 'required',
                'url' => 'required',
            ]);
            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            $e_learning = new ELearning;
            $e_learning->title = $request->input('title');
            $e_learning->description = $request->input('description');
            $e_learning->url = $request->input('url');
            $e_learning->save();

            return responseJson('Add e learning', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function show($id)
    {
        try {
            $e_learning = ELearning::where('id',$id)->first();
            if (!$e_learning) {
                return responseJson('Data not found', 404, 'Error');
            }
            $data = [
                'e_learning' => $e_learning
            ];
            return responseJson('Show e_learning', 200, 'Success',$data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $e_learning = ELearning::find($id);
            if (!$e_learning) {
                return responseJson('Data not found', 404, 'Error');
            }
            // Validate the updated data
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'url' => 'required',
            ]);
            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            // Update the e_learning data
            $e_learning->title = $request->title;
            $e_learning->description = $request->description;
            $e_learning->url = $request->url;
            $e_learning->save();

            return responseJson('Update e_learning', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function destroy($id)
    {
        try {
            $e_learning = ELearning::find($id);
            if (!$e_learning) {
                return responseJson('Data not found', 404, 'Error');
            }
            $e_learning->delete();
            return responseJson('Delete e_learning', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }
}
