<?php

namespace App\Enums;

enum Ability: string
{
    case USER_TOKENS_UPDATE = 'user.tokens.update';

    case LOCK_SYNC = 'lock.sync';
    case LOCK_ACTIVATE = 'lock.activate';

    case SHARE_CREATE = 'share.create';

    case ACTIVITY_VIEW = 'activity.view';
}
