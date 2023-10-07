<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Komentar extends Model
{
    use HasFactory;
    protected $fillable = [
        "komentar",
        "user_id",
        "berita_id"
    ];

    public function berita()
    {
        return $this->belongsTo('App\Models\Berita', 'berita_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public static function getTableColumns()
    {
        return DB::getSchemaBuilder()->getColumnListing("komentars");
    }
}
