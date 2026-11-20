<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\SaveLocationRequest;
use App\Http\Resources\LocationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    // GET /api/v1/me/profiles/{profile}/location : { data: {...} } or { data: null } when there is none.
    public function show(Request $request, int $profile): JsonResponse
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        $location = $model->location;

        return response()->json([
            'data' => $location ? (new LocationResource($location))->resolve($request) : null,
        ]);
    }

    // POST /api/v1/me/profiles/{profile}/location : add the location, or change it if it exists.
    // (POST and not PUT: PHP cannot read files sent with PUT.)
    public function save(SaveLocationRequest $request, int $profile): JsonResponse
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        $location = $model->location ?? $model->location()->make();
        $oldImage = $location->place_image;

        $location->fill([
            'latitude' => $request->validated('latitude'),
            'longitude' => $request->validated('longitude'),
            'directions' => $request->validated('directions'),
        ]);

        if ($request->hasFile('place_image')) {
            $location->place_image = $request->file('place_image')->store("profiles/{$model->id}/location", 'public');
        } elseif ($request->boolean('remove_place_image')) {
            $location->place_image = null;
        }

        $location->save();

        // Delete the old file only AFTER the new data is saved.
        if ($oldImage && $oldImage !== $location->place_image) {
            Storage::disk('public')->delete($oldImage);
        }

        // Always 200. (Laravel would send 201 by itself for a NEW row; one simple code is easier for React.)
        return (new LocationResource($location))->response()->setStatusCode(200);
    }

    // DELETE /api/v1/me/profiles/{profile}/location : remove the location and its picture.
    public function destroy(Request $request, int $profile): Response
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        $location = $model->location;

        if ($location) {
            $image = $location->place_image;
            $location->delete();
            if ($image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Deleting a location that does not exist is not an error: the result is the same.
        return response()->noContent();
    }
}
