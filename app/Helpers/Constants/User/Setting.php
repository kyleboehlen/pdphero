<?php

namespace App\Helpers\Constants\User;

class Setting
{
    // Settings IDs
    const TODO_MOVE_COMPLETED = 1;
    const TODO_SHOW_COMPLETED_FOR = 2;
    const AFFIRMATIONS_SHOW_READ = 3;
    const HABITS_SHOW_AFFIRMATIONS_HABIT = 4;
    const HABITS_DAYS_TO_DISPLAY = 5;
    const HABITS_START_OF_WEEK = 6;
    const PROFILE_SHOW_RULES = 7;

    // Settings options
    const HABITS_ROLLING_SEVEN_DAYS = 'rolling_seven_days';
    const HABITS_CURRENT_WEEK = 'current_week';

    const HABITS_SUNDAY = 0;
    const HABITS_MONDAY = 1;
}