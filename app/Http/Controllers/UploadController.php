<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertWebmToMp4;
use App\Models\Video;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __invoke()
    {
        $path = request()->file('file')->store('public/uploaded_videos');

        $video = new Video;
        $video->local_path = str_replace('public/', '', $path);
        $video->save();

        dispatch(new ConvertWebmToMp4($video));

        return $video;
    }
}
