<?php

namespace App\Http\Controllers\API\StayInfo;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StayInfo\PostRequest;

class MailFetchController extends Controller
{
    public function post(PostRequest $request)
    {
        return $request->all();
    }
}
