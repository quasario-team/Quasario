<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\JobCategory;
use App\Models\JobTitle;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    // GET /api/v1/lookups/registration : lists for the sign-up screen.
    public function registration(): JsonResponse
    {
        $categories = JobCategory::with('jobTitles')->orderBy('name')->get()
            ->map(fn (JobCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'job_titles' => $category->jobTitles->sortBy('name')->values()
                    ->map(fn (JobTitle $title) => ['id' => $title->id, 'name' => $title->name]),
            ]);

        return response()->json([
            'cities' => City::orderBy('name')->get(['id', 'name']),
            'job_categories' => $categories,
        ]);
    }
}
