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
    const HABITS_SHOW_JOURNALING_HABIT = 8;
    const SHOW_EMPTY_TODO_ITEM = 9;
    const SHOW_EMPTY_ACTION_ITEM = 10;
    const SHOW_EMPTY_AD_HOC_ITEM = 11;
    const SHOW_HOME_ICON = 12;
    const NOTIFICATION_CHANNEL = 13;
    const SHOW_EMPTY_BUCKETLIST_ITEM = 14;
    const SEND_ADDICTION_MILESTONE_NOTIFICATIONS = 15;

    // Settings options
    const HABITS_ROLLING_SEVEN_DAYS = 'rolling_seven_days';
    const HABITS_CURRENT_WEEK = 'current_week';

    const HABITS_SUNDAY = 0;
    const HABITS_MONDAY = 1;

    const DO_NOT_SHOW = 0;
    const TOP_OF_LIST = 1;
    const BOTTOM_OF_LIST = 2;

    const NOTIFICATION_EMAIL = 'email';
    const NOTIFICATION_SMS = 'sms';
    const NOTIFICATION_WEBPUSH = 'webpush';
}