<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('dashboard', [
            'activities' => Activity::latest()->take(10)->get(),
        ]);
    }
}
