<?php

namespace BookStack\Activity;

use BookStack\Access\SocialAccount;
use BookStack\Access\SocialAuthService;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateSocialUserAvatarJob implements ShouldQueue
{
    protected int $userId;
    public function __construct(int $userId = 0)
    {
        $this->userId = $userId;
    }

    public function handle(UserAvatars $userAvatar, SocialAuthService $socialAuthService)
    {
        if ($this->userId > 0) {
            $socialUsers = SocialAccount::where('user_id', $this->userId)->get();
        } else {
            $socialUsers = SocialAccount::where('custom_avatar', 0)->get();
        }

        foreach ($socialUsers as $socialUser) {
            $socialAvatar = $userAvatar->getSocialAccountAvatar($socialUser->driver, $socialUser->driver_id);

            $user = User::findOrFail($socialUser->user_id);
            try {
                $userAvatar->fetchAndAssignToUser($user, $socialAvatar);
            } catch (\Exception $exception) {
                \Log::error('Social User Photo Update Error : ' . $exception->getMessage());
            }
        }
    }
}
