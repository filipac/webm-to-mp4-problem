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
use Symfony\Component\Process\Process;

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

        $format->setAdditionalParameters([
            '-profile:v', 'baseline',
            '-level:v', '3.1',
            '-movflags', 'faststart',
            '-vsync', 'vfr',
        ]);

        $fname = pathinfo($this->video->local_path, PATHINFO_FILENAME);

        $old = $this->video->local_path;

        # run file through ts-ebml first
        $ebmlPath = base_path('node_modules/.bin/');
        $path = storage_path('app/public/'.$old);
        $dir = dirname($path);
        $process = Process::fromShellCommandline($ebmlPath.'/ts-ebml -s "$VAR1" | cat > "$VAR2"', $dir);
        $process->start(null, ['VAR1' => $path, 'VAR2' => $dir.'/'.$fname.'-converted.webm']);
        $process->wait();

        Storage::delete('public/'.$old);

        FFMpeg::fromDisk('local')
            ->open('public/uploaded_videos/'.$fname.'-converted.webm')
            ->export()
            ->toDisk('local')
            ->inFormat($format)
            ->save($path = 'public/uploaded_videos/'.$fname.'.mp4');

        $this->video->local_path = 'uploaded_videos/'.$fname.'.mp4';
        $this->video->save();
        Storage::delete('public/uploaded_videos/'.$fname.'-converted.webm');
    }
}
