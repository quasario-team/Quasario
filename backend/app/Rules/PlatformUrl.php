<?php

namespace App\Rules;

use App\Models\SocialLink;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

// For links.N.url: the address must belong to the platform of the same link.
// Example: platform "facebook" accepts facebook.com and m.facebook.com,
// but not instagram.com and not facebook.com.evil.com. Error code: wrongPlatformUrl.
class PlatformUrl implements ValidationRule, DataAwareRule
{
    private array $data = [];

    // Laravel gives us all the sent data, so we can read the platform next to this url.
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // "links.2.url" -> "links.2.platform"
        $platform = data_get($this->data, str_replace('.url', '.platform', $attribute));

        // A wrong or missing platform is reported by the platform rules, not here.
        if (! is_string($platform) || ! array_key_exists($platform, SocialLink::HOSTS)) {
            return;
        }

        $allowedHosts = SocialLink::HOSTS[$platform];

        // null = any website is allowed (platform "website").
        if ($allowedHosts === null) {
            return;
        }

        $host = is_string($value) ? strtolower((string) parse_url($value, PHP_URL_HOST)) : '';

        foreach ($allowedHosts as $allowed) {
            // The host itself (facebook.com) or one of its sub-domains (m.facebook.com).
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return;
            }
        }

        $fail('wrongPlatformUrl');
    }
}
