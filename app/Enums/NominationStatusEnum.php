<?php

namespace App\Enums;

enum NominationStatusEnum: string
{
    case CANCELLED = 'cancelled';

    case CLOSED = 'closed';

    case DRAFT = 'draft';

    case PUBLISHED = 'published';

    case SCRUTINISED = 'scrutinised';
}
