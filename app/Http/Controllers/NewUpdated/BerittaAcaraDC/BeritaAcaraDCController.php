<?php

namespace App\Http\Controllers\NewUpdated\BerittaAcaraDC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BeritaAcaraDCController extends Controller
{

    private function getMyPrincipal()
    {
        $data = DB::table('users_principal as a')
            ->join('iv_principal', 'a.principal_id', '=', 'iv_principal.id')
            ->where('a.user_id', Auth::user()->id)
            ->get();

        return $data;
    }

    private function myBranch()
    {
        $branch = DB::table('sm_user_branch as a')
            ->join('mt_branch as b', 'a.branch_id', '=', 'b.id')
            ->where('a.user_id', Auth::user()->id)
            ->where('b.active', 'Yes')
            ->get();

        return $branch;
    }
    public function index()
    {
        $principal = $this->getMyPrincipal();
        $branch = $this->myBranch();
        return view('new.BeritaAcaraDC.index', compact('principal', 'branch'));
    }
    public function store(Request $request)
    {
        $rules = [
            'tanggal_temuan' => 'required|date',
            'kategori' => 'required',
            'sub_kategori' => 'required',
            'branch' => 'required',
            'principal' => 'required',
            'kronologis' => 'required',
            'tempat_kejadian' => 'required',
            'solusi' => 'required',
            'qc' => 'required',
            'posisi_qc' => 'required',
            'mengetahui' => 'required',
            'posisi_mengetahui' => 'required',
            'pj' => 'required',
            'posisi_pj' => 'required',
            'ttd_pihak2' => 'required',
            'file' => 'required',
            'file.*' => 'mimes:jpg,jpeg,png|max:2048'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $baId = DB::table('iv_ba')->insertGetId([
                'no_doc' => $this->generateNoDoc($request->branch),
                'branch_id' => $request->branch,
                'tanggal_temuan' => $request->tanggal_temuan,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'no_manual' => $request->no_manual,
                'no_reff' => $request->no_reff,
                'principal' => $request->principal,
                'kronologis' => $request->kronologis,
                'solusi' => $request->solusi,
                'tempat_kejadian' => $request->tempat_kejadian,
                'created_by' => Auth::user()->name,
                'qc' => $request->qc,
                'posisi_qc' => $request->posisi_qc,
                'mengetahui' => $request->mengetahui,
                'posisi_mengetahui' => $request->posisi_mengetahui,
                'pj' => $request->pj,
                'posisi_pj' => $request->posisi_pj,
                'ttd_pihak2' => $request->ttd_pihak2,
                'created_at' => now()
            ]);

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . $originalName;
                    $file->move(public_path('foto/berta-acara-dc/'), $fileName);
                    DB::table('iv_ba_files')->insert([
                        'job_id' => $baId,
                        'file_path' => 'foto/berta-acara-dc/' . $fileName,
                        'file_name' => $originalName,
                        'created_at' => now()
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil disimpan',
                'id' => $baId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => [$e->getMessage()]
            ], 500);
        }
    }
    public function print($id)
    {
        $ba = DB::table('iv_ba')->where('id', $id)->first();
        $files = DB::table('iv_ba_files')->where('job_id', $id)->get();
        return view('new.BeritaAcaraDC.print', compact('ba', 'files'));
    }

    private function generateNoDoc($branch)
    {
        $branchMap = [
            1 => 'JKT',
            2 => 'SMG',
            3 => 'SUB',
            4 => 'BLW',
            5 => 'MKS',
        ];
        $branch = $branchMap[$branch] ?? 'JKT';

        $prefix = 'BA/MKT-' . $branch . '/';
        $year = date('y');
        $month = date('m');
        $base = $year . $month;
        $last = DB::table('iv_ba')
            ->where('no_doc', 'like', $prefix . $base . '%')
            ->where('branch_id', $branch)
            ->orderBy('no_doc', 'desc')
            ->first();
        if ($last) {
            $lastNumber = substr($last->no_doc, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        return $prefix . $base . $nextNumber;
    }

    public function filter(Request $request)
    {
        $query = DB::table('iv_ba');
        if ($request->date_from) {
            $query->whereDate('tanggal_temuan', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('tanggal_temuan', '<=', $request->date_to);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        $data = $query->orderBy('id', 'desc')->get();

        return response()->json($data);
    }
}
