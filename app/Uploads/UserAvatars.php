<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Http\HttpRequestService;
use BookStack\Users\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Client\ClientExceptionInterface;

class UserAvatars
{
    public function __construct(
        protected ImageService $imageService,
        protected HttpRequestService $http
    ) {
    }

    /**
     * Fetch and assign an avatar image to the given user.
     */
    public function fetchAndAssignToUser(User $user, string $avatar_url = null): void
    {
        if (!$this->avatarFetchEnabled()) {
            return;
        }

        try {
            $this->destroyAllForUser($user);
            $avatar = $this->saveAvatarImage($user, 500, $avatar_url);
            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image', ['exception' => $e]);
        }
    }

    /**
     * Assign a new avatar image to the given user using the given image data.
     */
    public function assignToUserFromExistingData(User $user, string $imageData, string $extension): void
    {
        try {
            $this->destroyAllForUser($user);
            $avatar = $this->createAvatarImageFromData($user, $imageData, $extension);
            $user->avatar()->associate($avatar);
            $user->save();
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image', ['exception' => $e]);
        }
    }

    /**
     * Destroy all user avatars uploaded to the given user.
     */
    public function destroyAllForUser(User $user): void
    {
        $profileImages = Image::query()->where('type', '=', 'user')
            ->where('uploaded_to', '=', $user->id)
            ->get();

        foreach ($profileImages as $image) {
            $this->imageService->destroy($image);
        }
    }

    /**
     * Save an avatar image from an external service.
     *
     * @throws HttpFetchException
     */
    protected function saveAvatarImage(User $user, int $size = 500, string $avatar_url = null): Image
    {
        $avatarUrl = $avatar_url ? $avatar_url : $this->getAvatarUrl() ;
        $email = strtolower(trim($user->email));

        $replacements = [
            '${hash}'  => md5($email),
            '${size}'  => $size,
            '${email}' => urlencode($email),
        ];

        $userAvatarUrl = strtr($avatarUrl, $replacements);
        $imageData = $this->getAvatarImageData($userAvatarUrl);

        return $this->createAvatarImageFromData($user, $imageData, 'png');
    }

    /**
     * Creates a new image instance and saves it in the system as a new user avatar image.
     */
    protected function createAvatarImageFromData(User $user, string $imageData, string $extension): Image
    {
        $imageName = Str::random(10) . '-avatar.' . $extension;

        $image = $this->imageService->saveNew($imageName, $imageData, 'user', $user->id);
        $image->created_by = $user->id;
        $image->updated_by = $user->id;
        $image->save();

        return $image;
    }

    /**
     * Gets an image from url and returns it as a string of image data.
     *
     * @throws HttpFetchException
     */
    protected function getAvatarImageData(string $url): string
    {
        try {
            $client = $this->http->buildClient(5);
            $response = $client->sendRequest(new Request('GET', $url));
            if ($response->getStatusCode() !== 200) {
                throw new HttpFetchException(trans('errors.cannot_get_image_from_url', ['url' => $url]));
            }

            return (string) $response->getBody();
        } catch (ClientExceptionInterface $exception) {
            throw new HttpFetchException(trans('errors.cannot_get_image_from_url', ['url' => $url]), $exception->getCode(), $exception);
        }
    }

    /**
     * Check if fetching external avatars is enabled.
     */
    public function avatarFetchEnabled(): bool
    {
        $fetchUrl = $this->getAvatarUrl();

        return str_starts_with($fetchUrl, 'http');
    }

    /**
     * Get the URL to fetch avatars from.
     */
    public function getAvatarUrl(): string
    {
        $configOption = config('services.avatar_url');
        if ($configOption === false) {
            return '';
        }

        $url = trim($configOption);

        if (empty($url) && !config('services.disable_services')) {
            $url = 'https://www.gravatar.com/avatar/${hash}?s=${size}&d=identicon';
        }

        return $url;
    }

    public function getSocialAccountAvatar(string $driver, string $driver_id)
    {
        if ($driver === 'google') {
            $response = \Http::get("https://people.googleapis.com/v1/people/{$driver_id}?personFields=names,photos", [
                'personFields' => 'photos',
                'key' => config('services.google.api_key'),
            ]);
            if ($response->ok()) {
                $data = $response->json();
                $avatarUrl = $data['photos'][0]['url'];
                return $avatarUrl;
            } else {
                \Log::info('Social User Error : ' . json_encode($response->json()));
                return null;
            }
        } else if ($driver === 'github') {
            $response = \Http::get("https://api.github.com/user/{$driver_id}");

            if ($response->successful()) {
                $avatarUrl = $response->json()['avatar_url'];
                return $avatarUrl;
            } else {
                \Log::info('Social User Error : ' . json_encode($response->json()));
                return null;
            }
        } else if ($driver === 'gitlab') {
            $response = \Http::get("https://gitlab.com/api/v4/users/{$driver_id}");

            if ($response->successful()) {
                $avatarUrl = $response->json()['avatar_url'];
                return $avatarUrl;
            } else {
                \Log::info('Social User Error : ' . json_encode($response->json()));
                return null;
            }
        } else if ($driver === 'facebook') {
            $accessToken = config('services.facebook.access_token');
            $response = \Http::get("https://graph.facebook.com/{$driver_id}/picture", [
                'access_token' => $accessToken,
                'redirect' => false,
                'type' => 'large', // Options are small, normal, large, square
            ]);

            if ($response->successful()) {
                $avatarUrl = $response->json()['data']['url'];
                return $avatarUrl;
            } else {
                \Log::info('Social User Error : ' . json_encode($response->json()));
                return null;
            }
        }

        return null;
    }
}
