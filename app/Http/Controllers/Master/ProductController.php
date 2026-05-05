<?php

namespace App\Http\Controllers\Master;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\Imports\MasterProductImport;

use App\Models\Master\Product as MasterProduct;
use App\Models\Master\ProductGroup as MasterProductGroup;
use App\Models\Master\ProductBrand as MasterProductBrand;
use App\Models\Master\ProductCategory as MasterProductCategory;
use App\Models\Master\Manufactur as MasterManufactur;
use App\Models\Master\LocationType as MasterLocationType;
use App\Models\Reference\UoM as ReferenceUoM;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public $menu_name = "product-master/product";

    public function index(Request $request)
    {

        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $company_id = Auth::user()->company_id;

        $details = MasterProduct::where('company_id', $company_id)
            ->where('principal_id', $request->principal_id)
            ->get();

        if ($request->ajax()) {
            return datatables()->of($details)
                ->editColumn('active', function ($data) {
                    if ($data->active == 'Yes') {
                        $status = '<div class="btn btn-sm btn-success"><i class="fas fa-check"></i><span> ' . $data->active . '</span></div>';
                    } else {
                        $status = '<div class="btn btn-sm btn-warning"><i class="fas fa-trash"></i><span> ' . $data->active . '</span></div>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($data) {
                    $button = "";
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<button type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<button type="button" name="pallet" id="' . $data->id . '" class="pallet btn btn-primary btn-sm"><i class="fas fa-pallet"></i> Pallet Unit</button>';
                    return $button;
                })
                ->rawColumns(['active', 'action'])
                ->addIndexColumn()
                ->make(true);
        }

        $unit = ReferenceUoM::where('active', 'Yes')->get();
        $location_type_list = MasterLocationType::where('active', 'Yes')->get();

        $data = [
            'unit_list' => $unit,
            'location_type_list' => $location_type_list
        ];

        return view('master.product', $data);
    }

    public function reference(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $manufactur_list = MasterManufactur::where('company_id', $company_id)
            ->where('principal_id', $request->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'manufactur_name']);

        $category_list = MasterProductCategory::where('company_id', $company_id)
            ->where('principal_id', $request->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'category_name']);

        $group_list = MasterProductGroup::where('company_id', $company_id)
            ->where('principal_id', $request->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'group_name']);

        $data = [
            'category_list' => $category_list,
            'manufactur_list' => $manufactur_list,
            'group_list' => $group_list,
        ];

        return response()->json($data);
    }

    public function brand(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $brand_list = MasterProductBrand::where('company_id', $company_id)
            ->where('principal_id', $request->principal_id)
            ->where('group_id', $request->group_id)
            ->where('active', 'Yes')
            ->get(['id', 'brand_name']);

        $data = [
            'brand_list' => $brand_list
        ];

        return response()->json($data);
    }

    public function updateDimension(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:iv_product,id',
            'length' => 'required|numeric|gt:0',
            'width'  => 'required|numeric|gt:0',
            'height' => 'required|numeric|gt:0',
        ]);

        $product = DB::table('iv_product')->where('id', $request->product_id)->first();
        if (
            (float)$product->length == 0 ||
            (float)$product->width == 0 ||
            (float)$product->height == 0
        ) {

            DB::table('iv_product')
                ->where('id', $request->product_id)
                ->update([
                    'length' => $request->length,
                    'width' => $request->width,
                    'height' => $request->height,
                    'volume' => ($request->length * $request->width * $request->height) / 1000000,
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'success' => true
        ]);
    }


    public function store(Request $request)
    {
        $messsages = array(
            'product_code.required' => 'Product code cannot be empty.',
            'product_name.required' => 'Product name cannot be empty.',
            'group_id.required' => 'Group name cannot be empty.',
            'brand_id.required' => 'Brand name cannot be empty.',
            'category_id.required' => 'Category name cannot be empty.',
            'pick_criteria.required' => 'Pick criteria cannot be empty.',
            'puom.required' => '1st unit cannot be empty.',
            'muom.required' => '2nd unit cannot be empty.',
            'buom.required' => '3rd unit cannot be empty.',
            'uppp.required' => 'UPPP cannot be empty.',
            'muppp.required' => 'Middle UPPP cannot be empty.',
            'uppp.integer' => 'UPPP must be integer.',
            'muppp.integer' => 'Middle UPPP must be integer.',
        );

        $rules = array(
            'product_code' => 'required',
            'product_name' => 'required',
            'group_id' => 'required',
            'brand_id' => 'required',
            'category_id' => 'required',
            'pick_criteria' => 'required',
            'puom' => 'required',
            'muom' => 'required',
            'buom' => 'required',
            'uppp' => 'required|integer',
            'muppp' => 'required|integer',
            'length' => 'numeric',
            'width' => 'numeric',
            'height' => 'numeric',
            'volume' => 'numeric',
            'gross_weight' => 'numeric',
            'net_weight' => 'numeric',
            'temperature' => 'numeric',
            'shelf_life' => 'integer',
            'freeze_day' => 'integer',
            'base_price' => 'numeric',
        );

        $company_id = Auth::user()->company_id;

        $validator = \Validator::make($request->all(), $rules, $messsages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $id = $request->id;

        MasterProduct::updateOrCreate(
            ['id' => $id],
            [
                'company_id' => $company_id,
                'principal_id' => $request->principal_id,
                'product_code' => $request->product_code,
                'product_name' => $request->product_name,
                'category_id' => $request->category_id,
                'group_id' => $request->group_id,
                'brand_id' => $request->brand_id,
                'pick_criteria' => $request->pick_criteria,
                'unit_level' => $request->unit_level,
                'puom' => $request->puom,
                'muom' => $request->muom,
                'buom' => $request->buom,
                'uppp' => $request->uppp,
                'muppp' => $request->muppp,
                'manufactur_id' => $request->manufactur_id,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'dimensions_unit' => $request->dimensions_unit,
                'volume' => $request->volume,
                'volume_unit' => $request->volume_unit,
                'gross_weight' => $request->gross_weight,
                'net_weight' => $request->net_weight,
                'weight_unit' => $request->weight_unit,
                'temperature' => $request->temperature,
                'shelf_life' => $request->shelf_life,
                'freeze_day' => $request->freeze_day,
                'base_price' => $request->base_price,
                'batch_flag' => $request->batch_flag,
                'expired_flag' => $request->expired_flag,
                'freeze_flag' => $request->freeze_flag,
                'active' => $request->active
            ]
        );

        return response()->json(['success' => 'Added new records.']);
    }

    public function edit($id)
    {
        $company_id = Auth::user()->company_id;

        $edit  = MasterProduct::where('id', $id)->first();

        $manufactur_list = MasterManufactur::where('company_id', $company_id)
            ->where('principal_id', $edit->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'manufactur_name']);

        $category_list = MasterProductCategory::where('company_id', $company_id)
            ->where('principal_id', $edit->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'category_name']);

        $group_list = MasterProductGroup::where('company_id', $company_id)
            ->where('principal_id', $edit->principal_id)
            ->where('active', 'Yes')
            ->get(['id', 'group_name']);

        $brand_list = MasterProductBrand::where('company_id', $company_id)
            ->where('principal_id', $edit->principal_id)
            ->where('group_id', $edit->group_id)
            ->where('active', 'Yes')
            ->get(['id', 'brand_name']);

        $data = [
            'edit_view' => $edit,
            'category_list' => $category_list,
            'manufactur_list' => $manufactur_list,
            'group_list' => $group_list,
            'brand_list' => $brand_list
        ];

        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        try {
            MasterProduct::where('id', $request->id)->delete();

            $data = ['success' => 'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex) {
            $data = ['error' => 'Cannot be deleted, this data is already used.'];
        }

        return response()->json($data);
    }

    public function upload(Request $request)
    {
        $principal_id = $request->principal;
        Excel::import(new MasterProductImport($principal_id), $request->file('file'));

        return back();
    }
}
