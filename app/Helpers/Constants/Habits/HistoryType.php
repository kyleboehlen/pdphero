<?php

namespace App\Helpers\Constants\Habits;

class HistoryType
{
    // Seeded types
    const COMPLETED = 1;
    const SKIPPED = 2;
    const MISSED = 3;

    // Non-seeded types, calculated only
    const PARTIAL = 4;
    const TBD = 5;
}