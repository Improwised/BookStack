<?php

namespace BookStack\Activity;

use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetSocialProfilePhotoAsUserAvatarJob implements ShouldQueue
{
    protected User $user;

    protected string $avatarUrl;

    public function __construct(User $user, string $avatarUrl)
    {
        $this->user = $user;
        $this->avatarUrl = $avatarUrl;
    }

    public function handle(UserAvatars $userAvatar)
    {
        $userAvatar->fetchAndAssignToUser($this->user, $this->avatarUrl);
    }
}
