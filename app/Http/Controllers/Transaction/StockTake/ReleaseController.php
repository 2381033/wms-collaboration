<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\StockTake\Job as StockTakeJob;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Hashids\Hashids;

class ReleaseController extends Controller
{
    public function index(Request $request)
    {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->take_id) && !empty($request->take_id)) {
                $details = DB::table("iv_stocktake_detail as a")
                    ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->leftjoin("iv_site as c", "a.site_id", "c.id")
                    ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                    ->where("a.stocktake_id", $request->take_id)
                    ->where("a.confirmed_flag", "Yes")
                    ->orderBy('notes', 'DESC')
                    ->get();
            }

            return datatables()->of($details)
                ->editColumn('mfg_date', function ($data) {
                    $mfg_date = "";
                    if (isset($data->mfg_date)) {
                        $mfg_date = date('d/m/Y', strtotime($data->mfg_date));
                    }
                    return $mfg_date;
                })
                ->editColumn('exp_date', function ($data) {
                    $exp_date = "";
                    if (isset($data->exp_date)) {
                        $exp_date = date('d/m/Y', strtotime($data->exp_date));
                    }
                    return $exp_date;
                })
                ->editColumn('actual_mfg_date', function ($data) {
                    $actual_mfg_date = "";
                    if (isset($data->actual_mfg_date)) {
                        $actual_mfg_date = date('d/m/Y', strtotime($data->actual_mfg_date));
                    }
                    return $actual_mfg_date;
                })
                ->editColumn('actual_exp_date', function ($data) {
                    $actual_exp_date = "";
                    if (isset($data->actual_exp_date)) {
                        $actual_exp_date = date('d/m/Y', strtotime($data->actual_exp_date));
                    }
                    return $actual_exp_date;
                })
                ->addColumn("check", function ($data) {
                    return "<input type='checkbox' required='required' name='release_id[]' class='release-check' data-id='" . $data->id . "' value='" . $data->id . "'>";
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function submit(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $data = $request->release_id;

                if (empty($data) || !is_array($data)) {
                    abort(400, 'release_id tidak valid');
                }

                $details = StockTakeDetail::select('id', 'product_code', 'qty', 'location_code')
                    ->whereIn('id', $data)
                    ->orderBy('location_code', 'ASC')
                    ->get();

                if ($details->isEmpty()) {
                    abort(404, 'Data tidak ditemukan.');
                }

                $hashids = new Hashids(config('app.key'), 8); // panjang hash 8 karakter

                $details->each(function ($item) use ($hashids) {
                    $item->encrypted_id = $hashids->encode($item->id);
                });

                // Bagi menjadi batch per 100
                $batches = $details->chunk(100);

                // Path zip
                $zipFileName = 'barcode_batches.zip';
                $zipPath = storage_path("app/public/{$zipFileName}");

                // Pastikan folder temp ada
                Storage::makeDirectory('temp');

                $zip = new ZipArchive;
                if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                    throw new \Exception("Gagal membuat file ZIP.");
                }

                foreach ($batches as $index => $batch) {
                    $pdf = Pdf::loadView('transaction.stocktake.barcode_sto', [
                        'list_data' => $batch
                    ])->setPaper([0, 0, 425.2, 283.5], 'portrait');

                    $batchFileName = "barcode_batch_" . ($index + 1) . ".pdf";
                    $pdfPath = storage_path("app/temp/{$batchFileName}");

                    // Simpan PDF sementara ke storage/temp
                    Storage::put("temp/{$batchFileName}", $pdf->output());

                    // Masukkan file ke ZIP
                    $zip->addFile(storage_path("app/temp/{$batchFileName}"), $batchFileName);
                }

                $zip->close();

                // Bersihkan file PDF temp
                Storage::deleteDirectory('temp');

                // Kirim file ZIP sebagai download
                return response()->download($zipPath)->deleteFileAfterSend(true);
            } catch (\Exception $e) {
                DB::rollBack(); // walaupun PDF tidak ubah DB, ini menjaga konsistensi
                return response()->json(["error" => $e->getMessage()], 500);
            }
        });
    }
}
