<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cron;

class CronController extends Controller
{

    public function index()
    {
        $prices = Cron::latest()->paginate(50);

        return view('Cron.index', ['prices' => $prices]);
    }

}