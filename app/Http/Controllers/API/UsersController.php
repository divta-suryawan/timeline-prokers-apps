<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function getAllData()
    {
        $data = User::where('role', 'user')->get();;
        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'success get all data',
                'data' => $data
            ]);
        }
    }

    public function createData(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'position' => 'required',
                'password' => 'required|confirmed|min:6',
                'password_confirmation' => 'required'
            ],
            [
                'name.required' => 'Form name tidak boleh kosong',
                'email.required' => 'Form email tidak boleh kosong',
                'position.required' => 'Form jabatan tidak boleh kosong',
                'password.required' => 'Form password tidak boleh kosong',
                'password_confirmation.required' => 'Form password confirmation tidak boleh kosong',
                'email.email' => 'Mohon isi alamat email dengan format yang benar',
                'email.unique' => 'Email sudah digunakan',
                'password.confirmed' => 'Password tidak sama'
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
            $data = new User();
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->position = $request->input('position');
            $data->password = Hash::make($request->input('password'));
            $data->save();
            $token = $data->createToken('auth_token')->plainTextToken;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success create data',
            'data' => $data,
            'access token' => $token
        ]);
    }

    public function getDataById($id)
    {

        $data = User::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'code' => 400,
                'message' => 'Data not found',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Success get data by id',
                'data' => $data
            ]);
        }
    }

    public function updateDataById($id, Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'position' => 'required',
                'password' => 'confirmed',
            ],
            [
                'name.required' => 'Form name tidak boleh kosong',
                'email.required' => 'Form email tidak boleh kosong',
                'position.required' => 'Form jabatan tidak boleh kosong',
                'email.email' => 'Mohon isi alamat email dengan format yang benar',
                'password.confirmed' => 'Password tidak sama'
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
            $data = User::where('id', $id)->firstOrFail();
            $data->name = $request->input('name');
            $data->email = $request->input('email');
            $data->position = $request->input('position');
            $data->password = Hash::make($request->input('password'));
            $data->update();
            $token = $data->createToken('auth_token')->plainTextToken;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success update data',
            'data' => $data,
            'access token' => $token
        ]);
    }
    public function deleteDataById($id)
    {

        try {
            $data = User::where('id', $id)->first();
            if (!$data) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data Not Found',
                ]);
            }
            $data->tokens()->delete();
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
            'message' => 'Delete data success'
        ]);
    }
}
