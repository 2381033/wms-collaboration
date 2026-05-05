<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction\Email as TransactionEmail;

class EmailController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        $details = TransactionEmail::where("company_id", $company_id)->get();

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
            ->addColumn('action', function($data){
                $button = "";
                $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm edit-data"><i class="far fa-edit"></i> Edit</a>';
                $button .= '&nbsp;&nbsp;';
                $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Hapus</button>';
                return $button;
            })
            ->rawColumns(['active', 'action'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('settings.email');
    }

    public function store(Request $request) {
        $messsages = array(
            'description.required'=>'Description cannot be empty.',
            'subject.required'=>'Email Subject cannot be empty.',
            'email_to.required'=>'Email To cannot be empty.',
        );

        $rules = array(
            'description' => 'required',
            'subject' => 'required',
            'email_to' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $company_id = Auth::user()->company_id;
        $id = $request->id;

        TransactionEmail::updateOrCreate(['id' => $id],
        [
            'company_id'=>$company_id,
            'description'=>$request->description,
            'subject' => $request->subject,
            'email_to' => $request->email_to,
            'email_cc' => $request->email_cc,
            'email_bcc' => $request->email_bcc,
            'active' => $request->active
        ]);

        return response()->json(['success'=>'Added new records.']);
    }

    public function edit($id) {
        $data  = TransactionEmail::find($id);

        return response()->json($data);
    }

    public function destroy(Request $request) {
        try {
            $data = TransactionEmail::where('id',$request->id)->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
