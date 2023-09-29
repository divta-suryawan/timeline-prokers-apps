<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeadershipModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadershipController extends Controller
{
    public function getAllData()
    {
        $user = auth()->user();
        $userRole = $user->role;
        $data = LeadershipModel::all();
        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Get all data successfully',
                'data' => $data,
                'userRole' => $userRole
            ]);
        }
    }

    public function createData(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'periode' => 'required',
            ],
            [
                'periode.required' => 'Form periode tidak boleh kosong',
            ]
        );
        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }
        try {
            $data = new LeadershipModel();
            $data->periode = $request->input('periode');
            $data->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed to create data',
                'errors' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Create data successfully',
            'data' => $data
        ]);
    }

    public function getDataById($id)
    {
        $data = LeadershipModel::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Get data by id successfully',
                'data' => $data
            ]);
        }
    }

    public function updateDataById($id, Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'periode' => 'required',
            ],
            [
                'periode.required' => 'Form periode tidak boleh kosong',
            ]
        );
        if ($validation->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'check your validation',
                'errors' => $validation->errors()
            ]);
        }
        try {
            $data = LeadershipModel::where('id', $id)->firstOrFail();
            $data->periode = $request->input('periode');
            $data->update();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed to update data',
                'errors' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Update data successfully',
            'data' => $data
        ]);
    }


    public function deleteDataById($id)
    {
        try {
            $data = LeadershipModel::where('id', $id)->first();
            if (!$data) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data not found',
                ]);
            }
            $data->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed to delete data',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Deleted data successfully'
        ]);
    }
}
