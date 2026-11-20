<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadProfileImagesRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // GET /api/v1/me/profiles : all profiles of my account, the active one first.
    public function index(Request $request): AnonymousResourceCollection
    {
        $profiles = $request->user()->profiles()
            ->with(['jobTitle.jobCategory', 'city'])
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->get();

        return ProfileResource::collection($profiles);
    }

    // POST /api/v1/me/profiles : a new profile, NOT active (the user switches to it later).
    public function store(StoreProfileRequest $request, ProfileService $profiles): JsonResponse
    {
        // job_category_id is only used to check the title; profiles have no such column.
        $data = $request->safe()->except('job_category_id');

        $profile = $profiles->create($request->user(), $data, active: false);
        $profile->load(['jobTitle.jobCategory', 'city']);

        return (new ProfileResource($profile))->response()->setStatusCode(201);
    }

    // PATCH /api/v1/me/profiles/{profile} : change only the sent fields.
    // Looking inside MY profiles means: another account's profile gives 404 notFound.
    public function update(UpdateProfileRequest $request, int $profile): ProfileResource
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        $model->update($request->safe()->except('job_category_id'));

        return new ProfileResource($model->load(['jobTitle.jobCategory', 'city']));
    }

    // DELETE /api/v1/me/profiles/{profile} : the rules (last profile, new active one) are in ProfileService.
    public function destroy(Request $request, int $profile, ProfileService $profiles): Response
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        $profiles->delete($request->user(), $model);

        return response()->noContent();
    }

    // POST /api/v1/me/profiles/{profile}/activate : this profile becomes the active one.
    public function activate(Request $request, int $profile, ProfileService $profiles): ProfileResource
    {
        $model = $request->user()->profiles()->findOrFail($profile);

        $profiles->activate($request->user(), $model);

        return new ProfileResource($model->load(['jobTitle.jobCategory', 'city']));
    }

    // POST /api/v1/me/profiles/{profile}/images : upload the profile picture and/or the cover picture.
    // The files go to storage/app/public/profiles/{id}/ ; the old picture is deleted.
    public function images(UploadProfileImagesRequest $request, int $profile): ProfileResource
    {
        $model = $request->user()->profiles()->findOrFail($profile);
        $oldFiles = [];

        foreach (['profile_image', 'cover_image'] as $field) {
            if ($request->hasFile($field)) {
                $oldFiles[] = $model->{$field};
                // store() gives the file a random name, so two uploads never clash.
                $model->{$field} = $request->file($field)->store("profiles/{$model->id}", 'public');
            }
        }

        $model->save();

        // Delete the old pictures only after the new ones are saved.
        Storage::disk('public')->delete(array_filter($oldFiles));

        return new ProfileResource($model->load(['jobTitle.jobCategory', 'city']));
    }
}
