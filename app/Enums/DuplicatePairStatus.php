<?php

namespace App\Enums;

enum DuplicatePairStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Dismissed = 'dismissed';
}
