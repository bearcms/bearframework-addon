<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

final class Localization
{

    static function getDate(int $timestamp): string
    {
        $language = 'en'; //todo
        $date = date('j', $timestamp);
        $month = __('bearcms.date.month_' . date('n', $timestamp));
        $year = date('Y', $timestamp);
        $showYear = $year === date('Y', time());
        if ($language === 'en') {
            return $month . ' ' . $date . ($showYear ? ', ' . $year : '');
        } else {
            return $date . ' ' . $month . ($showYear ? ' ' . $year : '');
        }
    }

    static function getTimeAgo(int $timestamp): string
    {
        $secondsAgo = time() - $timestamp;
        if ($secondsAgo < 60) {
            return __('bearcms.time.a_moment_ago');
        }
        if ($secondsAgo < 60 * 60) {
            return sprintf(__('bearcms.time.minutes_ago'), floor($secondsAgo / 60));
        }
        if ($secondsAgo < 60 * 60 * 24) {
            return sprintf(__('bearcms.time.hours_ago'), floor($secondsAgo / (60 * 60)));
        }
        return self::getDate($timestamp);
    }

}
