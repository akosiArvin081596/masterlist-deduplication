<?php

namespace App\Enums;

enum MasterlistStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Ready = 'ready';
    case Deduplicating = 'deduplicating';
    case Completed = 'completed';
}
