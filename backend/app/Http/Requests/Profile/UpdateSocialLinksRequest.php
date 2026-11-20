<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\ApiFormRequest;
use App\Models\SocialLink;
use App\Rules\PlatformUrl;
use Illuminate\Validation\Rule;

// PUT /me/profiles/{profile}/links : the WHOLE list of links of the profile.
// Example: {"links": [{"platform": "whatsapp", "url": "https://wa.me/963900000000"}]}
// An empty list ({"links": []}) removes all links.
class UpdateSocialLinksRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'links' => ['present', 'array', 'max:' . count(SocialLink::PLATFORMS)],
            // distinct: the same platform cannot come twice.
            'links.*.platform' => ['required', 'string', Rule::in(SocialLink::PLATFORMS), 'distinct'],
            // PlatformUrl: a facebook link must really be a facebook.com address, and so on.
            'links.*.url' => ['required', 'string', 'max:2048', 'url:http,https', new PlatformUrl],
        ];
    }
}
