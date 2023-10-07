<?php

namespace App\Listeners;

use App\Events\NewsEvent;
use App\Models\BeritaLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NewsListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewsEvent $event): void
    {
        $berita = $event->berita;
        $logMessage = "Berita ini telah ". $event->action;
        // Log::info($logMessage);
        BeritaLog::create([
            'news_id' => $event->berita->id,
            'updated_by' => $event->berita->updated_by,
            'action' => $event->action,
            'log_message' => $logMessage,
        ]);
    }
}
