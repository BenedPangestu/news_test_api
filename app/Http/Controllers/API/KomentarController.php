<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Komentar;
use App\Providers\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KomentarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cek cache berita
        if (Cache::has('komentar')) {
            $redis = Cache::get('komentar');
        } else {
            // membuat cache berita
            $redis = Cache::remember('komentar', now()->addMinute(5), function () {
                // ambil data dari db
                $dataDb = Komentar::with(['create_by', 'update_by'])->paginate(5);
                return $dataDb;
            });
        }
        
        if ($redis) {
            return ResponseBuilder::success(200, "Berhasil", $redis, true, true);
        } else {
            return ResponseBuilder::error(500, "Gagal");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {   
        // dd($request->all());
        $result = [];
        foreach (Komentar::getTableColumns() as $key) {
            if ($request->file() != null && $request->file($key) != null) {
                if ($request->file($key)) {
                    $files = $request->file($key);
                    $filename = md5($files->getClientOriginalName());
                    if ($request->file('gambar')) {
                        $files->move('berita', $filename . '.' . $files->getClientOriginalExtension());
                    }

                    $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
                }
            } elseif ($request->input($key) != null) {
                if ($key == 'password') {
                    $result[$key] = md5($request->input($key));
                } else {
                    $result[$key] = $request->input($key);
                }
            } else {
                $result[$key] = null;
            }
        }
        $insert = Komentar::create($result);
        Cache::forget('komentar');

        if ($insert) {
            return ResponseBuilder::success(200, "Berhasil", $insert, true, true);
        } else {
            return ResponseBuilder::success(200, "Gagal", $insert, false, true);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Komentar::with(['create_by', 'update_by'])->where('id', $id)->first();
        if ($data) {
            return ResponseBuilder::success(200, "Berhasil", $data, true, true);
        } else {
            return ResponseBuilder::success(200, "Gagal", [], false);

        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Komentar::find($id);
        if (!$user) {
            return ResponseBuilder::success(404, "Gagal Insert Data", '');
        }
        $result = [];
        foreach (Komentar::getTableColumns() as $key) {
            if ($request->file() != null && $request->file($key) != null) {
                if ($request->file('gambar')) {
                    $files = $request->file($key);
                    $filename = md5($files->getClientOriginalName());
                    $files->move('berita', $filename . '.' . $files->getClientOriginalExtension());
                    $result[$key] = $filename . '.' . $files->getClientOriginalExtension();
                }
            } elseif ($request->input($key) != null) {
                if ($key == 'password') {
                    $result[$key] = md5($request->input($key));
                } else {
                    $result[$key] = $request->input($key);
                }
            }
        }
        // dd($request->all());
        $user->fill($result);
        $user->save();
        Cache::forget('komentar');

        return ResponseBuilder::success(200, "success", $user, true, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Komentar::find($id);
        if (!$data) {
            return ResponseBuilder::success(404, "Gagal", '', false, true);
        } else {
            $data->delete();
            Cache::forget('komentar');
            return ResponseBuilder::success(200, "Berhasil", '', true, true);
        }
    }
}
