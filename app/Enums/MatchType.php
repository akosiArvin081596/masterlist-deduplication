<?php

namespace App\Enums;

enum MatchType: string
{
    case Exact = 'exact';
    case Fuzzy = 'fuzzy';
    case Typo = 'typo';
}
