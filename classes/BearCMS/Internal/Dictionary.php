<?php

/*
 * Bear CMS addon for Bear Framework
 * https://bearcms.com/
 * Copyright (c) 2016 Amplilabs Ltd.
 * Free to use under the MIT license.
 */

namespace BearCMS\Internal;

use BearFramework\App;

final class Dictionary
{

    static function get(string $key): string
    {
        $language = 'en'; //todo
        $data = [];
        $data['en']['month_1'] = 'January';
        $data['en']['month_2'] = 'February';
        $data['en']['month_3'] = 'March';
        $data['en']['month_4'] = 'April';
        $data['en']['month_5'] = 'May';
        $data['en']['month_6'] = 'June';
        $data['en']['month_7'] = 'July';
        $data['en']['month_8'] = 'August';
        $data['en']['month_9'] = 'September';
        $data['en']['month_10'] = 'October';
        $data['en']['month_11'] = 'November';
        $data['en']['month_12'] = 'December';
        $data['en']['a_moment_ago'] = 'a moment ago';
        $data['en']['minutes_ago'] = '%s minutes ago';
        $data['en']['hours_ago'] = '%s hours ago';

        $data['bg']['month_1'] = 'Януари';
        $data['bg']['month_2'] = 'Февруари';
        $data['bg']['month_3'] = 'Март';
        $data['bg']['month_4'] = 'Април';
        $data['bg']['month_5'] = 'Май';
        $data['bg']['month_6'] = 'Юни';
        $data['bg']['month_7'] = 'Юли';
        $data['bg']['month_8'] = 'Август';
        $data['bg']['month_9'] = 'Септември';
        $data['bg']['month_10'] = 'Октомври';
        $data['bg']['month_11'] = 'Ноември';
        $data['bg']['month_12'] = 'Декември';
        $data['bg']['a_moment_ago'] = 'току що';
        $data['bg']['minutes_ago'] = 'преди %s минути';
        $data['bg']['hours_ago'] = 'преди %s часа';

        if (!isset($data[$language])) {
            $language = 'en';
        }
        return isset($data[$language][$key]) ? $data[$language][$key] : '';
    }

}
