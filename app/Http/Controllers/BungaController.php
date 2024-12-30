<?php

namespace App\Http\Controllers;

use App\Models\Bunga;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BungaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bunga = Bunga::all();
        $data['success'] = true;
        $data['message'] = "Data Bunga";
        $data['result'] = $bunga;
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'foto' => 'required|file|max:5000',
            'nama_bunga' => 'required',
            'deskripsi' => 'required|max:10000'
        ]);

        if($request->hasFile('foto')) {
            $uploadedFile = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'uploads/bunga',
            ]);

            // Ambil URL aman dan public_id untuk file yang diunggah
            $validate['foto'] = $uploadedFile->getSecurePath(); // URL aman
            $filePublicId = $uploadedFile->getPublicId();       // Public ID
        }

        $result = Bunga::create($validate); //simpan ke tabel bunga
        if($result){
            $data['success'] = true;
            $data['message'] = "Bunga berhasil disimpan";
            $data['result'] = $result;
            return response()->json($data, Response::HTTP_CREATED);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bunga $bunga)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bunga $bunga)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'foto' => 'required|file|max:5000',
            'nama_bunga' => 'required',
            'deskripsi' => 'required|max:10000'
        ]);

        $bungas = Bunga::find($id);
        
        if (!$bungas) {
            return response()->json([
                'success' => false,
                'message' => 'Data bunga tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        // Hapus gambar lama di Cloudinary jika ada
        if ($bungas->foto) {
            $publicId = basename($bungas->foto, '.' . pathinfo($bungas->foto, PATHINFO_EXTENSION));
            Cloudinary::destroy($publicId);
        }

        // Upload gambar baru ke Cloudinary
        $uploadedFile = Cloudinary::upload($request->file('foto')->getRealPath(), [
            'folder' => 'uploads/bunga',
        ]);

        $validate['foto'] = $uploadedFile->getSecurePath(); // URL file baru



        $result = Bunga::where('id', $id)->update($validate);

        if($result){
            $data['success'] = true;
            $data['message'] = "Data bunga berhasil diupdate";
            $data['result'] = $result;
            return response()->json($data, Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bunga $bunga)
    {
        $bunga = Bunga::find($bunga->id);

        $filePath = 'public/'. $bunga->foto;

        if($bunga){
            // Hapus gambar lama
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            $bunga->delete();
            $data["success"] = true;
            $data["message"] = "Data bunga berhasil dihapus";
            return response()->json($data, Response::HTTP_OK);
        }else {
            $data["success"] = false;
            $data["message"] = "Data bunga tidak ditemukan";
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }
    }
}
