<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use ZipArchive;
use File;
use Illuminate\Support\Facades\Session;
use Smalot\PdfParser\Parser;

class TaxController extends Controller
{
    public function home()
    {
        return view('new.tax.home');
    }

    public function login(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $users = DB::table('users')
                    ->where('username', 'pajak')
                    ->value('password');
                if (Hash::check($request->password, $users)) {
                    $message = ['status' => 'success', 'token' => $users];
                } else {
                    $message = ['status' => 'error'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function index()
    {
        return view('new.tax.index');
    }

    public function uploadzip(Request $request)
    {
        DB::transaction(function () use ($request) {
            try {
                if ($request->file('file')) {
                    $file = $request->file('file');
                    $fileName = $file->getClientOriginalName();
                    $tempPath = public_path('uploads/');
                    if (!File::exists($tempPath)) {
                        File::makeDirectory($tempPath, 0755, true);
                    }
                    $file->move($tempPath, $fileName);
                    $zipFilePath = $tempPath . $fileName;
                    $extractPath = public_path('tax/pdf');

                    $zip = new ZipArchive;
                    if ($zip->open($zipFilePath) === TRUE) {
                        // Daftar untuk menyimpan nama file PDF yang diekstrak
                        $extractedFiles = [];

                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $fileInZip = $zip->getNameIndex($i); // Ambil nama file dalam ZIP

                            // Ekstrak file satu per satu hanya jika itu PDF
                            if (strtolower(pathinfo($fileInZip, PATHINFO_EXTENSION)) === 'pdf') {
                                $zip->extractTo($extractPath, $fileInZip); // Ekstrak hanya file PDF
                                $extractedFiles[] = $fileInZip; // Tambahkan ke daftar file yang diekstrak
                            }
                        }

                        $zip->close();
                        // Hapus file ZIP setelah diekstrak
                        File::delete($zipFilePath);

                        // Proses file PDF yang baru saja diekstrak
                        $pdfNames = [];
                        $parser = new Parser();
                        $pattern = '/Referensi:\s*([A-Za-z0-9]+)/';
                        $kw = [];

                        // Hanya proses file yang baru diekstrak
                        foreach ($extractedFiles as $index => $fileInZip) {
                            $pdfFile = $extractPath . '/' . $fileInZip;
                            $pdf = $parser->parseFile($pdfFile);
                            $text = $pdf->getText();
                            // $dump[] = preg_match($pattern, $text, $matches);
                            if (file_exists($pdfFile) && strtolower(pathinfo($pdfFile, PATHINFO_EXTENSION)) === 'pdf') {
                                $pdf = $parser->parseFile($pdfFile);
                                $text = $pdf->getText();
                                preg_match($pattern, $text, $matches);
                                if (isset($matches[1])) {
                                    $kw[] = $matches[1];
                                } else {
                                    Session::flash('error', 'Network error, please try again..');
                                    return back();
                                }
                                $pdfNames[] = $fileInZip;
                            }
                        }

                        // Menyimpan data PDF yang baru diekstrak ke dalam database
                        foreach ($pdfNames as $key => $pdfName) {
                            DB::table('fin_tax')->insert([
                                'file' => $pdfName,
                                'kw' => $kw[$key],
                                'created_at' => now(),
                                'updated_at' => now(),  // Menyimpan waktu update
                            ]);
                        }
                        DB::commit();
                    }
                } else {
                    echo "file tidak ada";
                }
                Session::flash('success', 'Data has been saved successfully');
                return back();
            } catch (\Exception $e) {
                DB::rollBack();
                Session::flash('error', 'Ada Kesalahan, silahkan hub IT..');
                return back();
            }
        });
        return back();
    }

    public function getList()
    {
        $data = DB::table('fin_tax')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->get();
        return datatables()->of($data)
            ->addColumn('npwp', function ($data) {
                $fileWithoutExtension = str_replace(".pdf", "", $data->file);
                $npwp = explode("-", $fileWithoutExtension);
                return $npwp[8];
            })
            ->addColumn('fp', function ($data) {
                $fileWithoutExtension = str_replace(".pdf", "", $data->file);
                $fp = explode("-", $fileWithoutExtension);
                return $fp[7];
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function tracking()
    {
        return view('new.tax.tracking');
    }
    public function postTracking(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = DB::table('fin_tax')
                    ->where('kw', 'LIKE', '%' . $request->kw . '%')
                    ->get();
                if ($data->count() > 0) {
                    $result = null;
                    $result = $data->filter(function ($item) use ($request) {
                        return str_contains($item->file, $request->npwp); // Memfilter berdasarkan npwp
                    });
                    if ($result->count() > 0) {
                        $message = ['data' => $result->first()];
                    } else {
                        $message = ['data' => 'null'];
                    }
                } else {
                    $message = ['data' => 'null'];
                }
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }
}
