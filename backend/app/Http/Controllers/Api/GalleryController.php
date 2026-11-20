<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DomainError;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreGalleryImageRequest;
use App\Http\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // GET /api/v1/me/profiles/{profile}/gallery : my gallery, the newest picture first.
    public function index(Request $request, int $profile): AnonymousResourceCollection
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        return GalleryImageResource::collection($model->galleryImages()->orderByDesc('id')->get());
    }

    // POST /api/v1/me/profiles/{profile}/gallery : add one picture (at most 30 per profile).
    public function store(StoreGalleryImageRequest $request, int $profile): JsonResponse
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        $image = DB::transaction(function () use ($request, $model) {
            // Lock the profile row: two uploads at the same time cannot pass the limit together.
            Profile::whereKey($model->id)->lockForUpdate()->first();

            if ($model->galleryImages()->count() >= GalleryImage::MAX_PER_PROFILE) {
                throw new DomainError('galleryFull', 409, 'A gallery can have at most 30 pictures.');
            }

            return $model->galleryImages()->create([
                'image_path' => $request->file('image')->store("profiles/{$model->id}/gallery", 'public'),
                'description' => $request->validated('description'),
            ]);
        });

        return (new GalleryImageResource($image))->response()->setStatusCode(201);
    }

    // DELETE /api/v1/me/profiles/{profile}/gallery/{image} : remove one picture and its file.
    public function destroy(Request $request, int $profile, int $image): Response
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        // Looking inside THIS profile's gallery: a picture of another profile gives 404.
        $picture = $model->galleryImages()->findOrFail($image);

        $path = $picture->image_path;
        $picture->delete();
        Storage::disk('public')->delete($path);

        return response()->noContent();
    }
}
