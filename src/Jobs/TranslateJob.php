<?php

namespace NicolaeSoitu\FilamentTranslations\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class TranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        public $userType;
        public $userId;
    public function __construct(
        public $phrases,
        public array $data,
        public string $translator,
    ) {
        $this->userType = auth()->user()->getMorphClass();
        $this->userId = auth()->user()->id;
        // dump($this->userType);
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->userType::find($this->userId);
        // dd($this->phrases);
        // $translation = Translation::whereIn('id', $this->phrases)->get();
        // dd($this->translator);
        $translation = (new $this->translator)->translateBulk($this->phrases, $this->data);
        // dump( $translation);
        // Notification::make()
        //     ->title(trans('filament-translations::translation.gpt_scan_notifications_done'))
        //     ->success()
        //     ->sendToDatabase($user);
    
    }
}
