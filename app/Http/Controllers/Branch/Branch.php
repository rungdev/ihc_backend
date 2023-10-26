<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Branch extends Controller
{
    function branch() {
        $data = DB::table('branchs')
                ->where('branch_online', 'Y')
                ->where('active_status', 'Y')
                ->where('status', 'Y')
                ->get();
        return $data;
    }
}
