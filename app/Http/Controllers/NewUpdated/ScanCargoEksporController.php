<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class ScanCargoEksporController extends Controller
{
    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');
        return $data;
    }

    private function getJobNo()
    {
        $branch = $this->myBranch();

        $job = DB::table('ex_scan_cargo')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'IN' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    private function getJobNoStuffing()
    {
        $branch = $this->myBranch();

        $job = DB::table('ex_scan_cargo')
            ->where('branch_id', $branch)
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->count();

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = $job + 1;
        }
        $job_no =  'OUT' . $branch . date('Y') . Str::of(date('m'))->padLeft(2, '0') . Str::of($increment)->padLeft(3, '0');

        return $job_no;
    }

    public function index()
    {
        return view("new.ScanCargoEkspor.dashboard");
    }


    public function stuffing()
    {
        return view("new.ScanCargoEkspor.stuffing");
    }
    private function saveBarcodeFile($fileName, $content)
    {
        $directory = public_path('jcpenny-barcode'); // Direktori tempat file akan disimpan
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); // Membuat folder jika belum ada
        }

        $filePath = $directory . '/' . $fileName;
        file_put_contents($filePath, $content);
    }

    public function submitReceiving(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $barcodes = $request->input('barcodes');
                $job_no = $this->getJobNo();

                foreach ($barcodes as $barcode) {
                    DB::table('ex_scan_cargo')->insert([
                        'pallet' => $barcode['pallet'],
                        'warehouse' => substr($barcode['kode_warehouse'], 2),
                        'carton' => substr($barcode['carton'], 2),
                        'branch_id'  => $this->myBranch(),
                        'job_no'  => $job_no,
                        // 'filename'  => $zipFileName,
                        'stock_flag'  => 'Yes',
                        'created_by'   => Auth::user()->username,
                        'created_at' => now(),
                    ]);
                }
                DB::commit();

                $message = [
                    'success' => 'success',
                ];
                return response()->json($message);
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });

        return response()->json($exception);
    }

    public function downloadReceiving($job_no)
    {
        $barcodes = DB::table('ex_scan_cargo')
            ->where('job_no', $job_no)
            ->orderBy('carton', 'ASC')
            ->groupBy('carton')
            ->get();
        $directory = public_path('jcpenny-barcode');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); // Membuat folder jika belum ada
        }

        $zip = new ZipArchive();
        $tahun = substr(date('Y'), -1);
        $bulan = date('m');
        $hari = date('d');
        $acak =  rand(10, 99);
        $zipFileName = 'J' . $tahun . $bulan . $hari . $acak . '.zip';
        $zipFilePath = $directory . '/' . $zipFileName;

        $file1 = 'SCAN.txt';
        $file2 = 'STUFFING.txt';
        $file3 = 'J' . $tahun . $bulan . $hari . $acak . '.txt';

        $fileContent = $this->generateTextFileContent($barcodes);
        $blankFile = $this->generateBlankContent();
        $summaryFile = $this->generateSummaryReceiving($barcodes);

        $this->saveBarcodeFile($file1, $fileContent);
        $this->saveBarcodeFile($file2, $blankFile);
        $this->saveBarcodeFile($file3, $summaryFile);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile(public_path('jcpenny-barcode/' . $file1), $file1);
            $zip->addFile(public_path('jcpenny-barcode/' . $file2), $file2);
            $zip->addFile(public_path('jcpenny-barcode/' . $file3), $file3);

            $zip->close();
        } else {
            throw new \Exception('Failed to create ZIP file.');
        }
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    private function generateTextFileContent($barcodes)
    {
        $content = '';
        foreach ($barcodes as $barcode) {
            $pallet = $barcode->pallet;
            $cartonNumber = str_pad($barcode->carton, 6, '0', STR_PAD_LEFT);
            $addedCartonNumber = $barcode->warehouse;
            $cartonWithHardCode = $pallet . str_repeat(' ', 15) . $addedCartonNumber . str_repeat(' ', 52); // Container + angka hardcode + spasi yang diperlukan
            $date = date('Ymd'); // Format: Ymd (misalnya: 20250212)
            $line = $cartonWithHardCode . '0   ' . $barcode->carton . $date . 'SHHT' . "\n";
            $content .= $line;
        }
        return $content;
    }

    private function generateBlankContent()
    {
        return '';
    }

    private function generateSummaryReceiving($barcodes)
    {
        $cartonCountPerItemCode = [];

        // Menghitung jumlah carton berdasarkan 10 karakter pertama dari kode pallet
        foreach ($barcodes as $barcode) {
            $pallet = $barcode->pallet;
            $itemCode = substr($pallet, 0, 10); // Ambil 10 karakter pertama

            if (!isset($cartonCountPerItemCode[$itemCode])) {
                $cartonCountPerItemCode[$itemCode] = 1;
            } else {
                $cartonCountPerItemCode[$itemCode]++;
            }
        }

        $content = '';
        $lineNumber = 1;

        ksort($cartonCountPerItemCode);
        foreach ($cartonCountPerItemCode as $itemCode => $quantity) {
            $description = 'Standard Hand Held'; // Deskripsi produk

            $line = str_pad($lineNumber, 8, ' ', STR_PAD_LEFT) .  // 7 spasi sebelum nomor urut
                ' ' . // 1 spasi setelah nomor urut
                str_pad($itemCode, 10, ' ', STR_PAD_RIGHT) . // Kode pallet (10 karakter)
                str_pad(' ', 10, ' ', STR_PAD_RIGHT) . // 10 spasi kosong
                str_pad($description, 32, ' ', STR_PAD_RIGHT) . // Deskripsi (32 karakter)
                str_pad($quantity, 19, ' ', STR_PAD_LEFT) . "\n"; // Qty (19 spasi kanan)

            $content .= $line;
            $lineNumber++;
        }

        return $content;
    }


    public function submitStuffing(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $barcodes = $request->input('barcodes');
                $job_no = $this->getJobNoStuffing();

                foreach ($barcodes as $barcode) {
                    $container = $barcode['container'];
                    $pallet = $barcode['pallet'];
                    foreach ($pallet as $item) {
                        DB::table('ex_scan_cargo')->insert([
                            'branch_id'  => $this->myBranch(),
                            'job_no'  => $job_no,
                            // 'filename'  => $zipFileName,
                            'created_by'   => Auth::user()->username,
                            'created_at' => now(),
                            'container_no' => $container,
                            'pallet' => $item,
                        ]);
                        DB::table('ex_scan_cargo')->where('pallet', $item)
                            ->update([
                                'stock_flag'  => 'No',
                                'updated_by' => Auth::user()->username,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
                DB::commit();
                $message = [
                    'success' => 'success',
                ];
                return response()->json($message);
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function downloadStuffing($job_no)
    {
        $barcodes = DB::table('ex_scan_cargo')
            ->where('job_no', $job_no)
            ->whereNotNull('container_no')
            ->orderBy('pallet', 'ASC')
            ->groupBy('pallet')
            ->get();
        $directory = public_path('jcpenny-barcode');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true); // Membuat folder jika belum ada
        }

        $zip = new ZipArchive();
        $tahun = substr(date('Y'), -1);
        $bulan = date('m');
        $hari = date('d');
        $acak =  rand(10, 99);
        $zipFileName = 'J' . $tahun . $bulan . $hari . $acak . '.zip';
        $zipFilePath = $directory . '/' . $zipFileName;

        $file1 = 'STUFFING.txt';
        $file2 = 'SCAN.txt';
        $file3 = 'J' . $tahun . $bulan . $hari . $acak . '.txt';

        $fileContent = $this->generateTextFileContentStuffing($barcodes);
        $blankFile = $this->generateBlankContent();
        $summaryFile = $this->generateSummaryStuffing($barcodes);

        $this->saveBarcodeFile($file1, $fileContent);
        $this->saveBarcodeFile($file2, $blankFile);
        $this->saveBarcodeFile($file3, $summaryFile);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile(public_path('jcpenny-barcode/' . $file1), $file1);
            $zip->addFile(public_path('jcpenny-barcode/' . $file2), $file2);
            $zip->addFile(public_path('jcpenny-barcode/' . $file3), $file3);

            $zip->close();
        } else {
            throw new \Exception('Failed to create ZIP file.');
        }
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
    private function generateTextFileContentStuffing($barcodes)
    {
        $content = '';

        foreach ($barcodes as $barcode) {
            $string = $barcode->container_no;
            $lastTenDigits = substr($barcode->pallet, -10);
            $currentDate = date('Ymd');
            $line = $string . $lastTenDigits . str_repeat(' ', 8) . $currentDate . "\n";
            $content .= $line;
        }

        return $content;
    }

    private function generateSummaryStuffing($barcodes)
    {
        $content = '';
        $lineNumber = 1; // Nomor urut untuk baris
        $palletCount = []; // Array untuk menyimpan jumlah carton per pallet

        // Loop untuk setiap barcode yang berisi pallet
        foreach ($barcodes as $barcode) {
            $cartonCount = DB::table('ex_scan_cargo')
                ->where('job_no', $barcode->job_no)
                ->groupBy('id')
                ->get()->count();
        }

        $totalLines = $cartonCount;
        $content .= str_repeat(' ', 7) . // 7 spasi
            $lineNumber . // Nomor urut
            ' ' . // 1 spasi
            '*STUFFING*' . // Teks *STUFFING*
            str_repeat(' ', 57) . // 57 spasi
            $totalLines . "\n"; // Angka 55 dan newline

        return $content; // Kembalikan hasil akhir
    }


    public function list()
    {
        return view('new.ScanCargoEkspor.Scan.index');
    }

    public function getListReceive(Request $request)
    {
        $start = $request['start'];
        $end = $request['end'];
        $data = DB::table('ex_scan_cargo')
            ->orderBy('id', 'ASC')
            ->where('branch_id', $this->myBranch())
            ->whereNull('container_no')
            ->whereBetween(\DB::raw('DATE(created_at)'), [$start, $end])
            ->groupBy('job_no')
            ->get();

        return datatables()->of($data)->make(true);
    }

    public function getListStuffing(Request $request)
    {
        $start = $request['start'];
        $end = $request['end'];
        $data = DB::table('ex_scan_cargo')
            ->orderBy('id', 'ASC')
            ->where('branch_id', $this->myBranch())
            ->whereNotNull('container_no')
            ->whereBetween(\DB::raw('DATE(created_at)'), [$start, $end])
            ->groupBy('job_no')
            ->get();

        return datatables()->of($data)->make(true);
    }

    public function printPalletTag()
    {
        return view('new.ScanCargoEkspor.Scan.print-pallet-tag');
    }

    public function doPrint(Request $request)
    {
        $tahun = substr(date('Y'), -2);
        $bulan = date('m');
        $hari = date('d');

        $barcodes = [];

        // DPI
        $dpi = 150;
        $pixelsPerMm = $dpi / 25.4;
        $moduleWidthMm = 0.5;
        $moduleHeightMm = 15;

        $barHeightPx = (int)($moduleHeightMm * $pixelsPerMm);
        $moduleWidthPx = (int)($moduleWidthMm * $pixelsPerMm);

        $fontHeight = 20;

        $generator = new BarcodeGeneratorPNG();

        for ($i = (int)$request->start; $i <= (int)$request->end; $i++) {
            $nomor = str_pad($i, 4, '0', STR_PAD_LEFT);
            $kode = $request->pallet_tag . $tahun . $bulan . $hari . $nomor;

            // Buat barcode PNG
            $barcode = $generator->getBarcode($kode, $generator::TYPE_CODE_128, $moduleWidthPx, $barHeightPx);

            // Encode base64 untuk dimasukkan ke <img src="data:image/png;base64,...">
            $base64 = base64_encode($barcode);

            $barcodes[] = [
                'kode' => $kode,
                'image_base64' => $base64
            ];
        }

        // Kirim ke view HTML
        $pdf = Pdf::loadView('new.ScanCargoEkspor.Scan.pallet-tag-pdf', [
            'barcodes' => $barcodes
        ])->setPaper([0, 0, 283.46, 141.73]); // 100mm x 50mm dalam pt (1 pt = 1/72 inch)

        return $pdf->download($request->pallet_tag . '.pdf');
    }

    public function getPalletStuffing($pallet)
    {
        $palletArray = explode(',', $pallet);
        $data = DB::table('ex_scan_cargo')
            ->where('branch_id', $this->myBranch())
            ->whereIn('pallet', $palletArray)
            ->get();
        $counting = $data->count();
        $qtyPerPallet = $data->groupBy('pallet')->map(function ($items, $pallet) {
            return [
                'pallet' => $pallet,
                'line_count' => $items->count()
            ];
        })->values();
        return response()->json(['data' => [
            'counting' => $counting,
            'qty_per_pallet' => $qtyPerPallet,
        ]]);
    }
}
