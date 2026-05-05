<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use App\Mail\BookingEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Transaction\CY\Booking as CYBooking;

class BookingController extends Controller
{
    public $menu_name = "cy/booking";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat("d/m/Y", $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format("Y-m-d");

            $dateObject = \Carbon\Carbon::createFromFormat("d/m/Y", $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format("Y-m-d");

            $list_data = DB::table("cy_booking as a")
                            ->select("a.*", "b.forwarder_name")
                            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                            ->where("a.branch_id", $request->branch_id)
                            ->whereBetween("a.booking_date", [$date_from, $date_to])
                            ->where("a.status_flag", $request->status_code)
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('booking_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->booking_date) );
                })
                ->addColumn('booking_no', function($data){
                    $button = "";
                    $button .= '<a href="' . URL("/cy/booking/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->booking_no . '</a>';
                    return $button;
                })
                ->rawColumns(['booking_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.cy.booking.index");
    }

    public function create($id = "") {
        $company_id = Auth::user()->company_id;

        $header = DB::table("cy_booking as a")
                        ->select("a.*", "b.forwarder_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->where("a.id", $id)
                        ->first();

        $invoice_list = DB::table("cy_invoice_type")->where("company_id", $company_id)->where("active", "Yes")->get();
        $size_list = DB::table("iv_container_size")->where("company_id", $company_id)->where("active", "Yes")->get();
        $type_list = DB::table("iv_container_type")->where("company_id", $company_id)->where("active", "Yes")->get();

        $data = [
            "header" => $header,
            "invoice_list" => $invoice_list,
            "size_list" => $size_list,
            "type_list" => $type_list
        ];

        return view("transaction.cy.booking.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_id.required'=>'Branch name cannot be empty.',
            'forwarder_id.required'=>'Company name cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;
                $booking_id = $request->booking_id;

                $header = CYBooking::find($booking_id);

                if (!isset($header)) {
                    $header = new CYBooking();
                    $header->booking_no = $this->getJob($request->branch_id);
                    $header->booking_date = \Carbon\Carbon::today();
                }

                $header->branch_id = $request->branch_id;
                $header->forwarder_id = $request->forwarder_id;
                $header->reference_no = $request->reference_no;
                $header->driver_name = $request->driver_name;
                $header->vehicle_no = $request->vehicle_no;
                $header->invoice_type = $request->invoice_type;
                $header->size_id = $request->size_id;
                $header->type_id = $request->type_id;
                $header->container_status = $request->container_status;
                $header->container_no = $request->container_no;
                $header->user_id = $username;
                $header->save();

                DB::commit();

                $message = ['success'=>url('/cy/booking/create/' . $header->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;
        $random = rand(0, 999);

        $book_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($random)->padLeft(3, '0');

        $count = CYBooking::where('branch_id', $branch_id)
                    ->whereYear('booking_date', $year)
                    ->whereMonth('booking_date', $month)
                    ->where("booking_no", $book_no)
                    ->count();

        if ($count == 0) {
            return $book_no;
        } else {
            $this->getjob($branch_id);
        }
    }

    public function email($id) {
        $header = DB::table("cy_booking as a")
                        ->select("a.*", "b.forwarder_name", "c.size_name", "d.type_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->leftjoin("iv_container_size as c", "a.size_id", "c.id")
                        ->leftjoin("iv_container_type as d", "a.type_id", "d.id")
                        ->where("a.id", $id)
                        ->first();

        $respon = [
            "header" => $header,
        ];

        $storage_path = 'public/pdf';
        $filename = "$header->booking_no.pdf";
        $filePath = $storage_path . '/' . $filename;

        $customPaper = array(0,0,400.00,450.00);
        $pdf = \PDF::loadView("pdf.cy.booking", $respon)->setPaper($customPaper, 'landscape');

        Storage::put($filePath, $pdf->output());

        $fileurl = Storage::path($filePath);

        $email_to = [
            // "slamet.riyanto@samudera.id",
            "firman.setiawan@samudera.id",
            // "dessyntya.arsa@samudera.id"
        ];

        \Mail::to($email_to)->send(new BookingEmail($fileurl));

        Storage::delete($filePath);

        $message = ["success"=>"Sukses"];

        return  response()->json($message);
    }
}
