<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeadershipModel;
use App\Models\ProkersModel;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class ProkersController extends Controller
{

    public function getAllData()
    {
        $user = Auth::user();
        $latestLeadership = LeadershipModel::latest()->first();
        $userRole = $user->role;
        if ($user->role == 'user') {
            $data = ProkersModel::with('leadership', 'users')
                ->where('id_user', $user->id)
                ->where('id_leadership', $latestLeadership->id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data not found ',
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'success get all data',
                    'data' => $data,
                    'userRole' => $userRole

                ]);
            }
        } elseif ($user->role == 'admin') {
            $data = ProkersModel::with('leadership', 'users')
                ->where('id_leadership', $latestLeadership->id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data not found ',
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'success get all data',
                    'data' => $data,
                    'userRole' => $userRole

                ]);
            }
        }
    }



    public function createData(Request $request)
    {
        $validation = FacadesValidator::make(
            $request->all(),
            [
                'name' => 'required',
                'start' => 'required|date',
                'end' => 'required|date',
            ],
            [
                'name' => 'Form name tidak boleh kosong',
                'start.required' => 'From start tidak boleh kosong',
                'end.required' => 'From end tidak boleh kosong',
                'start.date' => 'Harus menggunakan format tanggal yang benar',
                'end.date' => 'Harus menggunakan format tanggal yang benar',
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
            $user = Auth()->user();
            $data = new ProkersModel();
            $data->id_user = $user->id;
            $data->name = $request->input('name');
            $data->start = $request->input('start');
            $data->end = $request->input('end');
            $latestLeadership = LeadershipModel::latest()->first();
            $idLeadership = $latestLeadership->id;
            $data->id_leadership = $idLeadership;
            $data->save();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed',
                'errors' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success create data',
            'data' => $data
        ]);
    }

    public function getDataById($id)
    {
        $data = ProkersModel::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'code' => 404,
                'messeage' => 'Data not found'
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Get Data By id successfully',
                'data' => $data
            ]);
        }
    }

    public function updateDataById($id, Request $request)
    {
        $validation = FacadesValidator::make(
            $request->all(),
            [
                'name' => 'required',
                'start' => 'required|date',
                'end' => 'required|date',
            ],
            [
                'name' => 'Form name tidak boleh kosong',
                'start.required' => 'From start tidak boleh kosong',
                'end.required' => 'From end tidak boleh kosong',
                'start.date' => 'Harus menggunakan format tanggal yang benar',
                'end.date' => 'Harus menggunakan format tanggal yang benar',
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
            $data = ProkersModel::where('id', $id)->first();
            $data->name = $request->input('name');
            $data->start = $request->input('start');
            $data->end = $request->input('end');
            $latestLeadership = LeadershipModel::latest()->first();
            $idLeadership = $latestLeadership->id;
            $data->id_leadership = $idLeadership;
            $data->update();
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => 'Failed',
                'errors' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success update data',
            'data' => $data
        ]);
    }

    public function deleteDataById($id)
    {
        try {
            $data = ProkersModel::where('id', $id)->first();
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


    // get leadership new
    public function getLatestLeadership()
    {
        $data = LeadershipModel::latest()->first();
        if ($data) {
            return response()->json([
                'data' => [
                    'id' => $data->id,
                    'periode' => $data->periode,
                ]
            ]);
        } else {
            return response()->json(['message' => 'Tidak ada data leadership.']);
        }
    }

    public function startProker($id)
    {
        $data = ProkersModel::findOrFail($id);

        if ($data->status === 'pending') {
            $data->status = 'on-progress';
            $data->save();
            return response()->json($data);
        } elseif ($data->status === 'on-progress') {
            return response()->json(['message' => 'Proker is already on-progress.'], 400);
        } elseif ($data->status === 'finish') {
            return response()->json(['message' => 'Proker is already done.'], 400);
        }
    }

    public function finishProker($id, Request $request)
    {
        $data = ProkersModel::findOrFail($id);

        if ($data->status === 'on-progress') {
            if ($request->has('ket') && !empty($request->input('ket'))) {
                $data->status = 'finish';
                $data->ket = $request->input('ket');
                $data->save();
                return response()->json($data);
            } else {
                return response()->json(['message' => 'Field "keterangan" must be filled.'], 400);
            }
        } elseif ($data->status === 'finish') {
            return response()->json(['message' => 'Proker is already finish.'], 400);
        } else {
            return response()->json(['message' => 'Proker cannot be finished.'], 400);
        }
    }

    public function markProkerNotFinished($id, Request $request)
    {
        $data = ProkersModel::findOrFail($id);

        if ($data->status === 'on-progress') {
            if ($request->has('ket') && !empty($request->input('ket'))) {
                $data->status = 'not-finish';
                $data->ket = $request->input('ket');
                $data->save();
                return response()->json($data);
            } else {
                return response()->json(['message' => 'Field "keterangan" must be filled.'], 400);
            }
        } elseif ($data->status === 'finish') {
            return response()->json(['message' => 'Proker is not-finish.'], 400);
        } else {
            return response()->json(['message' => 'Proker cannot be finished.'], 400);
        }
    }

    public function detail($status)
    {
        $latestLeadership = LeadershipModel::latest()->first();
        $user = Auth::user();
        if ($user->role === 'user') {
            $data = ProkersModel::with('leadership', 'users')
                ->where('id_user', $user->id)
                ->where('id_leadership', $latestLeadership->id)
                ->where('status', $status)
                ->get();

            $statusCounts = [
                'pending' => ProkersModel::where('status', 'pending')->count(),
                'on_progress' => ProkersModel::where('status', 'on-progress')->count(),
                'finish' => ProkersModel::where('status', 'finish')->count(),
                'not_finish' => ProkersModel::where('status', 'not-finish')->count(),
            ];
            if ($data->isEmpty()) {
                return response()->json([
                    'userRole' => $user->role,
                    'status' => $status,
                    'data' => $data,
                    'statusCounts' => $statusCounts,
                    'code' => 200,
                    'message' => 'User does not have data for this status'
                ]);
            }

            return response()->json([
                'userRole' => $user->role,
                'status' => $status,
                'data' => $data,
                'statusCounts' => $statusCounts,
                'code' => 200,
                'message' => 'Success get by status'
            ]);
        } elseif ($user->role === 'admin') {
            $data = ProkersModel::with(['leadership', 'users'])
                ->where('id_leadership', $latestLeadership->id)
                ->where('status', $status)
                ->get();

            $statusCounts = [
                'pending' => ProkersModel::where('status', 'pending')->count(),
                'on_progress' => ProkersModel::where('status', 'on-progress')->count(),
                'finish' => ProkersModel::where('status', 'finish')->count(),
                'not_finish' => ProkersModel::where('status', 'not-finish')->count(),
            ];

            return response()->json([
                'userRole' => $user->role,
                'status' => $status,
                'data' => $data,
                'statusCounts' => $statusCounts,
                'code' => 200,
                'message' => 'Success get by status'
            ]);
        }
    }

    public function getDataByLeadership($id)
    {
        $user = Auth::user();
        if ($user->role == 'user') {
            $data = ProkersModel::with('leadership', 'users')
                ->where('id_leadership', $id)
                ->where('id_user', $user->id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data not found ',
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'success get all data',
                    'data' => $data
                ]);
            }
        } elseif ($user->role == 'admin') {
            $data = ProkersModel::with('leadership', 'users')
                ->where('id_leadership', $id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data not found ',
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => 'success get all data',
                    'data' => $data
                ]);
            }
        }
    }
}
