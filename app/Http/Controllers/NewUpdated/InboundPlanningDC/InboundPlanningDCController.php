<?php

namespace App\Http\Controllers\NewUpdated\InboundPlanningDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Transaction\Stock\Ledger as stockLedger;

class InboundPlanningDCController extends Controller
{
    public function index()
    {
        $user = DB::table("users as a")
            ->select(
                "a.*",
                "b.role_name"
            )
            ->join("sm_role as b", "a.role_id", "b.id")
            ->where("a.username", Auth::user()->username)
            ->first();

        return view('new.InboundPlanningDC.index', compact('user'));
    }

    public function getListStock()
    {
        $principalList = Auth::user()->principal;
        $branchList = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();
        $stock = DB::table("iv_stock_ledger as a")
            ->select(
                "a.id",
                "b.product_code",
                "a.product_id",
                "a.planning",
                "b.product_name",
                "b.puom",
                DB::raw("SUM(CASE WHEN a.qtys > 0 THEN a.qtys ELSE 0 END) AS qtys"),
                DB::raw("GROUP_CONCAT(a.id) AS ledger_ids")
            )
            ->join("iv_product as b", "a.product_id", "b.id")
            ->whereIn("a.principal_id", $principalList)
            ->whereIn("a.branch_id", $branchList)
            ->groupBy("a.product_id")
            ->orderBy("a.planning", "DESC")
            // ->where("a.q", $branchList)
            ->get();
        return response()->json(['data' => $stock]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'plans' => 'required|array',
            'plans.*.week' => 'required|integer',
            'plans.*.ip' => 'required|integer',
        ]);
        try {
            DB::transaction(function () use ($validated) {

                $stock = DB::table('iv_stock_ledger')->where('id', $validated['id'])->first();

                DB::table('iv_stock_ledger')->where('id', $validated['id'])->update([
                    'planning' => json_encode($validated['plans'])
                ]);

                DB::table('iv_inbound_planning')->insert([
                    'principal_id' => $stock->principal_id,
                    'product_code' => $stock->product_id,
                    'product_id'   => $stock->product_id,
                    'planning'     => json_encode($validated['plans']),
                    'created_by'   => Auth::user()->username,
                    'created_at'   => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Data updated',
                'updated_at' => now()
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update record: ' . $e->getMessage()
            ], 500);
        }
    }


    public function downloadTemplate()
    {
        $principalList = Auth::user()->principal;
        $branchList = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();
        $stock = DB::table("iv_stock_ledger as a")
            ->select(
                "a.product_id",
                "a.product_code",
                "a.planning"
            )
            ->whereIn("a.principal_id", $principalList)
            ->whereIn("a.branch_id", $branchList)
            ->groupBy("a.product_id")
            ->orderBy("a.product_code", "asc")
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'product_id');
        $sheet->setCellValue('B1', 'product_code');
        $max = 4;

        $col = 'C';

        for ($i = 1; $i <= $max; $i++) {
            $sheet->setCellValue($col++ . '1', "ip_$i");
            $sheet->setCellValue($col++ . '1', "week_$i");
        }

        $row = 2;
        foreach ($stock as $item) {
            $sheet->setCellValue("A{$row}", $item->product_id);
            $sheet->setCellValue("B{$row}", $item->product_code);
            $colStart = 'C';
            $plans = [];
            if (!empty($item->planning)) {
                $plans = json_decode($item->planning, true);
            }
            for ($i = 0; $i < $max; $i++) {

                $ip = $plans[$i]['ip'] ?? '';
                $week = $plans[$i]['week'] ?? '';

                $sheet->setCellValue($colStart++ . $row, $ip);
                $sheet->setCellValue($colStart++ . $row, $week);
            }
            $row++;
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = "inbound-planning-template.xlsx";
        $filePath = storage_path("app/public/{$fileName}");

        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $principalList = Auth::user()->principal;

            $file = $request->file('file')->getPathname();
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $updates = [];
            $rowNumber = 1;

            foreach (array_slice($rows, 1) as $r) {
                $rowNumber++;

                $product_id   = trim($r[0]);
                $product_code = trim($r[1]);

                if ($product_id === "" || $product_id === null) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Product ID kosong pada baris {$rowNumber}. Proses dibatalkan!"
                    ], 422);
                }

                $exists = DB::table('iv_product')
                    ->where('id', $product_id)
                    ->whereIn('principal_id', $principalList)
                    ->exists();

                if (!$exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Product ID {$product_id} (baris {$rowNumber}) tidak ditemukan!"
                    ], 422);
                }

                $plans = [];
                for ($i = 2; $i < count($r); $i += 2) {

                    $ip = isset($r[$i]) ? trim($r[$i]) : null;
                    $week = isset($r[$i + 1]) ? trim($r[$i + 1]) : null;

                    if ($ip !== "" && $week !== "") {
                        $plans[] = [
                            'ip' => (int)$ip,
                            'week' => (int)$week
                        ];
                    }

                    if (($ip && !$week) || (!$ip && $week)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "IP & Week harus berpasangan (baris {$rowNumber})"
                        ], 422);
                    }
                }

                $updates[] = [
                    'principal_id' => $principalList->first(),
                    'product_code' => $product_code,
                    'product_id'   => $product_id,
                    'plans'        => $plans
                ];
            }

            DB::transaction(function () use ($updates) {
                foreach ($updates as $up) {
                    DB::table('iv_stock_ledger')
                        ->where('product_id', $up['product_id'])
                        ->update([
                            'planning' => json_encode($up['plans']),
                            'updated_at' => now()
                        ]);

                    DB::table('iv_inbound_planning')->insert([
                        'principal_id' => $up['principal_id'],
                        'product_code' => $up['product_code'],
                        'product_id'   => $up['product_id'],
                        'planning'     => json_encode($up['plans']),
                        'created_by'   => Auth::user()->username,
                        'created_at'   => now(),
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => "Excel berhasil diproses tanpa error!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Terjadi error: " . $e->getMessage()
            ], 500);
        }
    }
}
