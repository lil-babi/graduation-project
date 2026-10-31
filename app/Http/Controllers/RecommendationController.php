<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(protected RecommendationService $recommendationService) {}

    public function index(): View
    {
        $user = Auth::user();
        $recommendations = $this->recommendationService->getRecommendations($user, 12);

        return view('recommendations.index', compact('recommendations'));
    }
}
