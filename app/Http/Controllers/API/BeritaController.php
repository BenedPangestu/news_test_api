<?php

namespace App\Http\Controllers\API;

// use App\Helper\ResponseBuilder;

// use App\Helper\ResponseBuilder;

use App\Events\NewsEvent;
use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Providers\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cek cache berita
        if (Cache::has('berita')) {
            $redis = Cache::get('berita');
        } else {
            // membuat cache berita
            $redis = Cache::remember('berita', now()->addMinute(5), function () {
                // ambil data dari db
                $dataDb = Berita::with(['create_by', 'update_by'])->paginate(5);
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
        $user = $request->user('api');
        $result = [];
        foreach (Berita::getTableColumns() as $key) {
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
        $result['created_by'] = $user->id;
        // dd($result);
        $insert = Berita::create($result);
        Cache::forget('berita');
        event(new NewsEvent($insert, 'create'));

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
        $data = Berita::with(['create_by', 'update_by', 'komentar'])->where('id', $id)->first();
        if ($data) {
            return ResponseBuilder::success(200, "Berhasil", $data, true, true);
        } else {
            return ResponseBuilder::success(200, "Gagal", [], false);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function edit(Berita $berita)
    {
        //
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
        $user = Berita::find($id);
        if (!$user) {
            return ResponseBuilder::success(404, "gagal", '');
        }
        $result = [];
        foreach (Berita::getTableColumns() as $key) {
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
        $dat = $request->user('api');
        $result['updated_by'] = $dat->id;
        
        $user->fill($result);
        $user->save();
        Cache::forget('berita');
        event(new NewsEvent($user, 'update'));

        return ResponseBuilder::success(200, "berhasil", $user, true, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Berita::find($id);
        if (!$data) {
            return ResponseBuilder::success(404, "gagal", '', false, true);
        } else {
            $data->delete();
            Cache::forget('berita');
            event(new NewsEvent($data, 'delete'));
            return ResponseBuilder::success(200, "gagal", '', true, true);
        }
    }
}
