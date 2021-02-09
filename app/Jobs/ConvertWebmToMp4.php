<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertWebmToMp4 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Video $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $format = new \FFMpeg\Format\Video\X264('aac');

        $fname = pathinfo($this->video->local_path, PATHINFO_FILENAME);

        $old = $this->video->local_path;

        FFMpeg::fromDisk('local')
            ->open('public/'.$this->video->local_path)
            ->export()
            ->toDisk('local')
            ->inFormat($format)
            ->save($path = 'public/uploaded_videos/'.$fname.'.mp4');

        $this->video->local_path = 'uploaded_videos/'.$fname.'.mp4';
        $this->video->save();

        Storage::delete('public/'.$old);
    }
}
