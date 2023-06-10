<?php

namespace App\Http\Controllers\Api\V1\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Personil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PersonilController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Personil::query();

            // Apply search
            $search = $request->input('search');
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nrp', 'like', "%$search%")
                      ->orWhere('username', 'like', "%$search%");
                });
            }

            // Apply filtering by created_at
            $created_at = $request->input('created_at');
            if (!empty($created_at)) {
                $query->whereDate('created_at', $created_at);
            }

            // Paginate the results
            $perPage = $request->input('per_page', 10);
            $personil = $query->paginate($perPage);

            $data = [
                'personil' => $personil
            ];

            return responseJson('All personil', 200, 'Success', $data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nrp' => 'required',
                'name' => 'required',
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }
            $username = Personil::where('username',$request->username)->first();

            if ($username) {
                $errors = $validator->errors();
                $errors->add('username', 'Username already exists');
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            $nrp = Personil::where('nrp',$request->nrp)->first();
            if ($nrp) {
                $errors = $validator->errors();
                $errors->add('nrp', 'NRP already exists');
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            $personil = new Personil;
            $personil->nrp = $request->input('nrp');
            $personil->name = $request->input('name');
            $personil->username = $request->input('username');
            $personil->password = Hash::make($request->input('password'));
            $personil->save();

            return responseJson('Add personil', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function show($id)
    {
        try {
            $personil = Personil::where('id',$id)->first();
            if (!$personil) {
                return responseJson('Data not found', 404, 'Error');
            }
            $data = [
                'personil' => $personil
            ];
            return responseJson('Show personil', 200, 'Success',$data);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $personil = Personil::find($id);
            if (!$personil) {
                return responseJson('Data not found', 404, 'Error');
            }
            // Validate the updated data
            $validator = Validator::make($request->all(), [
                'nrp' => 'required|unique:personil,nrp,' . $id,
                'username' => 'required|unique:personil,username,' . $id,
                'password' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return responseJson('Validation error', 400, 'Error', ['errors' => $validator->errors()]);
            }

            // Update the personil data
            $personil->nrp = $request->nrp;
            $personil->username = $request->username;
            $personil->password = $request->password;
            $personil->save();

            return responseJson('Update personil', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }

    public function destroy($id)
    {
        try {
            $personil = Personil::find($id);
            if (!$personil) {
                return responseJson('Data not found', 404, 'Error');
            }
            $personil->delete();
            return responseJson('Delete personil', 200, 'Success');
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            return responseJson($errorMessage, 500, 'Error');
        }
    }
}
