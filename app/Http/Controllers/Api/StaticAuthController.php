<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaticAuthController extends Controller
{
    protected $validToken = 'icvax43l4B9othPKYmth3pjq';
    public $page = 20;

    private function validateToken(Request $request)
    {
        $token = $request->header('Authorization') ?? $request->get('token') ?? $request->get('auth');
        return $token === $this->validToken;
    }

    public function getShipper(Request $request)
    {
        // Validate token
        if (!$this->validateToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid auth token',
                'data' => []
            ], 401);
        }

        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $list = DB::table('mt_shipper as a')
                ->where('a.shipper_name', 'LIKE', $search_text)
                ->where('a.branch_id', 1)
                ->take($this->page)
                ->orderBy("a.shipper_name")
                ->get();

            $response = $list;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data' => $response
        ], 200);
    }

    public function getForwarder(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid auth token',
                'data' => []
            ], 401);
        }

        $response = array();

        if ($request->has("search")) {
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%" . $search . "%";
            }

            $service_name = $request->get('service_name', '');

            $query = DB::table('mt_forwarder as a')
                ->where('a.branch_id', 1)
                ->where('a.forwarder_name', 'LIKE', $search_text);

            if(!empty($service_name)) {
                $query->join("mt_forwarder_service as b", "a.id", "b.forwarder_id")
                    ->join("mt_service as c", "b.service_id", "c.id")
                    ->where("c.service_name", $service_name);
            }

            $list = $query->take($this->page)
                ->orderBy("a.forwarder_name")
                ->distinct()
                ->get();
            $response = $list;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data' => $response
        ], 200);
    }

    public function getAllShippers(Request $request)
    {
        // Validate token
        if (!$this->validateToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid auth token',
                'data' => []
            ], 401);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 50);
        $offset = ($page - 1) * $limit;

        $total = DB::table('mt_shipper')->count();

        $list = DB::table('mt_shipper')
            ->where('branch_id', 1)
            ->orderBy('shipper_name')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
            'data' => $list
        ], 200);
    }
    
    public function getAllForwarders(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid auth token',
                'data' => []
            ], 401);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 50);
        $offset = ($page - 1) * $limit;

        $total = DB::table('mt_forwarder')->count();

        $list = DB::table('mt_forwarder')
            ->where('branch_id', 1)
            ->orderBy('forwarder_name')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
            'data' => $list
        ], 200);
    }
}
