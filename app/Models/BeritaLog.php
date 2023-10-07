<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaLog extends Model
{
    use HasFactory;
    protected $fillable = [
        "news_id",
        "updated_by",
        "action",
        "log_message",
    ];

    protected $dispatchesEvents = [
        'create' => NewsEvent::class,
        'update' => NewsEvent::class,
        'delete' => NewsEvent::class,
    ];
    public function update_by()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
