<?php

namespace App\Models;

use App\Events\NewsEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Berita extends Model
{
    use HasFactory;
    protected $fillable = [
        "judul",
        "gambar",
        "deskripsi",
        "log_message",
        "created_by",
        "updated_by",
    ];

    protected $dispatchesEvents = [
        'create' => NewsEvent::class,
        'update' => NewsEvent::class,
        'delete' => NewsEvent::class,
    ];
    public function create_by()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    public function update_by()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
    public function komentar()
    {
        return $this->hasMany('App\Models\Komentar', 'berita_id', 'id');
    }
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing("beritas");
    }
}
