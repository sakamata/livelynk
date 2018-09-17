<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\AdminCommunity;

class AdminCommunityController extends Controller
{
    public function index(Request $request)
    {
        $items = 'App\AdminCommunity'::get();
        return view('admin_community.index', [
            'items' => $items,
        ]);
    }

    public function add(Request $request)
    {
        return view('admin_community.add', [

        ]);
    }

    public function create(Request $request)
    {
        // code...
    }

    public function edit(Request $request)
    {
        // code...
    }

    public function update(Request $request)
    {
        // code...
    }
}
