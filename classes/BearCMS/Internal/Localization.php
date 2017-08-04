<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class Localization
{

    static function getDate(int $timestamp): string
    {
        $app = App::get();
        $language = $app->localization->getLocale();
        $date = date('j', $timestamp);
        $month = __('bearcms.date.month_' . date('n', $timestamp));
        $year = date('Y', $timestamp);
        $showYear = $year !== date('Y', time());
        if ($language === 'bg') {
            return $date . ' ' . $month . ($showYear ? ' ' . $year : '');
        } else {
            return $month . ' ' . $date . ($showYear ? ', ' . $year : '');
        }
    }

    static function getTimeAgo(int $timestamp): string
    {
        $secondsAgo = time() - $timestamp;
        if ($secondsAgo < 60) {
            return __('bearcms.time.a_moment_ago');
        }
        if ($secondsAgo < 60 * 60) {
            $minutes = floor($secondsAgo / 60);
            return $minutes > 1 ? sprintf(__('bearcms.time.minutes_ago'), $minutes) : __('bearcms.time.minute_ago');
        }
        if ($secondsAgo < 60 * 60 * 24) {
            $hours = floor($secondsAgo / (60 * 60));
            return $hours > 1 ? sprintf(__('bearcms.time.hours_ago'), $hours) : __('bearcms.time.hour_ago');
        }
        return self::getDate($timestamp);
    }

}
