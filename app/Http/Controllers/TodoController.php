<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index() {
        try {
            $data = Todo::select('id', 'title', 'description', 'due_date', 'status', 'deleted_at')->withTrashed()->get();
            return response()->json([ 'records' => $data ], 200);
        } catch (\Throwable $th) {
            return response()->json([ 'message' => 'Problem in fetching data' ], 500);
        }
    }

    public function store(Request $request) {
        try {
            $dataToSave = $request;
            $dataToSave['status'] = 'pending';
            $res = Todo::create($dataToSave->toArray());
            return response()->json($res);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function destroy(int $id) {
        try {
            $record = Todo::withTrashed()->find($id);
            if (!$record) {
                return response()->json(['message' => 'Record you are trying to delete is not found'], 500);
            }
            if (!$record->deleted_at) {
                $record->delete();
            }else if($record->deleted_at){
                $record->forceDelete();
            }
            return response()->json(['message' => 'Data deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function changeStatus(int $id) {
        try {
            $record = Todo::find($id);
            if (!$record) {
                return response()->json(['message' => 'Record you are trying to update is not found'], 500);
            }

            $record->status = $record->status === 'pending' ? 'completed' : 'pending';
            $result = $record->save();
            if ($result) {
                return response()->json(['message' => 'Status Updated Successfully'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

}
