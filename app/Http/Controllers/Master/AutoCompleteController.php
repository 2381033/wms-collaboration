<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoCompleteController extends Controller
{
    public $page = 20;

    public function getProduct(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $response = array();

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table('iv_product as a')
                            ->select("a.id", "a.product_code", "a.product_name", "a.puom", "a.muom", "a.buom", "a.uppp", "a.muppp", "a.unit_level")
                            ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
                            ->where('a.company_id', $company_id)
                            ->where('b.user_id', $user_id)
                            ->where('a.active', 'Yes')
                            ->where('a.principal_id', $request->principal_id)                    
                            ->where(function($query) use($search_text) {
                                $query->where("a.product_code", "LIKE", $search_text)
                                    ->orWhere("a.product_name","LIKE",$search_text);
                            })
                            ->take($this->page)
                            ->orderBy("a.product_name", "asc")
                            ->get();
                    
            foreach ($list as $k) {
                $response[] = array( 'id' =>$k->id, 
                    'product_code' =>$k->product_code, 
                    'product_name' => $k->product_name, 
                    'puom' => $k->puom, 
                    'muom' => $k->muom, 
                    'buom' => $k->buom, 
                    'uppp' => $k->uppp, 
                    'muppp' => $k->muppp, 
                    'unit_level' => $k->unit_level
                );
            }
        }

        echo json_encode($response);
        exit;
    }   
    
    public function getAreaAuto(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table('iv_site_area as a')
                        ->join('iv_site as b', 'a.site_id', 'b.id')
                        ->join('users_site as c', 'a.site_id', 'c.site_id')
                        ->where('a.company_id', $company_id)
                        ->where('c.user_id', $user_id)
                        ->where('a.site_id', $request->site_id)
                        ->where('a.active', 'Yes')
                        ->where('a.area_name','LIKE',$search_text)
                        ->take($this->page)
                        ->get(['a.id', 'a.area_name', 'a.site_id', 'b.site_name']);        
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "area_id" =>$k->id, 
                "area_name" =>$k->area_name, 
                "site_id" =>$k->site_id, 
                "site_name" =>$k->site_name, 
            );
        }

        return response()->json($response);
        exit;
    }     
    
    public function getAreaList(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $list = DB::table('iv_site_area as a')
                    ->join('iv_site as b', 'a.site_id', 'b.id')
                    ->join('users_site as c', 'a.site_id', 'c.site_id')
                    ->where('a.company_id', $company_id)
                    ->where('c.user_id', $user_id)
                    ->where('a.site_id', $request->site_id)
                    ->where('a.active', 'Yes')
                    ->get(['a.id', 'a.area_name', 'a.site_id', 'b.site_name']);
    
        $response = [
            "area_list"=>$list
        ];

        return response()->json($response);
        exit;
    }          
    
    public function getBranchPrincipalList(Request $request) {
        $list = DB::table('mt_branch as a')
                    ->join('iv_principal_branch as b', 'a.id', 'b.branch_id')
                    ->where('b.principal_id', $request->principal_id)
                    ->where('a.active', 'Yes')
                    ->get(['a.id', 'a.branch_name']);
    
        $response = [
            "list"=>$list
        ];

        return response()->json($response);
        exit;
    }     
    
    public function getLocationList(Request $request) {
        $company_id = Auth::user()->company_id;

        $location = DB::table('iv_location')
                        ->where('company_id', $company_id)
                        ->where('site_id', $request->site_id)
                        ->where('area_id', $request->area_id)
                        ->where('status_code', 'E')
                        ->where('active', 'Yes')
                        ->get(['id', 'location_code']);
        
        $data = [
            'location_list'=>$location,
        ];

        return response()->json($data);
        exit;
    }
    
    public function getLocationAuto(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table('iv_location as a')
                        ->select('a.*', 'b.site_name', 'c.area_name')
                        ->join('iv_site as b', 'a.site_id', 'b.id')
                        ->join('iv_site_area as c', 'a.area_id', 'c.id')
                        ->join('users_site as d', "a.site_id", "d.site_id")
                        ->where('a.company_id', $company_id)
                        ->where('d.user_id', $user_id)
                        ->where('a.site_id', 'like', $request->site_id)
                        ->where('a.area_id', 'like', $request->area_id)
                        ->whereIn('a.status_code', ['E', 'M', 'B'])
                        ->where('a.active', 'Yes')
                        ->where('a.location_code','LIKE',$search_text)
                        ->take($this->page)
                        ->get(['a.id', 'a.location_code']);
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "site_id" =>$k->site_id, 
                "site_name" =>$k->site_name,
                "area_id" =>$k->area_id, 
                "area_name" =>$k->area_name,
                "location_id" =>$k->id, 
                "location_code" =>$k->location_code
            );
        }

        return response()->json($response);
        exit;
    }
    
    public function getLocationPrincipalAuto(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table('iv_location as a')
                        ->select('a.*', 'b.site_name', 'c.area_name')
                        ->join('iv_site as b', 'a.site_id', 'b.id')
                        ->join('iv_site_area as c', 'a.area_id', 'c.id')
                        ->join('users_site as d', "a.site_id", "d.site_id")
                        ->join('iv_principal_site as e', "a.site_id", "e.site_id")
                        ->where('a.company_id', $company_id)
                        ->where('d.user_id', $user_id)
                        ->where('e.principal_id', 'like', $request->principal_id)
                        ->where('a.site_id', 'like', $request->site_id)
                        ->where('a.area_id', 'like', $request->area_id)
                        ->whereIn('a.status_code', ['E', 'M', 'B'])
                        ->where('a.active', 'Yes')
                        ->where('a.location_code','LIKE',$search_text)
                        ->orderBy("a.location_code", "ASC")
                        ->take($this->page)
                        ->get(['a.id', 'a.location_code']);
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "site_id" =>$k->site_id, 
                "site_name" =>$k->site_name,
                "area_id" =>$k->area_id, 
                "area_name" =>$k->area_name,
                "location_id" =>$k->id, 
                "location_code" =>$k->location_code
            );
        }

        return response()->json($response);
        exit;
    }
    
    public function getLocationMixedAuto(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $mixed_pallet = $request->mixed_pallet;            

            $site_id = "%";
            $area_id = "%";

            if (!empty($request->site_id) && isset($request->site_id)) {
                $site_id = $request->site_id;
            }

            if (!empty($request->area_id) && isset($request->area_id)) {
                $area_id = $request->area_id;
            }
            
            if ( $mixed_pallet == "No" ) {
                $status = "E";
            } else {
                $status = "M";
            }            

            $list = DB::table('iv_location as a')
                        ->select('a.*', 'b.site_name', 'c.area_name')
                        ->join('iv_site as b', 'a.site_id', 'b.id')
                        ->join('iv_site_area as c', 'a.area_id', 'c.id')
                        ->join('users_site as d', "a.site_id", "d.site_id")
                        ->where('a.company_id', $company_id)
                        ->where('d.user_id', $user_id)
                        ->where('a.site_id', 'like', $site_id)
                        ->where('a.area_id', 'like', $area_id)
                        ->where('a.status_code', $status)
                        ->where('a.active', 'Yes')
                        ->where('a.location_code','LIKE',$search_text)
                        ->take($this->page)
                        ->get(['a.id', 'a.location_code']);
        }

        $response = array();                
        foreach ($list as $k) {
            $response[] = array( 
                "site_id" =>$k->site_id, 
                "site_name" =>$k->site_name,
                "area_id" =>$k->area_id, 
                "area_name" =>$k->area_name,
                "location_id" =>$k->id, 
                "location_code" =>$k->location_code
            );
        }

        return response()->json($response);
        exit;
    }

    public function getLocationAll(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
         
        $location = DB::table('iv_location as a')
                        ->select("a.*" )
                        ->join("users_site as c", "a.site_id", "c.site_id")
                        ->where("a.company_id", $company_id)
                        ->where("c.user_id", $user_id)
                        ->where("a.site_id", $request->site_id)
                        ->where("a.area_id", $request->area_id)
                        ->whereIn('a.status_code', ['E', 'F', 'P'])
                        ->where('a.active', 'Yes')
                        ->get();
                
        $data = [
            'location_list'=>$location,
        ];

        return response()->json($data);
        exit;
    }   
    
    public function getCustomer(Request $request) {
        $company_id = Auth::user()->company_id;

        $customer = DB::table('iv_customer')
                        ->where('company_id', $company_id)
                        ->where('principal_id', $request->principal_id)
                        ->where('active', 'Yes')
                        ->get(['id', 'customer_name']);
        
        $data = [
            'customer_list'=>$customer,
        ];

        return response()->json($data);
        exit;
    }      
    
    public function getCustomerAuto(Request $request) {
        $company_id = Auth::user()->company_id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $customer = DB::table('iv_customer')
                            ->where('company_id', $company_id)
                            ->where('principal_id', $request->principal_id)
                            ->where('active', 'Yes')
                            ->where('customer_name','LIKE',$search_text)
                            ->orderBy('customer_name', 'asc')
                            ->get(['id', 'customer_code', 'customer_name']);

            $response = [];
            foreach ($customer as $k) {
                $response[] = array( 
                    'id' =>$k->id, 
                    'customer_code' =>$k->customer_code,
                    'customer_name' =>$k->customer_name
                );
            }
        }

        echo json_encode($response);
        exit;
    }   

    public function getStore(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if($request->has('search')){
            $search = $request->search;
            if (is_null($search) || empty($search) || strlen($search) < 1) {
                $search_text = "%";
            } else {
                $search_text = "%".$search."%";
            }

            $list = DB::table('tm_store as a')
                        ->select("a.id", "a.store_code", "a.store_name", "c.city_name", "a.address1", "a.address2", "a.address3", "a.address4")
                        ->join('users_principal as b', 'a.principal_id', 'b.principal_id')
                        ->join('rt_city as c', 'a.city_code', 'c.city_code')
                        ->where('a.company_id', $company_id)
                        ->where('b.user_id', $user_id)
                        ->where('a.active', 'Yes')
                        ->where('a.principal_id', $request->principal_id)
                        ->where('a.store_name','LIKE',$search_text)
                        ->take($this->page)
                        ->get();
            
            $response = [];
            foreach ($list as $k) {
                $address = $k->address1 !== "" || $k->address1 !== null ? $k->address1 : ''; 
                $address .= $k->address2 !== "" || $k->address2 !== null ? ' ' . $k->address2 : ''; 
                $address .= $k->address3 !== "" || $k->address3 !== null ? ' ' . $k->address3 : ''; 
                $address .= $k->address4 !== "" || $k->address4 !== null ? ' ' . $k->address4 : ''; 
                        
                $response[] = array( 
                    'id' =>$k->id, 
                    'store_code' =>$k->store_code, 
                    'store_name' => $k->store_name, 
                    'city_name' => $k->city_name,
                    'address' => $address
                );
            }
        }

        echo json_encode($response);
        exit;
    }   
}