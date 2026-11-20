<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateSocialLinksRequest;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialLinkController extends Controller
{
    // GET /api/v1/me/profiles/{profile}/links : the links of one of MY profiles.
    public function index(Request $request, int $profile): JsonResponse
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        return $this->linksOf($model);
    }

    // PUT /api/v1/me/profiles/{profile}/links : replace all the links at once.
    // Simple and safe: delete the old list, save the new list, in one transaction.
    public function update(UpdateSocialLinksRequest $request, int $profile): JsonResponse
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        $links = $request->validated('links');

        DB::transaction(function () use ($model, $links) {
            $model->socialLinks()->delete();
            $model->socialLinks()->createMany($links);
        });

        return $this->linksOf($model);
    }

    // The answer of both routes: {"data": [{"platform": "...", "url": "..."}]}
    private function linksOf(Profile $profile): JsonResponse
    {
        $links = $profile->socialLinks()->orderBy('id')->get(['platform', 'url']);

        return response()->json(['data' => $links]);
    }
}
