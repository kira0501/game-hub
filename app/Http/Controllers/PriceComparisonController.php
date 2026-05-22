<?php

namespace App\Http\Controllers;

use App\Services\PriceComparisonService;

class PriceComparisonController extends Controller
{
    public function __invoke(PriceComparisonService $service)
    {
        return view('compare-prices', [
            'rows' => $service->table(),
        ]);
    }
}
