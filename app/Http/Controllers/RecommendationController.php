<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __invoke(Request $request, RecommendationService $service)
    {
        return view('recommendations', [
            'recommendations' => $service->forUser($request->user(), 18),
        ]);
    }
}
