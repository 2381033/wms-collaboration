<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadImageController extends Controller
{
    public function upload(Request $request) {
        // $request->validate([
        //     'filename' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        $storage_path = 'public/uploads/cy';
        
        $image = $request->filename;  
        $image = str_replace('data:image/jpg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '.jpg';
        $filePath = $storage_path . '/' . $imageName;

        Storage::put($filePath, base64_decode($image));

        // Storage::disk('public')->put($imageName, base64_decode($image));
        
        return response()->json(["error"=>200, "message"=>"Berhasil"]);
    }
}