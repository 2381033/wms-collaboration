<?php

namespace App\Http\Controllers\NewUpdated\import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use ZipArchive;
use Illuminate\Support\Str;

class FotoManagementController extends Controller
{

    private function getHeader()
    {
        $data = DB::table('imp_image_header')
            // ->whereYear('created_at', date('Y'))
            ->get();
        return $data;
    }

    private function getFoto($token)
    {
        $data = DB::table('imp_image_detail')
            // ->whereYear('created_at', date('Y'))
            ->whereIn('token', $token)
            ->get();
        return $data;
    }
    private function myBranch()
    {
        $data = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->value('branch_id');

        return $data;
    }
    public function outstanding()
    {
        $data = $this->getHeader()
            ->where('branch_id', $this->myBranch())
            ->where('confirmed_flag', 'No');
        $token = DB::table('imp_image_detail')
            ->select('token')
            ->whereIn('token', $data->pluck('token')->toArray())
            ->pluck('token')
            ->toArray();
        $data = $data->whereIn('token', $token);
        return view("new.Import.outstanding", compact('data'));
    }

    public function tracing()
    {
        return view("new.Import.tracing");
    }

    public function postTracing(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = $this->getHeader();
                if (!is_null($request->housebl)) {
                    $data = $data->where('housebl', Str::Upper($request->housebl));
                }
                if (!is_null($request->container_no)) {
                    $data = $data->where('container', Str::Upper($request->container_no));
                }
                if ($data->count() == 0) {
                    $message = ['data' => 'null'];
                } else {
                    $token = $data->pluck('token')->toArray();
                    $image = [];
                    $message = [
                        'data' => $data,
                        'foto' => $image
                    ];
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

    public function showfoto($token)
    {
        $exception = DB::transaction(function () use ($token) {
            try {
                $token = array($token);
                $image = $this->getFoto($token);
                $images = [];
                if (count($image) > 0) {
                    foreach ($image as $value) {
                        $filePath = public_path('foto/warehouse-import/foto-management/' . $value->file);
                        if (file_exists($filePath)) {
                            $images[] = [
                                'id' => $value->id,
                                'foto' => base64_encode(file_get_contents($filePath))
                            ];
                        }
                    }
                } else {
                    $images = [];
                }
                $message = [
                    'data' => $images
                ];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function deleteFoto($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                DB::table('imp_image_detail')
                    ->where('id', $id)
                    ->delete();
                DB::commit();
                $message = ['data' => 'success'];
                return $message;
            } catch (\Exception $e) {
                DB::rollBack();
                $message = ['error' => $e->getMessage()];
                return $message;
            }
        });
        return response()->json($exception);
    }

    public function downloadFoto($token)
    {
        $header = $this->getHeader()->where('token', $token)->first();
        $housebl = str_replace('/', '-', $header->housebl);
        $container = $header->container;
        $zip = new ZipArchive;
        $zipFileName = 'Housebl-' . $housebl . '-' . $container . '-.zip';
        $data = $this->getFoto([$token]);
        $foto = [];
        foreach ($data as $key => $value) {
            $foto[] = public_path('foto/warehouse-import/foto-management/' . $value->file);
        }
        if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE) {
            $filezip = Arr::collapse([$foto]);
            foreach ($filezip as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        } else {
            return "Failed to create the zip file.";
        }
        dd('test');
    }
    public function uploadFoto(Request $request)
    {
        foreach ($request->foto as $value) {
            $header =  $this->getHeader()->where('token', $request->token)->first();
            $filename = $request->token . "-" . Str::random(3) .  "." . $value->getClientOriginalExtension();
            if (!file_exists(public_path('foto/warehouse-import/foto-management' . $filename))) {
                $value->move(public_path('foto/warehouse-import/foto-management'), $filename);
            }
            DB::table('imp_image_detail')
                ->insert([
                    'file'   => $filename,
                    'token' => $header->token,
                    'masterbl' => $header->masterbl,
                    'housebl' => $header->housebl,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->username,
                ]);
        }
        Session::flash('success', 'Foto has been created..');
        return back();
    }
}
