<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use App\Models\Division;
use Inertia\Inertia;

class DivisionController extends Controller
{
    public function show()
    {
        $Data = Division::all();

        return Inertia::render('Division', [
            'data' => $Data
        ]);
    }
}
