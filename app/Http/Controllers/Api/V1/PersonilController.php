<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Personil;
use Illuminate\Http\Request;

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

    public function create(Request $request)
    {
        return responseJson('personil', 200, 'Success');
    }

    public function store()
    {
        return responseJson('personil', 200, 'Success');
    }

    public function show()
    {
        return responseJson('personil', 200, 'Success');
    }

    public function edit()
    {
        return responseJson('personil', 200, 'Success');
    }

    public function update()
    {
        return responseJson('personil', 200, 'Success');
    }

    public function destroy()
    {
        return responseJson('personil', 200, 'Success');
    }
}
