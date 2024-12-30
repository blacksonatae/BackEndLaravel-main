<?php

namespace App\Http\Controllers;

use App\Models\Bunga;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


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
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            \Log::info('Token:', ['token' => env('BLOB_READ_WRITE_TOKEN')]);
            \Log::info('File Upload:', ['file' => $file->getClientOriginalName()]);


            // Unggah ke Vercel Blob
            /*return env('BLOB_READ_WRITE_TOKEN'). " ". $file->getClientOriginalName();*/
            /*$response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('BLOB_READ_WRITE_TOKEN'),
            ])->attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post('https://api.vercel.com/v1/blob');

            if ($response->successful()) {
                $blobData = $response->json();
                $validate['foto'] = $blobData['url']; // Simpan URL blob ke database
            } else {
                return response()->json(['error' => 'Failed to upload to Vercel Blob'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }*/
        }

        /*$result = Bunga::create($validate); //simpan ke tabel bunga
        if($result){
            $data['success'] = true;
            $data['message'] = "Bunga berhasil disimpan";
            $data['result'] = $result;
            return response()->json($data, Response::HTTP_CREATED);
        }*/

        return env('BLOB_READ_WRITE_TOKEN'). " ". $file->getClientOriginalName();
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
        $filePath = 'public/'. $bungas->foto;

        // Upload gambar baru
        $path = $request->file('foto')->store('images', 'public');
        $validate['foto'] = $path;

        // Hapus gambar lama
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }


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
