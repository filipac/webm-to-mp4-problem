<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class View extends Controller
{
    public function __invoke(Video $video)
    {
        return view('view', ['video' => $video]);
    }
}
