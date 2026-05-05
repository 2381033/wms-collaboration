<?php

namespace App\Http\Controllers\Api\GateContainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GateCargoController extends Controller
{
    public function submitGateIn(Request $request) {
        dd($request->all());
    }

    private function error($pesan) {
        return response()->json([
            "error" => TRUE,
            "user" => $pesan
        ]);
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            "messages" => 'success',
        ]);
    }
}